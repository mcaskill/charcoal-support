<?php

namespace Charcoal\Support\App\Template;

use InvalidArgumentException;

// From 'charcoal-config'
use Charcoal\Config\EntityInterface;

// From 'charcoal-core'
use Charcoal\Source\StorableInterface;

// From 'charcoal-app'
use Charcoal\App\AppConfig;

/**
 * Provides support for merging the {@see \Charcoal\App\AppConfig Charcoal application configset}
 * with a storable configset.
 */
trait DynamicAppConfigTrait
{
    /**
     * The application's configuration container.
     *
     * @var AppConfig
     */
    protected $appConfig;

    /**
     * The class name of the dynamic configset model.
     *
     * A fully-qualified PHP namespace. Used for the model factory.
     *
     * @var string|null
     */
    protected $dynamicConfigClass;

    /**
     * Set the application's configset.
     *
     * @param  AppConfig $config A Charcoal application configset.
     * @return self
     */
    protected function setAppConfig(AppConfig $config)
    {
        $delegated = $this->dynamicConfig();
        if ($delegated instanceof EntityInterface) {
            $config['dynamic'] = $delegated;
            $config->prependDelegate($delegated);
        }

        $this->appConfig = $config;

        return $this;
    }

    /**
     * Retrieve the application's configset or a specific setting.
     *
     * @param  string|null $key     Optional data key to retrieve from the configset.
     * @param  mixed|null  $default The default value to return if data key does not exist.
     * @return mixed|AppConfig
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
     * @return self
     */
    protected function setDynamicConfigClass($className)
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
    protected function dynamicConfig()
    {
        $className = $this->dynamicConfigClass();
        if ($className) {
            $config = $this->modelFactory()->get($className);

            if ($config instanceof StorableInterface && !$config->id()) {
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
