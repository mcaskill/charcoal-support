<?php

namespace Charcoal\Support\Property;

use RuntimeException;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

/**
 * Provides property control factory features.
 */
trait ManufacturablePropertyInputTrait
{
    /**
     * Store the factory instance.
     *
     * @var FactoryInterface
     */
    protected $propertyInputFactory;

    /**
     * Set a property control factory.
     *
     * @param  FactoryInterface $factory The factory to create form controls for property values.
     * @return self
     */
    protected function setPropertyInputFactory(FactoryInterface $factory)
    {
        $this->propertyInputFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the property control factory.
     *
     * @throws RuntimeException If the property control factory is missing.
     * @return FactoryInterface
     */
    public function propertyInputFactory()
    {
        if (!isset($this->propertyInputFactory)) {
            throw new RuntimeException(sprintf(
                'Property Control Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->propertyInputFactory;
    }
}
