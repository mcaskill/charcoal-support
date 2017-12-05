<?php

namespace Charcoal\Support\App;

// From 'charcoal-app'
use Charcoal\App\AppConfig;

/**
 * Provides awareness of the Charcoal application to the instance.
 */
trait AppAwareTrait
{
    /**
     * The application's configuration container.
     *
     * @var AppConfig
     */
    protected $appConfig;

    /**
     * Set the application's configset.
     *
     * @param  AppConfig $config A Charcoal application configset.
     * @return self
     */
    protected function setAppConfig(AppConfig $config)
    {
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
}
