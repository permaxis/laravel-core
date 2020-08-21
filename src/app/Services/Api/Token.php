<?php
/**
 * Created by Permaxis.
 * User: Permaxis
 * Date: 13/03/2020
 * Time: 15:05
 */

namespace Permaxis\Laravel\Core\App\Services\Api;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;

class Token
{
     /**
     * @var string
     */
    private $tokenType;

    /**
     * @var integer
     */
    private $expiresIn;

    /**
     * @var
     */
    private $accessToken;

    /**
     * @var
     */
    private $jwtToken;

    /**
     * @var
     */
    private $refreshToken;

    public function __construct($expireIn = null, $accessToken = null, $refreshToken = null)
    {
        $this->expiresIn = $expireIn;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @param string $tokenType
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresIn
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }


    /**
     * @return mixed
     */
    public function getJwtToken()
    {
        return $this->jwtToken;
    }

    /**
     * @param mixed $jwtToken
     */
    public function setJwtToken($jwtToken)
    {
        $this->jwtToken = $jwtToken;
    }

}