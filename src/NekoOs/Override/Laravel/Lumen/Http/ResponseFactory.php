<?php

namespace NekoOs\Override\Laravel\Lumen\Http;

use Closure;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseFactory
{
    use Macroable;

    /**
     * @var array
     */
    private static $facade = [];

    /**
     * @param string|Closure $concreted
     * @param string|null    $abstract
     */
    public static function use($concreted, string $abstract = null)
    {
        if (is_a($abstract, JsonResponse::class)) {
            $abstract = JsonResponse::class;
        } elseif (is_a($abstract, StreamedResponse::class)) {
            $abstract = StreamedResponse::class;
        } elseif (is_a($abstract, BinaryFileResponse::class)) {
            $abstract = BinaryFileResponse::class;
        } elseif (is_a($abstract, Response::class)) {
            $abstract = Response::class;
        } elseif (is_subclass_of($concreted, JsonResponse::class)) {
            $abstract = JsonResponse::class;
        } elseif (is_subclass_of($concreted, StreamedResponse::class)) {
            $abstract = StreamedResponse::class;
        } elseif (is_subclass_of($concreted, BinaryFileResponse::class)) {
            $abstract = BinaryFileResponse::class;
        } elseif (is_subclass_of($concreted, Response::class)) {
            $abstract = Response::class;
        } else {
            throw new Error('Facade defined is not valid instance of Illuminate\Http\Response');
        }
        static::$facade[$abstract] = $concreted;
    }

    /**
     * Create a new file download response.
     *
     * @param \SplFileInfo|string $file
     * @param string              $name
     * @param array               $headers
     * @param null|string         $disposition
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($file, $name = null, array $headers = [], $disposition = 'attachment')
    {
        $response = $this->create(BinaryFileResponse::class, $file, 200, $headers, true, $disposition);

        if (!is_null($name)) {
            return $response->setContentDisposition($disposition, $name, str_replace('%', '', Str::ascii($name)));
        }

        return $response;
    }

    /**
     * Return a new response from the application.
     *
     * @param string $content
     * @param int    $status
     * @param array  $headers
     *
     * @return \Illuminate\Http\Response|\NekoOs\Override\Laravel\Lumen\Http\ResponseFactory
     */
    public function make($content = '', $status = 200, array $headers = [])
    {
        /** @var ResponseFactory $factory */
        $factory = app(\Laravel\Lumen\Http\ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        } elseif (get_class($factory) == static::class) {
            return $this->create(Response::class, $content, $status, $headers);
        }
        return $factory->make($content, $status, $headers);
    }

    /**
     * Return a new JSON response from the application.
     *
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return $this->create(JsonResponse::class, $data, $status, $headers, $options);
    }

    /**
     * Create a new streamed response instance.
     *
     * @param \Closure $callback
     * @param int      $status
     * @param array    $headers
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function stream($callback, $status = 200, array $headers = [])
    {
        return $this->create(StreamedResponse::class, $callback, $status, $headers);
    }

    /**
     * Create a new defined instance
     *
     * @param string|Closure $abstract
     * @param mixed          ...$arguments
     *
     * @return mixed
     */
    private function create($abstract, ...$arguments)
    {
        $response = static::$facade[$abstract] ?: $abstract;
        if ($response instanceof Closure) {
            return $response(...$arguments);
        }
        return new $response(...$arguments);
    }
}
