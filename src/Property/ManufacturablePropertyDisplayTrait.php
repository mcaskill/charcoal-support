<?php

namespace Charcoal\Support\Property;

use RuntimeException;
use Charcoal\Factory\FactoryInterface;

/**
 * Provides property display factory features.
 */
trait ManufacturablePropertyDisplayTrait
{
    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    private $propertyDisplayFactory;

    /**
     * Set a property display factory.
     *
     * @param FactoryInterface $factory The property display factory,
     *     to create displayable property values.
     * @return self
     */
    protected function setPropertyDisplayFactory(FactoryInterface $factory)
    {
        $this->propertyDisplayFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the property display factory.
     *
     * @throws RuntimeException If the property display factory was not previously set.
     * @return FactoryInterface
     */
    public function propertyDisplayFactory()
    {
        if (!isset($this->propertyDisplayFactory)) {
            throw new RuntimeException(
                sprintf('Property Display Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->propertyDisplayFactory;
    }
}
