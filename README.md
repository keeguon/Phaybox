# Phaybox

Phaybox is a small library wrote in PHP allowing e-commerce website to interact easily with the Paybox API.


## Requirements

* PHP 5.3.x
* Guzzle 2.0.x


## Installation

### composer

To install Phaybox with composer you simply need to create a composer.json in your project root and add:

```json
{
    "require": {
        "friendspray/phaybox"
    }
}
```

Then run

```bash
$ wget -nc http://getcomposer.org/composer.phar
$ php composer.phar install
```

You have now Phaybox installed in vendor/friendspray/phaybox

And an handy autoload file to include in you project in vendor/.composer/autoload.php


## Testing

The library is fully tested with PHPUnit for unit tests. To run tests you need PHPUnit installed on your system.

Go to the base library folder and run the test suites

```bash
$ phpunit
```


## Code style

Phaybox follows the [Symfony2 Coding Standard](https://github.com/opensky/Symfony2-coding-standard) for the most part (we use two spaces for indentation instead of four).
