<?php

namespace Charcoal\Support\App\Template;

use ArrayAccess;
use RuntimeException;
use InvalidArgumentException;

// From PSR-7
use Psr\Http\Message\UriInterface;

// From 'charcoal-config'
use Charcoal\Config\EntityInterface;

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
     * The application's configuration container.
     *
     * It can be a static configset from the DIC (e.g.:
     * {@see \Charcoal\App\StaticConfig `$container['config']`}) or a
     * {@see SupportTrait::dynamicConfig() dynamic configset} from the database.
     *
     * @var array|ArrayAccess
     */
    protected $appConfig = [];

    /**
     * The class name of the dynamic configset model.
     *
     * A fully-qualified PHP namespace. Used for the model factory.
     *
     * @var string|null
     */
    protected $dynamicConfigClass;

    /**
     * The cache of parsed template names.
     *
     * @var array
     */
    protected static $templateNameCache = [];

    /**
     * Application Debug Mode.
     *
     * @return boolean
     */
    public function debug()
    {
        if (!$this->appConfig) {
            return false;
        }

        $debug   = isset($this->appConfig['debug'])    ? $this->appConfig['debug']    : false;
        $devMode = isset($this->appConfig['dev_mode']) ? $this->appConfig['dev_mode'] : false;

        return $debug || $devMode;
    }

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
     * Set the base URI of the project.
     *
     * @see    \Charcoal\App\ServiceProvider\AppServiceProvider `$container['base-url']`
     * @param  UriInterface $uri The base URI.
     * @return self
     */
    public function setBaseUrl(UriInterface $uri)
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
    public function withBaseUrl($uri)
    {
        $uri = strval($uri);
        if ($uri && !parse_url($uri, PHP_URL_SCHEME)) {
            if (!in_array($uri[0], [ '/', '#', '?' ])) {
                return $this->baseUrl()->withPath($uri);
            }
        }

        return $uri;
    }

    /**
     * Set the application's configset.
     *
     * @param  array|ArrayAccess $config A configset.
     * @throws InvalidArgumentException If the configset is invalid.
     * @return self
     */
    public function setAppConfig($config)
    {
        if (!is_array($config) && !($config instanceof ArrayAccess)) {
            throw new InvalidArgumentException('The configset must be array-accessible.');
        }

        if ($config instanceof EntityInterface) {
            $delegate = $this->dynamicConfig();
            if ($delegate instanceof EntityInterface) {
                $config['dynamic'] = $delegate;
                $config->prependDelegate($delegate);
            }
        }

        $this->appConfig = $config;

        return $this;
    }

    /**
     * Retrieve the application's configset.
     *
     * @param  string|null $key     Optional data key to retrieve from the configset.
     * @param  mixed|null  $default The default value to return if data key does not exist.
     * @return mixed|ArrayAccess
     */
    public function appConfig($key = null, $default = null)
    {
        if ($key) {
            if (isset($this->appConfig[$key])) {
                return $this->appConfig[$key];
            } else {
                if (!is_string($default) && is_callable($default)) {
                    return $default();
                } else {
                    return $default;
                }
            }
        }

        return $this->appConfig;
    }

    /**
     * Set the class name of the dynamic configset model.
     *
     * @param  string $className The class name of the dynamic configset model.
     * @throws InvalidArgumentException If the class name is not a string.
     * @return AbstractPropertyDisplay Chainable
     */
    public function setDynamicConfigClass($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(
                'Configset class name must be a string.'
            );
        }

        $this->dynamicConfigClass = $className;

        return $this;
    }

    /**
     * Retrieve the class name of the dynamic configset model.
     *
     * @return string
     */
    public function dynamicConfigClass()
    {
        return $this->dynamicConfigClass;
    }

    /**
     * Retrieve the dynamic configset (from the database).
     *
     * @return EntityInterface|null
     */
    private function dynamicConfig()
    {
        $className = $this->dynamicConfigClass();
        if ($className) {
            $config = $this->modelFactory()->get($className);

            if (!$config->id()) {
                $config->load(1);
            }

            return $config;
        }

        return null;
    }

    /**
     * Retrieve the object model factory.
     *
     * @return \Charcoal\Factory\FactoryInterface
     */
    abstract public function modelFactory();
}
