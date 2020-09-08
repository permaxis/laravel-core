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
trait ModelManager
{
    protected $errors;

    //protected static $rules = array();

    protected $enableValidate = array(
        'attributes' => false,
        'model' => true
    );

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

        if (!isset($options['validate']) || (isset($options['validate']) && $options['validate']))
        {
            $validate = $this->validate($options);
            if ($validate)
            {
                $result = $this->beforeSave($options);
                $result = $result && parent::save($options);
                $result = $result && $this->afterSave($options);
                return $result;
            }
            else
            {
                return $validate;
            }
        }

        $result = $this->beforeSave($options);
        $result = $result && $this->save($options);
        $result = $result && $this->afterSave($options);
        return $result;

    }

    public function customValidate($params = array())
    {
        return true;
    }

    public function beforeSave()
    {
        return true;
    }

    public function afterSave()
    {
        return true;
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


    public function save(array $options = array())
    {
        if (!isset($options['validate']) || (isset($options['validate']) && $options['validate']))
        {
            $result = $this->beforeValidate($options);
            $validate = $result && $this->validate($options);
            $validate = $validate && $this->AfterValidate($options);
            if ($validate)
            {
                $result = $this->beforeSave($options);
                $result = $result && parent::save($options);
                $result = $result && $this->afterSave($options);
                return $result;
            }
            else
            {
                return $validate;
            }
        }

        $result = $this->beforeSave($options);
        $result = $result && parent::save($options);
        $result = $result && $this->afterSave($options);
        return $result;
    }


    public function beforeValidate($options = array())
    {
        return true;
    }

    public function afterValidate($options = array())
    {
        return true;
    }

    public static function getPrefixTable()
    {
        if (property_exists(self::class, 'prefix_table'))
        {
            return (config()->has(self::$prefix_table))? config(self::$prefix_table) : self::$prefix_table;
        }
        else
        {
            return '';

        }
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        //bug !
        $tablename = str_replace($this::getPrefixTable(),'',parent::getTable());

        $tablename = $this::getPrefixTable().$tablename;

        return $tablename;
    }
}