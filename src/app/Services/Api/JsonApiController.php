<?php

namespace Permaxis\Laravel\Core\App\Services\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Permaxis\Laravel\Core\App\Services\Entities\Entity;
use Permaxis\Laravel\Core\App\Services\Utils\UtilsRequest;

class JsonApiController extends Controller
{

    const PAGINATION_REQUIRED = true;

    private $requirePagination = true;

    protected static $rules = [
        'pagination' => [
            'max_per_page' => 100
        ]
    ];
            /**
     */
    public function getModelClass()
    {
        return;
    }

    /**
     */
    public function getResourceClass()
    {
        return;
    }

    /**
     *
     */
    public function getResourceCollectionClass()
    {
        return;
    }

    public function requirePagination()
    {
        return $this->requirePagination;
    }

    public function setRequirePagination($requirePagination)
    {
        $this->requirePagination = $requirePagination;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $input = $request->only([
            'sort',
            'filter',
            'page',
        ]);

        $request->replace($input);

        $modelClass = $this->getModelClass();
        $qb = $modelClass::query();

        //process sorting
        $this->processSorting($qb, $request);

        //process filter
        $this->processFilter($qb, $request);

        //process pagination
        $results = $this->processPagination($qb, $request);

        $resourceCollectionClass = $this->getResourceCollectionClass();
        $entities = (new $resourceCollectionClass($results));


        return $entities;

    }

    /**
     * Create a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->handleRequest($request);

        $modelClass = $this->getModelClass();
        $entity = new $modelClass;

        return $this->save($request, $entity);

    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $entity = $this->getEntity($id);

        $resourceClass = $this->getResourceClass();
        return new $resourceClass($entity);
    }

    /**
     * Update the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->handleRequest($request);

        $entity = $this->getEntity($id);

        return $this->save($request, $entity);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $entity = $this->getEntity($id);

        $entity->delete();

        return response()->json(
            array(
                'title' => 'success',
                'detail' => "Entity $id deleted with success"
            ),
            200);
    }

    /**
     * Fill Model With Parameters from request
     * @param Request $request
     * @param Entity $entity
     */
    public function handleForm(Request $request, &$entity)
    {
        $input = $request->all();

        if (isset($input['data']['attributes']))
        {
            $entity = $entity->fill($input['data']['attributes']);
        }
    }

    /**
     * Process Request
     * @param Request $request
     */
    public function handleRequest(Request &$request)
    {
        $input = $request->all();

        UtilsRequest::trimInput($input);

        $request->replace($input);

    }

    /**
     * Create or Update Resource
     *
     * @param $request
     * @param $entity
     * @return \Illuminate\Http\JsonResponse
     */
    public function save($request, $entity)
    {
        $this->handleForm($request, $entity);

        try
        {
            $result = $entity->save();

            if (!$result)
            {
                $errors = [];

                foreach ($entity->errors()->toArray() as $key => $messages)
                {
                    $errors[] = [
                        'title' => 'Error on field '.$key,
                        'detail' => implode('|',$messages),
                        'status' => 501,
                        'source' => [
                            'pointer' => 'data/attributes/'. $key
                        ],
                        'meta' => [
                            'field' => $key,
                            'messages' => $messages
                        ]
                    ];
                }

                return response()->json(
                    [
                        'errors' => $errors
                    ], 501);
            }

            $resourceClass = $this->getResourceClass();
            return new $resourceClass($entity);
        }

        catch (\Exception $e)
        {
            return $this->getExceptionResponse($e, 500);
        }
    }

    /**
     * Process Sorting
     *
     * @param $qb
     * @param $input
     * @return $this
     */
    public function processSorting(&$qb , Request $request)
    {
        $orders = $this->getOrders($request);

        foreach ($orders as $order)
        {
            $qb->orderBy($order['sort_by'],$order['sort_dir']);
        }

        return $this;
    }

    public function getOrders(Request $request)
    {
        $orders = [];

        $input = $request->all();

        if (!empty($input['sort']))
        {
            $sorts = explode(',',$input['sort']);
            foreach ($sorts as $sort)
            {
                $result = preg_match('/^([\-]{1})?(.*)$/', $sort,$matches);
                if (!empty($matches) && count($matches) == 3)
                {
                    $sort_dir = $matches[1];
                    $sort_dir = (!empty($sort_dir))? 'asc' : 'desc';
                    $sort_by = $matches[2];
                    $orders[] = ['sort_by' => $sort_by, 'sort_dir' => $sort_dir ];
                }
            }


        }

        $orders = (empty($orders))?  [
            [
                'sort_by' => 'id',
                'sort_dir' => 'asc'
            ]
        ] : $orders;

        return $orders;
    }

