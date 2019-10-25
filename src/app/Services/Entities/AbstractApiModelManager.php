<?php
/**
 * Created by PhpStorm.
 * User: abdel
 * Date: 04/10/2019
 * Time: 12:40
 */

namespace Permaxis\Core\App\Services\Entities;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\MessageBag;
use Permaxis\Core\App\Services\Entities\ModelManager;

Abstract class  AbstractApiModelManager
{
    /**
     * @var string
     */
    public $resourceType;

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
       $this->resourceType = self::ressourceType();
       $this->attributes  = new \stdClass();
       $this->initAttributes();
       $this->errors = new MessageBag();
    }


    public function client() {

        $client = App::make('api_v1');
        return $client;
    }

    public function baseUrl() {

        return 'entities';
    }

    public function ressourceType() {

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
    public static function query()
    {
        $apiBuilder = (new static)->newQuery((new static)->client(), (new static)->getClass(), (new static)->baseUrl());

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
                'type' => $this->resourceType,
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
                $response = self::client()->post(self::baseUrl(),[
                    'body' => json_encode($data)
                ]);
            }
            else
            {
                $data['data']['id'] = $this->id;
                $response = self::client()->patch(self::baseUrl().'/'.$this->id,[
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
        self::client()->delete(self::baseUrl().'/'.$this->id);

        return true;
    }
}