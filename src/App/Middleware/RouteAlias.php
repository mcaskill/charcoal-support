<?php

namespace Charcoal\Support\App\Middleware;

// From PSR-7
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware to create an alternate path or to rename a URI path.
 *
 * Renames the request path or supplies an alternative path.
 * The middleware is provided a map of URI paths to catch
 * and redirect to new paths.
 */
class RouteAlias
{
    /**
     * @var array Aliased paths.
     */
    private $aliasedPaths = [];

    /**
     * @var array Renamed paths.
     */
    private $renamedPaths = [];

    /**
     * Constructor
     *
     * Accepted format for $paths: `[ 'real-path' => 'alternate-path' ]`.
     *
     * The "alternate-path" can be a URI or a named {@see \Slim\Route}.
     *
     * @param  array $aliases One or more paths as an associative array.
     * @param  array $renamed One or more paths as an associative array.
     * @return void
     */
    public function __construct(array $aliases = [], array $renamed = [])
    {
        $this->aliasedPaths = $aliases;
        $this->renamedPaths = $renamed;
    }

    /**
     * Execute the middleware.
     *
     * @param  RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response A PSR-7 compatible Response instance.
     * @param  callable          $next     The next callable middleware.
     * @return ResponseInterface
     */
    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $uri  = $request->getUri();
        $path = $uri->getPath();

        $realPath = false;

        if (isset($this->renamedPaths[$path])) {
            return $response->withStatus(404);
        } else {
            $realPath = array_search($path, $this->renamedPaths, true);
        }

        if ($realPath === false && !isset($this->aliasedPaths[$path])) {
            $realPath = array_search($path, $this->aliasedPaths, true);
        }

        if ($realPath !== false) {
            $request = $request->withUri($uri->withPath($realPath));
        }

        return $next($request, $response);
    }
}
