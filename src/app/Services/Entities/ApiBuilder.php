<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 27/09/2019
 * Time: 16:38
 */

namespace Permaxis\Laravel\Core\App\Services\Entities;


use Permaxis\Laravel\Core\App\Services\Api\ApiClient;
use Permaxis\Laravel\Core\App\Services\Api\RestClient as Client;

class ApiBuilder
{
    /**
     * @var array
     */
    private $queryString;

    /**
     * @var bool
     */
    private $doPaginate;

    /*
     * @var string
     */
    private $uri;

    public function __construct(Client $client, $model, $baseUrl )
    {
        $this->client = $client;
        $this->model = $model;
        $this->baseUrl = $baseUrl;
        $this->queryString = [];
        $this->doPaginate = true;
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param  string  $column
     * @param  string  $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orders = [
            'column' => $column,
            'direction' => strtolower($direction) == 'asc' ? '-' : '',
        ];

        return $this;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function getClient() : Client
    {
        return $this->client;
    }

    public function all()
    {
        $this->uri = $this->baseUrl;

        return $this;
    }


    public function find($id, $container = false)
    {
        $id = (string) $id;

        $api_request = $this->client->get($this->baseUrl.'/'.$id);

        $entity = json_decode($api_request->getBody(),true);

        $newEntity = new $this->model;

            $newEntity->hydrateModel($entity['data']);

            if ($container)
            {
                $entity['data'] = $newEntity;
                return $entity;
        }

        return $newEntity;

    }


    public function get()
    {
        $query = array();

        if (!empty($this->orders['column']))
        {
            $query['sort']= $this->orders['column'];
        }

        if (!empty($this->orders['direction'])
            && !empty($this->orders['column']))
        {
            $query['sort'] = $this->orders['direction'].$query['sort'];
        }

        if (!empty($this->orders['direction'])
            && !empty($this->orders['column']))
        {
            $query['sort'] = $this->orders['direction'].$this->orders['column'];
        }

        if (!$this->doPaginate)
        {
            $query['page[pagination]'] = 0;
        }

        if (!empty($this->pagination['page'])
            && !empty($this->pagination['per_page']))
        {
            $query['page[number]'] = $this->pagination['page'];
            $query['page[size]'] = $this->pagination['per_page'];
        }

        $query = array_merge($query, $this->queryString);

        $api_request = $this->client->get($this->baseUrl ,[
            'query' => $query
        ]);

        $entities = json_decode($api_request->getBody()->getContents(), true);

        foreach ($entities['data'] as $key => $entity)
        {
            $newEntity = new $this->model;

            $newEntity->hydrateModel($entity);

            $entities['data'][$key] = $newEntity;
        }

        $entities['data'] = collect($entities['data']);

        return $entities;

    }

    public function paginate($page, $perPage)
    {
        $this->pagination = [
            'page' => $page,
            'per_page' => $perPage
        ];

        return $this;
    }

    public function doPaginate($doPaginate = true)
    {
        $this->doPaginate = $doPaginate;

        return $this;
    }

    public function where($field_name, $operator , $value)
    {
        switch ($operator)
        {
            case('='):
                break;
            case('like'):
                preg_match('/([%]{1})?([^%]+)([%]{1})?/',$value, $matches);
                if (!empty($matches))
                {
                    if (!empty($matches[1]) && $matches[1] == '%')
                    {
                        $value = '*'.$value;

                    }
                    if (!empty($matches[3]) && $matches[3] == '%')
                    {
                        $value .= '*';
                    }
                }
                $value = str_replace('%','',$value);
                break;
        }

        $this->queryString["filter[$field_name]"] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        if (strpos($uri, "/") === 0)
        {
            $this->baseUrl = $uri;
        }
        else
        {
            $this->baseUrl .= '/'. $uri;
        }


        return $this;
    }

}