<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 10/05/2020
 * Time: 17:04
 */

namespace Permaxis\LaravelCore\app\Services\Api;


interface JwtTokenInterface
{
    /**
     * @return mixed
     */
    //public function getPayload();

    /**
     * @param mixed $payload
     */
    //public function setPayload($payload): void;

    /**
     * @return mixed
     */

    /**
     * @return mixed
     */
    public function getHeaders();

    /**
     * @param $headers
     */
    public function setHeaders($headers): void;

    /**
     * @return mixed
     */
    public function getClaims();

    /**
     * @param mixed $claims
     */
    public function setClaims($claims): void;

}