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
     * @var \Charcoal\Factory\GenericFactory
     */
    protected $propertyInputFactory;

    /**
     * Set a property control factory.
     *
     * @param  \Charcoal\Factory\GenericFactory $factory The factory to create form controls for property values.
     * @return void
     */
    protected function setPropertyInputFactory(FactoryInterface $factory)
    {
        $this->propertyInputFactory = $factory;
    }

    /**
     * Retrieve the property control factory.
     *
     * @throws RuntimeException If the property control factory is missing.
     * @return \Charcoal\Factory\GenericFactory
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
