<?php
namespace Permaxis\LaravelCore\App\Services\Api;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Created by Permaxis.
 * User: Permaxis
 * Date: 27/12/2019
 * Time: 10:53
 */
class RestClient
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var RestClient
     */
    private $oauthRestClient;

    public function __construct(GuzzleClient $client, $options = array(), RestClient $oauthRestClient = null)
    {
        $this->client = $client;
        $this->options = $options;
        $this->oauthRestClient = $oauthRestClient;
    }

    public function addOption($key, $option)
    {
        $option = array_merge_recursive($this->options[$key], $option);
        $this->options[$key] = $option;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function request($method, $uri, $options = array())
    {
        $this->processAuthorizationToken();
        $options = array_merge_recursive($this->options, $options);

        return $this->client->request($method,$uri, $options);
    }

    public function get($uri, $options = array())
    {
        $this->processAuthorizationToken();
        $options = array_merge_recursive($this->options, $options);

        return $this->client->request('GET',$uri, $options);
    }

    public function post($uri, $options = array())
    {
        $this->processAuthorizationToken();
        $options = array_merge_recursive($this->options, $options);
        return $this->client->request('POST',$uri, $options);
    }

    public function put($uri, $options = array())
    {
        $this->processAuthorizationToken();
        $options = array_merge_recursive($this->options, $options);

        return $this->client->request('PUT',$uri, $options);
    }

    public function patch($uri, $options = array())
    {
        $this->processAuthorizationToken();
        $options = array_merge_recursive($this->options, $options);

        return $this->client->request('PATCH',$uri, $options);
    }

    public function delete($uri, $options = array())
    {
        $this->processAuthorizationToken();
        $options = array_merge_recursive($this->options, $options);

        return $this->client->request('DELETE',$uri, $options);
    }

    /**
     * @return RestClient
     */
    public function getOauthRestClient()
    {
        return $this->oauthRestClient;
    }

    /**
     * @param RestClient $oauthRestClient
     */
    public function setOauthRestClient($oauthRestClient)
    {
        $this->oauthRestClient = $oauthRestClient;
    }


    private function getToken()
    {
        try
        {
            $response  = $this->oauthRestClient->post('token');

            $token =  $response->getBody()->getContents();

            return json_decode($token, true);

        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
            return null;
        }
    }


    private function processAuthorizationToken()
    {
        if ($this->oauthRestClient)
        {
            $token = $this->getToken();

            if ($token && !empty($token) && !empty($token['access_token']))
            {
                $this->options['headers']['Authorization'] =  'Bearer ' . $token['access_token'];
            }
        }

    }
}