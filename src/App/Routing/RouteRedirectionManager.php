<?php

namespace Charcoal\Support\App\Routing;

use Closure;
use InvalidArgumentException;

// From PSR-7
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-app'
use Charcoal\App\AppAwareInterface;
use Charcoal\App\AppAwareTrait;

/**
 * The Route Redirection Manager
 *
 * Takes care of registering deprecated routes to be redirected to other routes.
 */
class RouteRedirectionManager implements
    AppAwareInterface
{
    use AppAwareTrait;
    use TranslatorAwareTrait;

    /**
     * @var array Redirected paths.
     */
    private $paths = [];

    /**
     * Default redirection structure.
     *
     * The _route_ can be a URI path, an external URL, a named route, or `404`.
     *
     * The _route type_ can be one of:
     *
     * - "templates" — A route defined under "config.routes.templates".
     * - "actions" — A route defined under "config.routes.actions".
     * - "scripts" — A route defined under "config.routes.scripts".
     * - "none" or FALSE — For a non-Charcoal route
     *
     * @var array
     */
    private $defaultRedirect = [
        'route'      => '/',
        'route_type' => null,
        'data_key'   => null,
        'methods'    => [ 'GET' ],
        'status'     => 302,
    ];

    /**
     * Constructor
     *
     * @param  array $data The class dependencies.
     * @return void
     */
    public function __construct(array $data)
    {
        $this->setApp($data['app']);
        $this->setPaths($data['routes']);

        if (isset($data['translator'])) {
            $this->setTranslator($data['translator']);
        } else {
            $container = $this->app()->getContainer();
            $this->setTranslator($container['translator']);
        }
    }

    /**
     * Set the paths to redirect.
     *
     * @param  array $paths One or more paths.
     * @return void
     */
    private function setPaths(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Setup Route Redirections
     *
     * @return void
     */
    public function setupRoutes()
    {
        foreach ($this->paths as $oldPath => &$newPath) {
            $this->addRedirection($oldPath, $newPath);
        }
    }

    /**
     * Add a redirection.
     *
     * Accepted formats for $paths:
     *
     * **Format #1 — Basic Usage**
     *
     * The "new-path" can be a URI or a named {@see \Slim\Route}.
     *
     * ```
     * [ 'old-path' => 'new-path' ]
     * ```
     *
     * **Format #2 — Redirect to front page**
     *
     * ```
     * [
     *     'old-path' => []
     * ]
     * ```
     *
     * **Format #2 — Custom HTTP status code**
     *
     * ```
     * [
     *     'old-path' => [
     *         'route'  => 'new-path',
     *         'status' => 301
     *     ]
     * ]
     * ```
     *
     * **Format #3 — Verbose redirection**
     *
     * The target URI or route can be multilingual.
     *
     * ```
     * [
     *     'old-path' => [
     *         'route' => [
     *             'en' => '/fr/new-path'
     *             'es' => '/es/nueva-ruta'
     *             'fr' => '/fr/nouveau-chemin'
     *         ],
     *         'route_type' => 'templates',
     *         'status'     => 301
     *     ]
     * ]
     * ```
     *
     * @todo   Add support for explicit {@see \Slim\Route}.
     * @param  string $oldPath The path to watch for and redirect to $newPath.
     * @param  mixed  $newPath The destination for $oldPath.
     * @throws InvalidArgumentException If the path is not a string.
     * @return void
     */
    public function addRedirection($oldPath, $newPath)
    {
        if (!is_string($oldPath) && !($oldPath instanceof UriInterface)) {
            throw new InvalidArgumentException(
                'The deprecated path must be a string; received %s',
                (is_object($oldPath) ? get_class($oldPath) : gettype($oldPath))
            );
        }

        $newPath = $this->parseRoute($newPath);
        $this->paths[$oldPath] = $newPath;

        $this->app()->map(
            $newPath['methods'],
            $oldPath,
            function (
                RequestInterface $request,
                ResponseInterface $response,
                array $args = []
            ) use (
                $newPath
            ) {
                if ($newPath['route'] === 404) {
                    return $response->withStatus(404);
                }

                $locale = isset($args['lang']) ? $args['lang'] : null;

                if ($newPath['data_key'] === null) {
                    if (is_array($newPath['route'])) {
                        $route = $this->translator()->translate($newPath['route'], [], null, $locale);
                        if ($route !== null) {
                            $newPath['route'] = $route;
                        } else {
                            $newPath['route'] = reset($newPath['route']);
                        }
                    }

                    return $response->withRedirect($newPath['route'], $newPath['status']);
                }

                if (!isset($newPath['ident'])) {
                    return $response->withStatus(404);
                }

                if (!empty($newPath[$newPath['data_key']])) {
                    $args = array_merge($newPath[$newPath['data_key']], $args);
                }

                $router = $this->get('router');
                if (isset($newPath[$newPath['data_key']]['route_endpoint'])) {
                    $endpoint = $this->translator()->translate(
                        $newPath[$newPath['data_key']]['route_endpoint'],
                        [],
                        null,
                        $locale
                    );

                    $args['route_endpoint'] = $endpoint;
                }

                $uri = $router->pathFor($newPath['ident'], $args);

                return $response->withRedirect($uri, $newPath['status']);
            }
        );
    }

    /**
     * Parse the destination route.
     *
     * @todo   Add support for explicit {@see \Slim\Route}.
     * @param  mixed $route The destination to prepare.
     * @throws InvalidArgumentException If the route is invalid.
     * @return array The parsed route structure.
     */
    private function parseRoute($route)
    {
        if (is_string($route) || is_numeric($route) || $route instanceof UriInterface) {
            $route = array_merge(
                $this->defaultRedirect,
                [ 'route' => $route ]
            );
        } else {
            if (!is_array($route)) {
                throw new InvalidArgumentException(
                    'The new path must be a string (%s) or an array structure; received %s',
                    'URI, \Psr\Http\Message\UriInterface, or the name of a \Slim\Route',
                    (is_object($route) ? get_class($route) : gettype($route))
                );
            }

            if (!isset($route['status'])) {
                $route['status'] = $this->defaultRedirect['status'];
            }

            if (!isset($route['route_type'])) {
                $route['route_type'] = null;
            }

            if (!isset($route['data_key'])) {
                $route['data_key'] = null;
            }

            if (isset($route['template_route'])) {
                $route['route']         = $route['template_route'];
                $route['methods']       = [ 'GET' ];
                $route['data_key']      = 'template_data';
                $route['route_type']    = 'templates';
                $route['template_data'] = [];
                unset($route['template_route']);
            } elseif (isset($route['action_route'])) {
                $route['route']       = $route['action_route'];
                $route['methods']     = [ 'POST' ];
                $route['data_key']    = 'action_data';
                $route['route_type']  = 'actions';
                $route['action_data'] = [];
                unset($route['action_route']);
            } elseif (isset($route['script_route'])) {
                $route['route']       = $route['script_route'];
                $route['methods']     = [ 'GET' ];
                $route['data_key']    = 'script_data';
                $route['route_type']  = 'scripts';
                $route['script_data'] = [];
                unset($route['script_route']);
            } else {
                if (isset($route['route_type'])) {
                    switch ($route['route_type']) {
                        case 'templates':
                            $route['data_key'] = 'template_data';
                            $route['methods']  = [ 'GET' ];
                            break;

                        case 'actions':
                            $route['data_key'] = 'action_data';
                            $route['methods']  = [ 'POST' ];
                            break;

                        case 'scripts':
                            $route['data_key'] = 'script_data';
                            $route['methods']  = [ 'GET' ];
                            break;

                        case 'template':
                        case 'action':
                        case 'script':
                            $route['data_key']    = $route['route_type'] . '_data';
                            $route['route_type'] .= 's';
                            $route['methods'] = ($route['route_type'] === 'actions') ? [ 'POST' ] : [ 'GET' ];
                            break;

                        default:
                            $route['data_key']   = null;
                            $route['route_type'] = null;
                    }
                }

                if (!isset($route[$route['data_key']])) {
                    $route[$route['data_key']] = [];
                }
            }

            $found = false;
            if ($route['route_type'] && isset($route['route'])) {
                $container = $this->app()->getContainer();
                $routeName = $route['route'];
                $routeType = $route['route_type'];
                $appRoutes = $container['config']['routes'];

                if (isset($appRoutes[$routeType])) {
                    $appRoutes = $appRoutes[$routeType];

                    if (isset($appRoutes[$routeName])) {
                        $route = array_merge($route, $appRoutes[$routeName]);
                        $found = true;
                    } else {
                        foreach ($appRoutes as $targetRoute) {
                            if (isset($targetRoute['ident']) && $targetRoute['ident'] === $routeName) {
                                $route = array_merge($route, $targetRoute);
                                $found = true;
                            }
                        }
                    }
                }
            }

            if ($found === false) {
                if (!isset($route['route'])) {
                    $route['route'] = $this->defaultRedirect['route'];
                }

                if (!isset($route['methods'])) {
                    $route['methods'] = $this->defaultRedirect['methods'];
                }
            }
        }

        return $route;
    }
}
