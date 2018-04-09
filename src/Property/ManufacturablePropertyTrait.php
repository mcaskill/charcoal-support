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
     * @return void
     */
    protected function setPropertyFactory(FactoryInterface $factory)
    {
        $this->propertyFactory = $factory;
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

    /**
     * Create an instance of {@see PropertyInterface} for the given property.
     *
     * @param  string            $propertyIdent The property identifier to return.
     * @param  MetadataInterface $metadata      The metadata to create the property from.
     * @throws \InvalidArgumentException If the $propertyIdent is not a string.
     * @throws \RuntimeException If the requested property is invalid.
     * @return PropertyInterface The {@see PropertyInterface} if found, null otherwise
     */
    protected function createPropertyFromMetadata($propertyIdent, MetadataInterface $metadata)
    {
        if (!is_string($propertyIdent)) {
            throw new \InvalidArgumentException(
                'Property identifier must be a string.'
            );
        }

        $props = $metadata->properties();

        if (empty($props)) {
            throw new \RuntimeException(sprintf(
                'Invalid model metadata [%s] - No properties defined.',
                get_class($this)
            ));
        }

        if (!isset($props[$propertyIdent])) {
            throw new \RuntimeException(sprintf(
                'Invalid model metadata [%s] - Undefined property metadata for "%s".',
                get_class($this),
                $propertyIdent
            ));
        }

        $propertyMetadata = $props[$propertyIdent];
        if (!isset($propertyMetadata['type'])) {
            throw new \RuntimeException(sprintf(
                'Invalid model metadata [%s] - Undefined property type for "%s".',
                get_class($this),
                $propertyIdent
            ));
        }

        $factory  = $this->propertyFactory();
        $property = $factory->create($propertyMetadata['type']);
        $property->metadata();
        $property->setIdent($propertyIdent);
        $property->setData($propertyMetadata);

        return $property;
    }
}
