<?php

namespace Tenfef\MYOB;

use Guzzle\Http\Exception\BadResponseException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Exception;
use League\OAuth2\Client\Exception\IDPException as IDPException;

class Provider extends AbstractProvider
{
    public $headers = [
        'x-myobapi-version' => 'v2',
        'Accept' => 'application/json'
    ];    

    public $authorizationHeader = 'Bearer';

    public $base_url = "https://api.myob.com";

    public function getHeaders($token = null)
    {
        $headers = parent::getHeaders($token);
        $headers['x-myobapi-key'] = $this->clientId;        
        return $headers;
    }

    public function urlAuthorize()
    {
        return "https://secure.myob.com/oauth2/account/authorize";
    }

    public function urlAccessToken()
    {
        return "https://secure.myob.com/oauth2/v1/authorize";
    }

    public function urlUserDetails(AccessToken $token)
    {
        throw new \RuntimeException('You are connected, but was no straight forward end point I could find for this :)');
    }

    public function userDetails($response, AccessToken $token)
    {
        return [];
    }

    /**
     * Helper method that can be used to fetch API responses.
     *
     * @param  string      $path
     * @param  AccessToken $token
     * @param  boolean     $as_array
     * @return array|object
     */
    public function getApiResponse($url, $token, $username, $password)
    {        
        $headers = $this->getHeaders($token);

        if ($username) {
            $headers['x-myobapi-cftoken'] = base64_encode($username . ":" . $password);
        }

        try {
            $client = $this->getHttpClient();
            $client->setBaseUrl($this->base_url);

            if ($headers) {                
                $client->setDefaultOption('headers', $headers);
            }

            $request = $client->get($url)->send();
            $response = $request->getBody();
        } catch (BadResponseException $e) {
            // @codeCoverageIgnoreStart
            $response = $e->getResponse()->getBody();            
            $result = $this->prepareResponse($response);
            if (json_last_error())
            {
                throw new \Exception($response);
            }
            if (isset($result['Errors']))
            {
                $error = $result['Errors'][0];
                throw new \Exception($error['Name'] . ": " . $error['Message']);
            }
        }


        return json_decode($response);

    }
}