    /**
     * Process Pagination
     *
     * @param Builder $qb
     * @param $input
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function processPagination(&$qb, Request $request)
    {
        $input = $request->all();
        $pagination = $this->getRules('pagination');
        if (isset($input['page']))
        {
            $page = $input['page'];

            if (isset($page['pagination']) && !$page['pagination'] && !$this->requirePagination())
            {
                $results = $qb->get();
                return $results;
            }

            $page_number = (isset($page['number']))? (int) $page['number'] : 1;
            $per_page = (isset($page['size']))? (int) $page['size'] : $pagination['max_per_page'];
        }
        else
        {
            $page_number = 1;
            $per_page =  $pagination['max_per_page'];
        }


        $results = $qb->paginate($per_page,['*'],'page',$page_number);


        return $results;
    }

    public function acceptFilter(Request &$request, $keys = array())
    {
        $input = $request->all();

        $results = array();

        $results['filter'] = [];

        if (isset($input['filter']))
        {
            foreach ($keys  as $key) {
                if (isset($input['filter'][$key]))
                {
                    $results['filter'][$key] = $input['filter'][$key];
                }
            }

            if (isset($results['filter']))
            {
                $input['filter'] = $results['filter'];

            }
        }

        $request->replace($input);

        return $this;
    }

    /**
     * Process Filter
     *
     * @param Builder $qb
     * @param $input
     * @return $this
     */
    public function processFilter(&$qb, Request &$request)
    {
        $this->acceptFilter($request, $this->getAcceptFilter());

        $input = $request->all();

        if (isset($input['filter']) && !empty($input['filter']))
        {
            $filter = $input['filter'];


            foreach ($filter as $field_name => $value)
            {
                $values = explode(',', $value);

                if (!empty($values) && count($values) > 1)
                {
                    $qb->whereIn($field_name, $values);
                }
                else
                {
                    $operator = '=';
                    preg_match('/([\*]{1})?([^\*]+)([\*]{1})?/',$value, $matches);
                    if (!empty($matches))
                    {
                        if (!empty($matches[1]) && $matches[1] == '*')
                        {
                            $value = '%'.$value;
                            $operator = 'like';

                        }
                        if (!empty($matches[3]) && $matches[3] == '*')
                        {
                            $value .= '%';
                            $operator = 'like';
                        }

                    }

                    $value = str_replace('*','',$value);

                    $qb->where($field_name, $operator, $value);

                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAcceptFilter()
    {
        return ['id'];
    }

    /**
     * Get Specific exception response
     * @param \Exception $e
     * @param $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExceptionResponse(\Exception $e, $status_code)
    {
        $errors = [];
        $errors[] = [
            'title' => 'Exception occurs',
            'status' => $status_code,
            'meta' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]
        ];

        return response()->json(
            [
                'errors' => $errors
            ], $status_code);
    }

    public function getEntity($id)
    {
        $modelClass = $this->getModelClass();
        try {
            if (is_object($id) && $id instanceof $modelClass)
            {
                $entity = $id;
            }
            else
            {
                $entity = $modelClass::findOrFail($id);
            }

            return $entity;
        }
        catch(\Exception $e)
        {
            return $this->getExceptionResponse($e, 500);
        }
    }

    public static function getRules($rule = '')
    {
        $rules = static::$rules;

        if (!empty($rules) && array_key_exists($rule,$rules))
        {
            return $rules[$rule];
        }

        return  $rules;
    }

    /**
     * Update the specified resource with uuid.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateWithUuid(Request $request, $uuid)
    {
        $this->handleRequest($request);

        $entity = $this->getEntityByUuid($uuid);

        return $this->save($request, $entity);
    }

    /**
     * find an entity by uuid
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntityByUuid($uuid)
    {
        $modelClass = $this->getModelClass();
        try {
            if (is_object($uuid) && $uuid instanceof $modelClass)
            {
                $entity = $uuid;
            }
            else
            {
                $entity = $modelClass::query()->where('uuid','=',$uuid)->first();
            }

            return $entity;
        }
        catch(\Exception $e)
        {
            return $this->getExceptionResponse($e, 500);
        }
    }
}
