# MYOB Provider for OAuth 2.0 Client

This package provides MYOB support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require 'tenfef/oauth2-myob:dev-master'
```

## Usage

Usage is the same as The League's OAuth client, using `Wheniwork\OAuth2\Client\Provider\MYOB` as the provider.

### Authorization Code Flow

```php
$provider = new Wheniwork\OAuth2\Client\Provider\MYOB([
    'clientId'     => '{myob-client-id}',
    'clientSecret' => '{myob-client-secret}',
    'domainPrefix' => '{myob-domain-prefix}',
    'redirectUri'  => 'https://example.com/callback-url'
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->state;
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Use this to interact with an API on the users behalf
    echo $token->accessToken;
}
```

## Refreshing a Token

```php
$provider = new Wheniwork\OAuth2\Client\Provider\MYOB([
    'clientId'     => '{myob-client-id}',
    'clientSecret' => '{myob-client-secret}',
    'domainPrefix' => '{myob-domain-prefix}',
    'redirectUri'  => 'https://example.com/callback-url'
]);

$grant = new \League\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
```

Hat tip to the Vend OAuth Provider which gave a good template for this Provider.
https://github.com/wheniwork/oauth2-vend
