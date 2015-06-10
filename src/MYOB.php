<?php

namespace Tenfef\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class MYOB extends AbstractProvider
{
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
}
