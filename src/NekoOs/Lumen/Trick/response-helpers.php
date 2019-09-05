<?php

use Laravel\Lumen\Http\ResponseFactory;

if (function_exists('response')) {
    if (!class_exists('Laravel\Lumen\Http\ResponseFactory', false)) {
        spl_autoload_register(function ($class) {
            if ($class == 'Laravel\Lumen\Http\ResponseFactory') {
                require_once __DIR__ . "/../../Override/Laravel/Lumen/Http/ResponseFactory.php";
                /** @noinspection PhpIgnoredClassAliasDeclaration */
                return class_alias(NekoOs\Override\Laravel\Lumen\Http\ResponseFactory::class, 'Laravel\Lumen\Http\ResponseFactory');
            }
        }, true, true);
    }
} else {
    /**
     * Return a new response from the application.
     *
     * @param string $content
     * @param int    $status
     * @param array  $headers
     * @param array  $options
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    function response($content = '', $status = 200, array $headers = [], array $options = [])
    {
        /** @var ResponseFactory $factory */
        $factory = app(ResponseFactory::class);
        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->make($content, $status, $headers, $options);
    }
}