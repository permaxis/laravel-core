<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 04/10/2019
 * Time: 12:40
 */

namespace Permaxis\LaravelCore\app\Services\Entities;


use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Permaxis\LaravelCore\app\Services\Api\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\MessageBag;
use Permaxis\LaravelCore\app\Services\Entities\ModelManager;
use Permaxis\Oauth2Passport\App\Entities\ApiClient;

Abstract class  AbstractApiModelManagerOld
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var array
     */
    public $attributes;

    public function __construct()
    {
       $this->attributes  = new \stdClass();
       $this->initAttributes();
       $this->errors = new MessageBag();
    }


    public function getClient() : Client {

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
     * @return Permaxis\LaravelCrudGenerator\app\Entities\ApiBuilder
     */
    public static function query() : ApiBuilder
    {
        $apiBuilder = (new static)->newQuery((new static)->getClient(), (new static)->getClass(), (new static)->getBaseUrl());

        return $apiBuilder;
    }

    /**
     * Get a new query builder for the model's api.
     *
     * @return Permaxis\LaravelCrudGenerator\app\Entities\ApiBuilder
     */
    public function newQuery(Client $client, $model, $baseUrl)
    {
        return new ApiBuilder($client, $model, $baseUrl);
    }


    public function fill($input = array())
    {
        foreach ($input as $key => $value)
        {
            if (property_exists($this->attributes, $key))
            {
                $this->attributes->{$key} = $value;
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

        foreach ($this->getAttributes() as $name => $value)
        {
            $attributes[$name] = $value;
        }


        unset($attributes['created_at']);
        unset($attributes['updated_at']);

        $data['data']['attributes'] = $attributes;

        try {
            if ($this->id == null)
            {
                $response = $this->getClient()->post($this->getBaseUrl(),[
                    'body' => json_encode($data)
                ]);
            }
            else
            {
                $data['data']['id'] = $this->id;
                $response = $this->getClient()->patch($this->getBaseUrl().'/'.$this->id,[
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


    public function getAttributes()
    {
        return get_object_vars($this->attributes);
    }


    public function delete()
    {
        $this->getClient()->delete($this->getBaseUrl().'/'.$this->id);

        return true;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function hydrateModel($resource)
    {
        self::hydrateThisModel($this, $resource);
        return $this;
    }


    public static function hydrateThisModel(&$model, $resource)
    {
        $attributes  =  get_object_vars($model->attributes);
        foreach ($attributes as $key => $value)
        {
            if ($key == 'created_at')
            {
                $model->attributes->created_at = Carbon::parse($resource->attributes->created_at);
            }
            elseif ($key == 'updated_at')
            {
                $model->attributes->updated_at = Carbon::parse($resource->attributes->updated_at);
            }
            elseif ($key == 'id')
            {
                $model->id = $resource->attributes->id;
                $model->attributes->id = $resource->attributes->id;
            }
            else
            {
                if (property_exists($model->attributes, $key) && property_exists($resource->attributes, $key))
                {
                    $model->attributes->$key = $resource->attributes->$key;
                }
            }
        }

        return $model;
    }

    public function initAttributes()
    {
        return $this;
    }

}