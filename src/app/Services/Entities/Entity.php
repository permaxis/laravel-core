<?php

namespace Permaxis\LaravelCore\app\Services\Entities;

use Illuminate\Database\Eloquent\Model;
use Permaxis\LaravelCore\app\Services\Entities\ModelManager;

class Entity extends Model
{
    use ModelManager;

    protected static $rules = array(
        'name' => 'required',
        'enabled' => 'boolean|required'

    );

    protected $enableValidate = array(
        'attributes' => false,
        'model' => true
    );

    /**
     * All The attributes are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function __toString()
    {
        return $this->name;
    }


    public function customValidate($params = array())
    {

        /*if (empty($this->name))
        {
            $this->errors()->add('name','The name field is required');
        }*/

        if (strlen($this->name) <= 1)
        {
            $this->errors()->add('name','The name field length must be > 1');
            return false;
        }

        if ($this->errors()->isNotEmpty())
        {
            return false;
        }

        return true;
    }

    //return true/false in json response
    public function getEnabledAttribute($value)
    {
        return ($value)? true : false;
    }

    public function beforeSave()
    {
        $this->enabled = ($this->enabled)? true : false;

        return $this;
    }

}
