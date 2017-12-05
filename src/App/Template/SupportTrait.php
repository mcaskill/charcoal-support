<?php

namespace Charcoal\Support\App\Template;

use RuntimeException;

// From PSR-7
use Psr\Http\Message\UriInterface;

/**
 * Additional utilities for managing views.
 */
trait SupportTrait
{
    /**
     * The base URI.
     *
     * @var UriInterface|null
     */
    protected $baseUrl;

    /**
     * The cache of parsed template names.
     *
     * @var array
     */
    protected static $templateNameCache = [];

    /**
     * Whether the debug mode is enabled.
     *
     * @var boolean
     */
    private $debug = false;

    /**
     * Retrieve the template's identifier.
     *
     * @return string
     */
    public function templateName()
    {
        $key = substr(strrchr('\\' . get_class($this), '\\'), 1);

        if (!isset(static::$templateNameCache[$key])) {
            $value = $key;

            if (!ctype_lower($value)) {
                $value = preg_replace('/\s+/u', '', $value);
                $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1-', $value), 'UTF-8');
            }

            $value = str_replace(
                [ 'abstract', 'trait', 'interface', 'template', '\\' ],
                '',
                $value
            );

            static::$templateNameCache[$key] = trim($value, '-');
        }

        return static::$templateNameCache[$key];
    }

    /**
     * Set application debug mode.
     *
     * @param  boolean $debug The debug flag.
     * @return void
     */
    protected function setDebug($debug)
    {
        $this->debug = !!$debug;
    }

    /**
     * Retrieve the application debug mode.
     *
     * @return boolean
     */
    public function debug()
    {
        return $this->debug;
    }

    /**
     * Set the base URI of the project.
     *
     * @see    \Charcoal\App\ServiceProvider\AppServiceProvider `$container['base-url']`
     * @param  UriInterface $uri The base URI.
     * @return self
     */
    protected function setBaseUrl(UriInterface $uri)
    {
        $this->baseUrl = $uri;

        return $this;
    }

    /**
     * Retrieve the base URI of the project.
     *
     * @throws RuntimeException If the base URI is missing.
     * @return UriInterface|null
     */
    public function baseUrl()
    {
        if (!isset($this->baseUrl)) {
            throw new RuntimeException(sprintf(
                'The base URI is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->baseUrl;
    }

    /**
     * Prepend the base URI to the given path.
     *
     * @param  string $uri A URI path to wrap.
     * @return UriInterface
     */
    public function createAbsoluteUrl($uri)
    {
        $uri = strval($uri);
        if ($uri === '') {
            $uri = $this->baseUrl()->withPath('');
        } else {
            $parts = parse_url($uri);
            if (!isset($parts['scheme'])) {
                if (!in_array($uri[0], [ '/', '#', '?' ])) {
                    $path  = isset($parts['path']) ? $parts['path'] : '';
                    $query = isset($parts['query']) ? $parts['query'] : '';
                    $hash  = isset($parts['fragment']) ? $parts['fragment'] : '';
                    $uri   = $this->baseUrl()->withPath($path)->withQuery($query)->withFragment($hash);
                }
            }
        }

        return $uri;
    }
}
