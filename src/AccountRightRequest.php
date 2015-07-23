<?php

namespace Tenfef\MYOB;

class AccountRightRequest
{       
    function __construct($provider, $token, $username = NULL, $password = NULL)
    {
        $this->token = $token;
        $this->provider = $provider;
        $this->username = $username;
        $this->password = $password;
    }

    function fetch($uri)
    {
        return $this->provider->getApiResponse($uri, $this->token, $this->username, $this->password);
    }

    function fetchWithPagination($uri)
    {
        $result = $this->fetch($uri);
        if (! isset($result->Items))
        {            
            return $result;
        }

        $items = $result->Items;
        if (! empty($result->NextPageLink))
        {            
            $result = $this->fetchWithPagination($result->NextPageLink);
            $items = array_merge($items, $result);
        }
        
        return $items;
    }

    function post($URI, $data)
    {
        return $this->provider->post("/accountright/" . $URI, $data, $this->token, $this->username, $this->password);
    }

    function put($URI, $data)
    {
        return $this->provider->put("/accountright/" . $URI, $data, $this->token, $this->username, $this->password);
    }

    function delete($URI)
    {
        return $this->provider->delete("/accountright/" . $URI, $this->token, $this->username, $this->password);
    }

    function postFullResponse($URI, $data)
    {
        return $this->provider->postFullResponse("/accountright/" . $URI, $data, $this->token, $this->username, $this->password);
    }

    
}