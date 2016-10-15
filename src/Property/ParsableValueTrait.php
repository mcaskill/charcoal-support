<?php

namespace Charcoal\Support\Property;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

use Charcoal\Model\ModelInterface;
use Charcoal\Property\AbstractProperty;
use Charcoal\Property\PropertyInterface;
use Charcoal\Translation\TranslationString;

/**
 * Provides utilities for parsing property values.
 */
trait ParsableValueTrait
{
    /**
     * Retrieve the object model factory.
     *
     * @return \Charcoal\Factory\FactoryInterface
     */
    abstract public function modelFactory();

    /**
     * Parse the property value as a "multiple" value type.
     *
     * @param  mixed                    $value     The value being converted to an array.
     * @param  string|PropertyInterface $separator The boundary string.
     * @return array
     */
    public function parseAsMultiple($value, $separator = ',')
    {
        if (
            !isset($value) ||
            (is_string($value) && ! strlen(trim($value))) ||
            (is_array($value) && ! count(array_filter($value, 'strlen')))
        ) {
            return [];
        }

        /**
         * This property is marked as "multiple".
         * Manually handling the resolution to array
         * until the property itself manages this.
         */
        if (is_string($value)) {
            return explode($separator, $value);
        }

        /**
         * If the parameter isn't an array yet,
         * means we might be dealing with an integer,
         * an empty string, or an object.
         */
        if (!is_array($value)) {
            return [ $value ];
        }

        return $value;
    }

    /**
     * Parse the property value as a date/time object.
     *
     * @param  mixed $value The date/time value.
     * @return DateTimeInterface|null
     */
    public function parseAsDateTime($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = new DateTime($value);
        }

        if (!$value instanceof DateTimeInterface) {
            throw new InvalidArgumentException(
                'Invalid date/time value. Must be a date/time string or an implementation of DateTime.'
            );
        }

        return $value;
    }

    /**
     * Parse the property value as a "L10N" value type.
     *
     * @param  mixed $value The value being localized.
     * @return TranslationString|string
     */
    public function parseAsTranslatable($value)
    {
        if (TranslationString::isTranslatable($value)) {
            return new TranslationString($value);
        } else {
            return '';
        }
    }

    /**
     * Cast the property value to a given data type.
     *
     * @param  mixed  $value    The related value.
     * @param  string $castTo   The data type to cast the $value to.
     * @param  mixed  $fallback A default value or a property identifier
     *     to retrieve a default value from the current object.
     * @return mixed|null
     */
    public function castTo(
        $value,
        $castTo,
        $fallback = null
    ) {
        if (!is_string($castTo)) {
            throw new InvalidArgumentException('Invalid data type.');
        }

        if ($value === null || $value === '') {
            if ($fallback instanceof PropertyInterface) {
                $property = $fallback->ident();
            } elseif (is_string($fallback) && $this->hasProperty($fallback)) {
                $property = $fallback;
            } else {
                $value = $fallback;
            }

            if (isset($property)) {
                $defaultData = $this->defaultData();
                if (isset($defaultData[$fallback])) {
                    $value = $defaultData[$fallback];
                } else {
                    $value = $fallback;
                }
            } else {
                $value = $fallback;
            }
        }

        switch ($castTo) {
            case 'bool':
            case 'boolean':
                return boolval($value);

            case 'str':
            case 'string':
                return strval($value);

            case 'int':
            case 'integer':
                return intval($value);

            case 'float':
                return floatval($value);

            case 'object':
                return (object) $value;

            case 'array':
                return (array) $value;

            default:
                # if (class_exists($castTo)) {
                    if (is_string($value) || is_numeric($value)) {
                        $objId = $value;
                        $value = $this->modelFactory()->create($castTo);
                        $value->load($objId);
                    }/* else {
                        $value = new $castTo($value);
                    }*/
                # }
                break;
        }

        return $value;
    }
}
