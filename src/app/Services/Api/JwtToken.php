<?php
/**
 * Created by Permaxis.
 * User: abdel
 * Date: 10/05/2020
 * Time: 17:01
 */

namespace Permaxis\LaravelCore\app\Services\Api;


class JwtToken implements JwtTokenInterface
{
    private $claims;

    private $headers;

    /**
     * @return mixed
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * @param mixed $claims
     */
    public function setClaims($claims): void
    {
        $this->claims = $claims;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers): void
    {
        $this->headers = $headers;
    }




}