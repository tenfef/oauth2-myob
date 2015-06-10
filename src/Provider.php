<?php

namespace Tenfef\MYOB;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

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
    public function getApiResponse($url, $token, $as_array = true)
    {        
        $headers = $this->getHeaders($token);
        
        $result = $this->fetchProviderData($this->base_url . $url, $headers);
        return json_decode($result);

    }
}