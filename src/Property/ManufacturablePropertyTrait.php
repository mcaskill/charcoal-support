<?php

namespace Charcoal\Support\Property;

use RuntimeException;
use Charcoal\Factory\FactoryInterface;

/**
 * Provides property factory features.
 */
trait ManufacturablePropertyTrait
{
    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    protected $propertyFactory;

    /**
     * Set a property factory.
     *
     * @param FactoryInterface $factory The property factory,
     *     to createable property values.
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
     * @throws RuntimeException If the property factory was not previously set.
     * @return FactoryInterface
     */
    public function propertyFactory()
    {
        if (!isset($this->propertyFactory)) {
            throw new RuntimeException(
                sprintf('Property Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->propertyFactory;
    }
}
