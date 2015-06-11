<?php

namespace Tenfef\MYOB;

class AccountRight 
{       
    function __construct($provider, $token, $username = NULL, $password = NULL)
    {
        $this->token = $token;
        $this->provider = $provider;
        $this->username = $username;
        $this->password = $password;
    }

    function fetch($url)
    {
        return $this->provider->getApiResponse($url, $this->token, $this->username, $this->password);
    }

    function companyFiles()
    {
        return $this->fetch("/accountright");
    }

    function contacts($companyID)
    {
        return $this->fetch("/accountright/" . $companyID . "/Contact");
    }

    function companyDetails($companyID)
    {
        return $this->fetch("/accountright/" . $companyID . "/Company");        
    }
}