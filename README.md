# Vend Provider for OAuth 2.0 Client

[![Build Status](https://travis-ci.org/ursuleacv/oauth2-vend.png?branch=master)](https://travis-ci.org/ursuleacv/oauth2-vend)

This package provides Vend OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

This package is compliant with [PSR-1][], [PSR-2][], [PSR-4][], and [PSR-7][]. If you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[PSR-7]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message.md


## Requirements

The following versions of PHP are supported.

* PHP 5.5
* PHP 5.6
* PHP 7.0
* HHVM

## Installation

Add the following to your `composer.json` file.

```json
{
    "require": {
        "ursuleacv/oauth2-vend": "~1.0"
    }
}
```

## Usage

### Authorization Code Flow

```php
session_start();

$provider = new League\OAuth2\Client\Provider\Vend([
    'clientId' => CLIENT_ID,
    'clientSecret' => CLIENT_SECRET,
    'redirectUri' => REDIRECT_URI,
    'storeName' => STORE_NAME,
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([]);
    $_SESSION['oauth2state'] = $provider->getState();
    
    echo '<a href="'.$authUrl.'">Log in with Vend!</a>';
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    echo 'Invalid state.';
    exit;

}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

try {

    // We got an access token, let's make some requests
    $vendApi = $vend->vendApi($token);
    $sale = $vendApi->getSale($vendSaleID);
    $registers = $vendApi->getRegisters();

    echo '<pre>';
    print_r($sale);
    echo '</pre>';

} catch (Exception $e) {
    exit($e->getMessage());
}

echo '<pre>';
// Use this to interact with the API on the client behalf
var_dump($token->getToken());

echo '</pre>';
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/ursuleacv/oauth2-vend/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Valentin Ursuleac](https://github.com/ursuleacv)

## License

The MIT License (MIT). Please see [License File](https://github.com/ursuleacv/oauth2-vend/blob/master/LICENSE) for more information.
