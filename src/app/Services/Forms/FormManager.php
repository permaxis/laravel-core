<?php
namespace Permaxis\Laravel\Core\App\Services\Forms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

/**
 * Created by Permaxis.
 * User: Permaxis
 * Date: 04/01/2019
 * Time: 10:50
 */
trait FormManager
{
    public function handleRequest(Request &$request)
    {

        $input = $request->all();

        array_walk_recursive($input, function(&$value) {

            if ($value !== '0' && empty($value))
            {
                $value = null;
            }
            else
            {
                $value = trim($value);
            }
        });

        $request->replace($input);

        return;
    }

    public function handleForm(Request &$request, &$entity)
    {

        $this->handleRequest($request);

        $input = $request->all();

        $entity = $entity->fill($input);

        return;

    }

}