<?php

namespace Charcoal\Support\Widget;

use RuntimeException;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

/**
 * Provides widget factory features.
 */
trait ManufacturableWidgetTrait
{
    /**
     * Store the factory instance.
     *
     * @var \Charcoal\Factory\GenericFactory
     */
    protected $widgetFactory;

    /**
     * Set an widget factory.
     *
     * @param  \Charcoal\Factory\GenericFactory $factory The factory to create widgets.
     * @return void
     */
    protected function setWidgetFactory(FactoryInterface $factory)
    {
        $this->widgetFactory = $factory;
    }

    /**
     * Retrieve the widget factory.
     *
     * @throws RuntimeException If the widget factory is missing.
     * @return \Charcoal\Factory\GenericFactory
     */
    protected function widgetFactory()
    {
        if (!isset($this->widgetFactory)) {
            throw new RuntimeException(sprintf(
                'Widget Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->widgetFactory;
    }
}
