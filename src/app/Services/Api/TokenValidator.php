<?php
/**
 * Created by PhpStorm.
 * User: dakin
 * Date: 24/02/2020
 * Time: 13:23
 */

namespace Permaxis\Core\App\Services\Api;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use RuntimeException;

class TokenValidator
{
    /**
     * @var string
     */
    protected $publicKey;

    /**
     * {@inheritdoc}
     */
    public function validateAuthorization(Request $request)
    {
        if ($request->hasHeader('authorization') === false) {
            throw TokenValidatorException::accessDenied('Missing "Authorization" header');
        }

        $jwt = $request->bearerToken();

        try {
            // Attempt to parse and validate the JWT
            $token = (new Parser())->parse($jwt);
            try {
                if ($token->verify(new Sha256(), new Key($this->publicKey)) === false) {
                    throw TokenValidatorException::accessDenied('Access token could not be verified');
                }
            } catch (\BadMethodCallException $exception) {
                throw TokenValidatorException::accessDenied('Access token is not signed', null, $exception);
            }

            // Ensure access token hasn't expired
            $data = new ValidationData();
            $data->setCurrentTime(\time());

            if ($token->validate($data) === false) {
                throw TokenValidatorException::accessDenied('Access token is invalid');
            }


            // Return the request with additional attributes
            /*return $request
                ->request->add('oauth_access_token_id', $token->getClaim('jti'))
                ->withAttribute('oauth_client_id', $token->getClaim('aud'))
                ->withAttribute('oauth_user_id', $token->getClaim('sub'))
                ->withAttribute('oauth_scopes', $token->getClaim('scopes'));*/

            return $request;

        } catch (InvalidArgumentException $exception) {
            // JWT couldn't be parsed so return the request as is
            throw TokenValidatorException::accessDenied($exception->getMessage(), null, $exception);
        } catch (RuntimeException $exception) {
            //JWR couldn't be parsed so return the request as is
            throw TokenValidatorException::accessDenied('Error while decoding to JSON', null, $exception);
        }
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }


}