<?php
namespace Permaxis\LaravelCore\App\Services\Entities;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

/**
 * Created by Permaxis.
 * User: permaxis
 * Date: 04/01/2019
 * Time: 10:50
 */
trait TraitEntityManager
{
    protected $errors;

    /**
     * @var bool
     */
    protected $valid = false;

    public function doValidate($data = array())
    {
        $result = true;

        // make a new validator object
        $v = Validator::make($data, self::$rules);


        // check for failure
        if ($v->fails())
        {
            // set errors and return false
            $this->errors = $this->errors->merge($v->errors());
            $result = false;
        }

        // validation pass
        return $result;
    }

    public function validateAttributes()
    {
        $result = true;
        if (isset($this->enableValidate)
            && isset($this->enableValidate['attributes'])
            && $this->enableValidate['attributes'])
        {
            $result =  $this->doValidate($this->getAttributes());
        }

        return $result;
    }

    public function validateModel()
    {
        $result = true;

        if (isset($this->enableValidate)
            && isset($this->enableValidate['model'])
            && $this->enableValidate['model'])
        {
            $result =  $this->doValidate($this->toArray());
        }

        return $result;
    }

    public function errors()
    {
        if (!$this->errors)
        {
            return new MessageBag();
        }
        return $this->errors;
    }

    public function saveTo(array $options = array())
    {
        $this->beforeSave();

        $result = $this->save($options);

        $this->afterSave();

        return $result;

    }

    public function customValidate($params = array())
    {
        return true;
    }

    public function beforeSave()
    {
        return $this;
    }

    public function afterSave()
    {
        return $this;
    }

    public function validate($params = array())
    {
        $this->errors = new MessageBag();

        $result = $this->validateModel();

        $resultValidateAttributes = $this->validateAttributes();

        $result = $result && $resultValidateAttributes;

        $resultCustomValidate = $this->customValidate($params);

        $result = $result && $resultCustomValidate;

        return $result;
    }

    public static function getRules()
    {
        return self::$rules;
    }


    public static function getTableName()
    {
        return with(new static)->getTable();
    }




}