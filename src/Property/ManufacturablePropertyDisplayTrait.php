<?php

namespace Charcoal\Support\Property;

use RuntimeException;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

/**
 * Provides property display factory features.
 */
trait ManufacturablePropertyDisplayTrait
{
    /**
     * Store the factory instance.
     *
     * @var FactoryInterface
     */
    protected $propertyDisplayFactory;

    /**
     * Set a property display factory.
     *
     * @param  FactoryInterface $factory The factory to create displayable property values.
     * @return void
     */
    protected function setPropertyDisplayFactory(FactoryInterface $factory)
    {
        $this->propertyDisplayFactory = $factory;
    }

    /**
     * Retrieve the property display factory.
     *
     * @throws RuntimeException If the property display factory is missing.
     * @return FactoryInterface
     */
    public function propertyDisplayFactory()
    {
        if (!isset($this->propertyDisplayFactory)) {
            throw new RuntimeException(sprintf(
                'Property Display Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->propertyDisplayFactory;
    }
}
