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
     * @var string
     */
    private static $facade;

    /**
     * @param string|Closure $concreted
     */
    public static function use($concreted)
    {
        if (is_subclass_of($concreted, Response::class) || $concreted instanceof Closure) {
            static::$facade = $concreted;
        } else {
            throw new Error('Argument not is a instance of Illuminate\Http\Response or a Closure');
        }
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
        $response = new BinaryFileResponse($file, 200, $headers, true, $disposition);

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
     * @return \Illuminate\Http\Response
     */
    public function make($content = '', $status = 200, array $headers = [])
    {

        /** @var ResponseFactory $factory */
        $factory = app(\Laravel\Lumen\Http\ResponseFactory::class);

        if (!($factory instanceof Response)) {
            return new Response($content, $status, $headers);
        } elseif (func_num_args() === 0) {
            return $factory;
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
        return new JsonResponse($data, $status, $headers, $options);
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
        return new StreamedResponse($callback, $status, $headers);
    }
}
