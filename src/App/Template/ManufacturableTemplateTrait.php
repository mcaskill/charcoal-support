<?php

namespace Charcoal\Support\App\Template;

use RuntimeException;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

/**
 * Provides template factory features.
 */
trait ManufacturableTemplateTrait
{
    /**
     * Store the factory instance.
     *
     * @var FactoryInterface
     */
    protected $templateFactory;

    /**
     * Set an template factory.
     *
     * @param  FactoryInterface $factory The factory to create templates.
     * @return void
     */
    protected function setTemplateFactory(FactoryInterface $factory)
    {
        $this->templateFactory = $factory;
    }

    /**
     * Retrieve the template factory.
     *
     * @throws RuntimeException If the template factory is missing.
     * @return FactoryInterface
     */
    public function templateFactory()
    {
        if (!isset($this->templateFactory)) {
            throw new RuntimeException(sprintf(
                'Template Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->templateFactory;
    }
}
