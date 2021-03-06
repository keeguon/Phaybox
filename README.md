# Phaybox

Phaybox is a small library wrote in PHP allowing e-commerce website to interact easily with the Paybox API.


## Requirements

* PHP 5.3.x


## Installation

### Using composer

To install Phaybox with composer you simply need to create a composer.json in your project root and add:

```
{
    "require": {
        "keeguon/phaybox"
    }
}
```

Then run

```
$ wget --quiet http://getcomposer.org/composer.phar
$ php composer.phar install --install-suggest
```

You have now Phaybox installed in vendor/friendspray/phaybox

And an handy autoload file to include in you project in vendor/.composer/autoload.php

### Using git

To install Phaybox using git you just have to run the following command:

```
$ git clone https://github.com/Keeguon/Phaybox.git --recursive
```


## Testing

The library is fully tested with PHPUnit for unit tests. To run tests you need PHPUnit installed on your system.

Go to the base library folder and run the test suites

```
$ phpunit
```


## Code style

* Phaybox follows most of the [Symfony2 Coding Standard](https://github.com/opensky/Symfony2-coding-standard)


## How to use

```
<?php

use Phaybox\Client;
$client = new Client('your_client_id', 'your_client_secret', 'your_client_rang', 'your_client_site', array(
    'algorithm'     => '' // Optional, see hash_algos(), defaults to 'sha512'
  , 'callback'      => '' // Optional, see PBX_RETOUR in the Paybox documentation, defaults to 'Amt:M;Ref:R;Auth:A;Err:E'
  , 'path_prefix'   => '' // Optional, the path which prefix the different phases for HTTP transactions, defaults to 'paybox'
  , 'request_path'  => '' // Optional, the path pointing to the request phase
  , 'callback_path' => '' // Optional, the path pointing to the callback phase
));
$transaction = $client->getTransaction(array(
    'PBX_TOTAL'   => 0000          // required
  , 'PBX_DEVISE'  => 978           // required
  , 'PBX_CMD'     => 'test'        // required
  , 'PBX_PORTEUR' => 'me@mail.com' // required
  , // optional params...
));
$formFields = $transaction->getFormattedParams();
```

Using the snippets above should provide you the fields you need in your form (you would still have to url encode some of them according to the Paybox documentation).

Optional fields will be located at the end but before the HMAC signature and they should be in the same order as you entered them in your form (see 4.3 in Paybox documentation).

For more in-depth informations there's an exhaustive example in the the examples folder.
