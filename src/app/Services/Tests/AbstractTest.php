<?php
/**
 * Created by Permaxis.
 * User: mk2
 * Date: 19/03/2019
 * Time: 17:16
 */

namespace Permaxis\Core\App\Services\Tests;


use Tests\TestCase;

Abstract class AbstractTest extends TestCase
{
    private $options;

    public function setUp() : void
    {
        $this->setOptions();
        $this->verifyOptions();

        parent::setUp();
    }


    public function setOptions()
    {
        $opts = array();

        if (!empty(getenv('opts')))
        {
            $envs = getenv('opts');
            $envs = preg_replace('/[{}]/','',$envs);

            $envs = explode(',', $envs);


            foreach ($envs as $option)
            {
                // get environnement
                if (strpos($option,'=') !== false)
                {
                    $tmp = preg_split('/=/',$option);
                    if (is_array($tmp) && count($tmp) == 2)
                    {
                        $opts[$tmp[0]] = $tmp[1];
                    }
                }
            }

        }

        $this->options = $opts;

        return $this;

    }

    public function getOptions()
    {
        return $this->options;
    }


    public function verifyOptions()
    {
        $options = $this->options;
    }


}