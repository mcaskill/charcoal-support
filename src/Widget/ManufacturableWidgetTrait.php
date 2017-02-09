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
     * @var FactoryInterface
     */
    protected $widgetFactory;

    /**
     * Set an widget factory.
     *
     * @param  FactoryInterface $factory The factory to create widgets.
     * @return self
     */
    protected function setWidgetFactory(FactoryInterface $factory)
    {
        $this->widgetFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the widget factory.
     *
     * @throws RuntimeException If the widget factory is missing.
     * @return FactoryInterface
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
