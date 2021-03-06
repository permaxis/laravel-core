<?php
namespace Permaxis\LaravelCore\app\Services\Api;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Created by Permaxis.
 * User: Permaxis
 * Date: 27/12/2019
 * Time: 10:53
 */
class Client
{
    /**
     * @var array
     */
    private $options;

    public function __construct(GuzzleClient $client, $options = array())
    {
        $this->client = $client;
        $this->options = $options;
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

    public function request($uri, $options = array())
    {
        $options = array_merge_recursive($this->options, $options);
        return $this->client->request($uri, $options);
    }

    public function get($uri, $options = array())
    {
        $options = array_merge_recursive($this->options, $options);
        return $this->client->request('GET',$uri, $options);
    }

    public function post($uri, $options = array())
    {
        $options = array_merge_recursive($this->options, $options);
        return $this->client->request('POST',$uri, $options);
    }

    public function put($uri, $options = array())
    {
        $options = array_merge_recursive($this->options, $options);
        return $this->client->request('PUT',$uri, $options);
    }

    public function patch($uri, $options = array())
    {
        $options = array_merge_recursive($this->options, $options);
        return $this->client->request('PATCH',$uri, $options);
    }

    public function delete($uri, $options = array())
    {
        $options = array_merge_recursive($this->options, $options);
        return $this->client->request('DELETE',$uri, $options);
    }
}