<?php
/**
 * Created by PhpStorm.
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

    protected static $attributes_names = [];

    public function __construct()
    {
       $this->initAttributes();
       $this->errors = new MessageBag();
    }


    public function getRestClient() : Client {

        $client = App::make('api_v1');
        return $client;
    }

    public function getBaseUrl() {

        return 'entities';
    }

    public function getResourceType() {

        return 'entities';
    }

    public function getClass() {

        return self::class;
    }

    /**
     * Begin querying the model.
     *
     * @return Permaxis\Frontscaffold\App\Entities\ApiBuilder
     */
    public static function query() : ApiBuilder
    {
        $apiBuilder = (new static)->newQuery((new static)->getRestClient(), (new static)->getClass(), (new static)->getBaseUrl());

        return $apiBuilder;
    }

    /**
     * Get a new query builder for the model's api.
     *
     * @return Permaxis\Frontscaffold\App\Entities\ApiBuilder
     */
    public function newQuery(Client $client, $model, $baseUrl)
    {
        return new ApiBuilder($client, $model, $baseUrl);
    }


    public function fill($input = array())
    {
        foreach ($input as $key => $value)
        {
            if (in_array($key, $this::$attributes_names))
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

        foreach ($this::$attributes_names as $attribute_name)
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
            }


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

        foreach ($model::$attributes_names as $attributes_name)
        {
            if ($attributes_name == 'created_at')
            {
                $model->created_at = Carbon::parse($resource->attributes->created_at);
            }
            elseif ($attributes_name == 'updated_at')
            {
                $model->updated_at = Carbon::parse($resource->attributes->updated_at);
            }
            elseif ($attributes_name == 'id')
            {
                $model->id = $resource->attributes->id;
            }
            else
            {
                if (property_exists($resource->attributes, $attributes_name))
                {
                    $model->{$attributes_name} = $resource->attributes->{$attributes_name};
                }
            }
        }

        return $model;
    }

    public function initAttributes()
    {
        foreach ($this::$attributes_names as $attribute_name)
        {
            $this->{$attribute_name} = null;
        }
        return $this;
    }

}