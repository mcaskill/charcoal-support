<?php

namespace Charcoal\Support\Cms;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;

/**
 * Additional utilities for the routing.
 */
trait HasTemplateOptionsTrait
{
    /**
     * The customized template options from the current context.
     *
     * @var mixed
     */
    protected $templateOptions;

    /**
     * Retrieve the template options from the current context.
     *
     * @param  string|null $key     Optional data key to retrieve from the configset.
     * @param  mixed|null  $default The default value to return if data key does not exist.
     * @return mixed|array
     */
    protected function templateOptions($key = null, $default = null)
    {
        if ($this->templateOptions === null) {
            $this->templateOptions = $this->buildTemplateOptions();
        }

        if ($key) {
            if (isset($this->templateOptions[$key])) {
                return $this->templateOptions[$key];
            } else {
                if (!is_string($default) && is_callable($default)) {
                    return $default();
                } else {
                    return $default;
                }
            }
        }

        return $this->templateOptions;
    }

    /**
     * Determine if the template option exists.
     *
     * @param  string $key Data key to check.
     * @return boolean
     */
    protected function hasTemplateOption($key)
    {
        if ($this->templateOptions === null) {
            $this->templateOptions = $this->buildTemplateOptions();
        }

        return isset($this->templateOptions[$key]);
    }

    /**
     * Build the template options from the current context.
     *
     * @return mixed|array
     */
    protected function buildTemplateOptions()
    {
        $templateOptions = [];

        $context = $this->contextObject();
        if ($context instanceof TemplateableInterface) {
            $templateOptions = $context->templateOptionsStructure();
        }

        return $templateOptions;
    }

    /**
     * Retrieve the current object relative to the context.
     *
     * @return \Charcoal\Model\ModelInterface
     */
    abstract public function contextObject();
}
