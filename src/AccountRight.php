<?php

namespace Tenfef\MYOB;

class AccountRight 
{       
    function __construct($provider, $token)
    {
        $this->token = $token;
        $this->provider = $provider;
    }

    private function fetch($url)
    {
        return $this->provider->getApiResponse("/accountright", $this->token);
    }

    function companyFiles()
    {
        return $this->fetch("/accountright");
    }
}