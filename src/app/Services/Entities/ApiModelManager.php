<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 04/10/2019
 * Time: 12:40
 */

namespace Permaxis\Laravel\Core\App\Services\Entities;


use Carbon\Carbon;
use Permaxis\Laravel\Core\App\Services\Api\RestClient as Client;
use Illuminate\Support\Facades\App;
use Permaxis\Laravel\Core\App\Services\Entities\ModelManager;

trait ApiModelManager
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
    }


    static function client() {

        $client = App::make('api_v1');
        return $client;
    }

    static function baseUrl() {

        return 'entities';
    }

    static function ressourceType() {

        return 'entities';
    }

    /**
     * Begin querying the model.
     *
     * @return Permaxis\Laravel\CrudGenerator\App\Entities\ApiBuilder
     */
    public static function query()
    {
        $apiBuilder = (new static)->newQuery(self::client(), self::class, self::baseUrl());

        return $apiBuilder;
    }

    /**
     * Get a new query builder for the model's api.
     *
     * @return Permaxis\Laravel\CrudGenerator\App\Entities\ApiBuilder
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

        if ($this->id == null)
        {
              self::client()->post(self::baseUrl(),[
                'body' => json_encode($data)
            ]);
        }
        else
        {
            $data['data']['id'] = $this->id;
            self::client()->patch(self::baseUrl().'/'.$this->id,[
                'body' => json_encode($data)
            ]);
        }

        return true;
    }


    public function getAttributes()
    {
        return get_object_vars($this->attributes);
    }

}