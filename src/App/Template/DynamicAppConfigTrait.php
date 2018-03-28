<?php

namespace Charcoal\Support\App\Template;

use InvalidArgumentException;

// From 'charcoal-config'
use Charcoal\Config\DelegatesAwareInterface;
use Charcoal\Config\EntityInterface;

// From 'charcoal-core'
use Charcoal\Source\StorableInterface;

// From 'charcoal-app'
use Charcoal\App\AppConfig;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Cms\Object\SettingsInterface;

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
     * The dynamic configset instance.
     *
     * @var EntityInterface
     */
    protected $dynamicConfig;

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
     * Note: This method should be called after {@see self::setDynamicConfig()}
     * or {@see self::setDynamicConfigClass()}.
     *
     * @param  AppConfig $appConfig A Charcoal application configset.
     * @throws InvalidArgumentException If a configset already exists.
     * @return self
     */
    protected function setAppConfig(AppConfig $appConfig)
    {
        if ($this->appConfig !== null) {
            throw new InvalidArgumentException(
                'Application configset already assigned.'
            );
        }

        $dynConfig = $this->dynamicConfig();
        if ($dynConfig instanceof DelegatesAwareInterface) {
            $dynConfig->prependDelegate($appConfig);
            $this->appConfig = $dynConfig;
        } elseif ($dynConfig instanceof EntityInterface) {
            $appConfig['dynamic'] = $dynConfig;
            $appConfig->prependDelegate($dynConfig);
            $this->appConfig = $appConfig;
        } else {
            $this->appConfig = $appConfig;
        }

        return $this;
    }

    /**
     * Retrieve the application's configset or a specific setting.
     *
     * @param  string|null $key     Optional data key to retrieve from the configset.
     * @param  mixed|null  $default The default value to return if data key does not exist.
     * @return mixed|AppConfig|SettingsInterface
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
     * Note: This method should be called before {@see self::setAppConfig()}.
     *
     * @param  string $className The class name of the dynamic configset model.
     * @throws InvalidArgumentException If the class name is not a string.
     * @return self
     */
    protected function setDynamicConfigClass($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(
                'Dynamic configset class name must be a string.'
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
     * Set the dynamic configset instance.
     *
     * Note: This method should be called before {@see self::setAppConfig()}.
     *
     * @param  EntityInterface $dynConfig A dynamic configset.
     * @throws InvalidArgumentException If a configset already exists.
     * @return self
     */
    protected function setDynamicConfig(EntityInterface $dynConfig)
    {
        if ($this->dynamicConfig !== null) {
            throw new InvalidArgumentException(
                'Dynamic configset already assigned.'
            );
        }

        if ($this->appConfig !== null) {
            if ($dynConfig instanceof DelegatesAwareInterface) {
                $dynConfig->prependDelegate($this->appConfig);
            } else {
                $appConfig['dynamic'] = $dynConfig;
                $appConfig->prependDelegate($dynConfig);
            }
        }

        $this->dynamicConfig = $dynConfig;
        return $this;
    }

    /**
     * Retrieve the dynamic configset instance.
     *
     * @return DelegatesAwareInterface|EntityInterface|null
     */
    protected function dynamicConfig()
    {
        if ($this->dynamicConfig === null) {
            $this->dynamicConfig = $this->loadDynamicConfig();
        }

        return $this->dynamicConfig;
    }

    /**
     * Load the dynamic configset instance (from the database).
     *
     * @return DelegatesAwareInterface|EntityInterface|null
     */
    protected function loadDynamicConfig()
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
