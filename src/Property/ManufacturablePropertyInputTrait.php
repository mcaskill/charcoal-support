<?php

namespace Charcoal\Support\Property;

use RuntimeException;
use Charcoal\Factory\FactoryInterface;

/**
 * Provides property control factory features.
 */
trait ManufacturablePropertyInputTrait
{
    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    protected $propertyInputFactory;

    /**
     * Set a property control factory.
     *
     * @param FactoryInterface $factory The property control factory,
     *     to create controlable property values.
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
     * @throws RuntimeException If the property control factory was not previously set.
     * @return FactoryInterface
     */
    public function propertyInputFactory()
    {
        if (!isset($this->propertyInputFactory)) {
            throw new RuntimeException(
                sprintf('Property Control Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->propertyInputFactory;
    }
}
