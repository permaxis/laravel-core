<?php
/**
 * Created by PhpStorm.
 * User: dakin
 * Date: 30/12/2019
 * Time: 12:39
 */

namespace Permaxis\Core\App\Services\Api;

use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository;
use Illuminate\Container\Container;

Trait PersonalAccessToken
{
    /**
     * Get personal access token for user.
     *
     * @return \Laravel\Passport\Token|null
     */
    public function getAccessToken()
    {
        if (session()->has('current_user_access_token'))
    {
        $access_token =session()->get('current_user_access_token');
        return $access_token;
    }
    else
    {
        Passport::personalAccessClientId('0aafc6c2-fe01-4b97-ba88-b094ca504d28');
        $access_token =  $this->createToken('name')->accessToken;
        session('current_user_access_token',$access_token);
    };

        return $access_token;
    }
}