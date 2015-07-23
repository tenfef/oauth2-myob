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

    public $base_url = "https://api.myob.com/";

    public function getHeaders($token = NULL, $username = NULL, $password = NULL)
    {
        $headers = parent::getHeaders($token);
        $headers['x-myobapi-key'] = $this->clientId;        
        if ($username) {
            $headers['x-myobapi-cftoken'] = base64_encode($username . ":" . $password);            
        }        
        return $headers;
    }

    private function guzzleClient($token, $username, $password)
    {
        $headers = $this->getHeaders($token, $username, $password);

        $client = $this->getHttpClient();
        $client->setBaseUrl($this->base_url);

        if ($headers) {                
            $client->setDefaultOption('headers', $headers);
        }

        return $client;
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

    private function handle_exception($e)
    {
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
            $message = $error['Name'] . ": " . $error['Message'];
            if (! empty($error['AdditionalDetails']))
            {
                $message .= " " . $error['AdditionalDetails'];
            }
            
            throw new \Exception($message);
        }

        throw $e;
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
        try {        
            $client = $this->guzzleClient($token, $username, $password);
            $request = $client->get($url)->send();
            $response = $request->getBody();
        } catch (BadResponseException $e) {            
            $response = $this->handle_exception($e);
        }

        return json_decode($response);
    }    

    public function postFullResponse($URI, $data, $token, $username, $password)
    {
        try {                    
            $client = $this->guzzleClient($token, $username, $password);
            $options = [
                'content-type' => 'application/json'
            ];
            $request = $client->post($URI, $options)->setBody(json_encode($data))->send();            
            
        } catch (BadResponseException $e) {            
            $responseBody = $this->handle_exception($e);
        }

        return $request;
    }

    public function post($URI, $data, $token, $username, $password)
    {        
        $response = $this->postFullResponse($URI, $data, $token, $username, $password);

        return json_decode($response->getBody());
    }

    public function putFullResponse($URI, $data, $token, $username, $password)
    {
        try {                    
            $client = $this->guzzleClient($token, $username, $password);
            $options = [
                'content-type' => 'application/json'
            ];
            $request = $client->put($URI, $options)->setBody(json_encode($data))->send();            
            
        } catch (BadResponseException $e) {            
            $responseBody = $this->handle_exception($e);
        }

        return $request;
    }

    public function put($URI, $data, $token, $username, $password)
    {        
        $response = $this->putFullResponse($URI, $data, $token, $username, $password);

        return json_decode($response->getBody());
    }

    public function deleteFullResponse($URI, $token, $username, $password)
    {
        try {                    
            $client = $this->guzzleClient($token, $username, $password);
            $options = [
                'content-type' => 'application/json'
            ];
            $request = $client->delete($URI, $options)->send();
            
        } catch (BadResponseException $e) {            
            $responseBody = $this->handle_exception($e);
        }

        return $request;
    }

    public function delete($URI, $token, $username, $password)
    {        
        $response = $this->deleteFullResponse($URI, $token, $username, $password);

        return json_decode($response->getBody());
    }

}