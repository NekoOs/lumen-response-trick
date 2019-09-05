# Lumen Response Customized

This library presents an alternative for replacing the default classes used by Lumen

* Illuminate\Http\Response
* Illuminate\Http\JsonResponse;
* Symfony\Component\HttpFoundation\BinaryFileResponse;
* Symfony\Component\HttpFoundation\StreamedResponse;

## Installation

```shell script
composer require nekoos/lumen-response-trick
```

## Usage

### Basic use

Suppose you have created a custom class for common responses

```php
use Illuminate\Http\Response;

class MyOverrideResponse extends Response { ... }
```

and one for the json answers

```php
use Illuminate\Http\JsonResponse;

class MyOverrideJsonResponse extends Response { ... }
```

Now you can add these lines to your initial load file

```php
# path: bootstrap/app

use NekoOs\Override\Laravel\Lumen\Http\ResponseFactory;

ResponseFactory::use(MyOverrideResponse::class);
ResponseFactory::use(MyOverrideJsonResponse::class);
```

This are expected results

```php
response('common messaje')              # return instance of MyOverrideResponse
response()->json('common messaje')      # return instance of MyOverrideJsonResponse
```

## Customized use

You could even use response instances according to specific conditions

```php
# path: bootstrap/app

use NekoOs\Override\Laravel\Lumen\Http\ResponseFactory;

ResponseFactory::use(function (...$arguments) { ... }, Illuminate\Http\Response);
```

or even register a service provider

```php
# path: app/Providers/MyResponseServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\ResponseFactory;
use MyOverrideResponse;

class MyResponseServiceProvider extends ServiceProvider
{
    /**
     * register()
     */
    public function register()
    {
        $view = $this->app->make('view');
        $this->app->singleton(ResponseFactory::class, function () use ($view) {
            return new MyOverrideResponse($view);
        });
    }
}
```
