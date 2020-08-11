<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 04/10/2019
 * Time: 12:40
 */

namespace Permaxis\Core\App\Services\Entities;


use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Permaxis\Core\App\Services\Api\RestClient as Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\MessageBag;
use Permaxis\Core\App\Services\Entities\ModelManager;
use Permaxis\Oauth2Passport\App\Entities\ApiClient;

Abstract class  AbstractApiModelManager
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var array
     */
    protected static $config_properties =  [
        'base_url' => '',
        'resource_type' => '',
        'api_client_name' => ''
    ];

    /**
    protected static $attributes = [];

    /**
     * @var \Permaxis\Core\App\Services\Api\RestClient
     */
    protected $restClient;

    public function __construct()
    {
       $this->initAttributes();
       $this->errors = new MessageBag();
    }


    public function getRestClient() : \Permaxis\Core\App\Services\Api\RestClient{

        if (empty($this->restClient))
        {
            $name = $this->getMappingApiClientName($this->getApiClientName());
            $client = App::make($name);
            $this->setRestClient($client);
        }

        return $this->restClient;
    }

    public function getBaseUrl()
    {
        return $this::$config_properties['base_url'];
    }

    public function setBaseUrl($base_url)
    {
        $this::$config_properties['base_url'] = $base_url;
    }

    public function getResourceType()
    {

        return $this::$config_properties['resource_type'];
    }

    public function getClass()
    {

        return get_class($this);
    }

    /**
     * Begin querying the model.
     *
     * @return Permaxis\CrudGenerator\App\Entities\ApiBuilder
     */
    public static function query() : ApiBuilder
    {
        $apiBuilder = (new static)->newQuery((new static)->getRestClient(), (new static)->getClass(), (new static)->getBaseUrl());

        return $apiBuilder;
    }

    /**
     * Get a new query builder for the model's api.
     *
     * @return Permaxis\CrudGenerator\App\Entities\ApiBuilder
     */
    public function newQuery(Client $client, $model, $baseUrl)
    {
        return new ApiBuilder($client, $model, $baseUrl);
    }


    public function fill($input = array())
    {
        foreach ($input as $key => $value)
        {
            if (in_array($key, $this::$attributes))
            {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function save(array $options = array())
    {
        $data = array(
            'data' => [
                'type' => $this->getResourceType(),
            ]
        );



        $attributes = array();

        foreach ($this::$attributes as $attribute_name)
        {
            $attributes[$attribute_name] = $this->{$attribute_name};

        }

        unset($attributes['created_at']);
        unset($attributes['updated_at']);

        $data['data']['attributes'] = $attributes;

        try {
            if ($this->id == null)
            {
                $response = $this->getRestClient()->post($this->getBaseUrl(),[
                    'body' => json_encode($data)
                ]);
            }
            else
            {
                $data['data']['id'] = $this->id;
                $response = $this->getRestClient()->patch($this->getBaseUrl().'/'.$this->id,[
                    'body' => json_encode($data)
                ]);
            }
        }

        catch (ServerException $e) {

            $response = $e->getResponse();
            $results = json_decode($response->getBody()->getContents(),true);
            $results = $results['errors'];


            foreach ($results as $result)
            {
                if (isset($result['meta']['field']))
                {
                    $field = $result['meta']['field'];
                    if (isset($result['meta']['messages']) && is_array($result['meta']['messages']))
                    {
                        foreach ($result['meta']['messages'] as $message)
                        {
                            $this->errors->add($field, $message);
                        }
                    }

                }
                else
                {
                    $this->errors->add('transversal',json_encode($result));
                }

            }

           //todo add global errors <hen internal server
            //dd($results);
            /*
             * array:1 [?
  "errors" => array:1 [?
    0 => array:3 [?
      "title" => "Exception occurs"
      "status" => 500
      "meta" => array:2 [?
        "code" => "23000"
        "message" => "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`dev_core`.`user_client_target_roles`, CONSTRAINT `user_client_target_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)) (SQL: update `user_client_target_roles` set `user_id` = 3, `user_client_target_roles`.`updated_at` = 2020-03-06 11:03:05 where `id` = 3) ?"
      ]
    ]
  ]
]
             */

            return false;

        }

        return true;
    }


    public function delete()
    {
        $this->getRestClient()->delete($this->getBaseUrl().'/'.$this->id);

        return true;
    }

    public function setRestClient(Client $client)
    {
        $this->restClient = $client;
    }

    public function hydrateModel($resource)
    {
        $this::hydrateThisModel($this, $resource);
        return $this;
    }


    public static function hydrateThisModel(&$model, $resource)
    {
        foreach ($model::$attributes as $attributes_name)
        {
            if ($attributes_name == 'created_at')
            {
                $model->created_at = Carbon::parse($resource['attributes']['created_at']);
            }
            elseif ($attributes_name == 'updated_at')
            {
                $model->updated_at = Carbon::parse($resource['attributes']['updated_at']);
            }
            elseif ($attributes_name == 'id')
            {
                $model->id = $resource['attributes']['id'];
            }
            else
            {
                if (array_key_exists($attributes_name, $resource['attributes']))
                {
                    $model->{$attributes_name} = $resource['attributes'][$attributes_name];
                }
            }
        }

        return $model;
    }

    public function initAttributes()
    {
        foreach ($this::$attributes as $attribute_name)
        {
            $this->{$attribute_name} = null;
        }
        return $this;
    }


    public function getMappingApiClientName($name)
    {
        $mapping =  config('permaxis_apiclient.mapping.'.$name);
        if (!empty($mapping))
        {
            return $mapping;
        }
        return $name;
    }

    public function getApiClientName()
    {
        return $this::$config_properties['api_client_name'];
    }

    public function getFullBaseUrl()
    {
        $options = $this->getRestClient()->getOptions();

        if (isset($options['base_uri']))
        {
            return $options['base_uri'].$this->getBaseUrl();
        }

        return null;
    }


}