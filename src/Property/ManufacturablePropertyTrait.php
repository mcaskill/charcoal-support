<?php

namespace Charcoal\Support\Property;

use RuntimeException;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

/**
 * Provides property factory features.
 */
trait ManufacturablePropertyTrait
{
    /**
     * Store the factory instance.
     *
     * @var FactoryInterface
     */
    protected $propertyFactory;

    /**
     * Set a property factory.
     *
     * @param  FactoryInterface $factory The factory to create property values.
     * @return self
     */
    protected function setPropertyFactory(FactoryInterface $factory)
    {
        $this->propertyFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the property factory.
     *
     * @throws RuntimeException If the property factory is missing.
     * @return FactoryInterface
     */
    public function propertyFactory()
    {
        if (!isset($this->propertyFactory)) {
            throw new RuntimeException(sprintf(
                'Property Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->propertyFactory;
    }
}
