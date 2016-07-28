<?php

namespace Charcoal\Support\Property;

use DateTime;
use InvalidArgumentException;

use Charcoal\Property\AbstractProperty;
use Charcoal\Translation\TranslationString;

/**
 * Provides utilities for parsing property values.
 */
trait ParsableValueTrait
{
    /**
     * Parse the property value as a "multiple" value type.
     *
     * @param  mixed                   $value     The value being converted to an array.
     * @param  string|AbstractProperty $separator The boundary string.
     * @return array
     */
    public function parseAsMultiple($value, $separator = ',')
    {
        if (is_var_empty($value)) {
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
     * Parse the property value as a "L10N" value type.
     *
     * @deprecated In favor of {@see TranslationString::isTranslatable()}
     * @param  mixed $value The value being localized.
     * @return TranslationString|null
     */
    public function parseAsDateTime($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = new DateTime($value);
        }

        if (!$value instanceof DateTime) {
            throw new InvalidArgumentException(
                'Invalid date/time value. Must be a date/time string or an implementation of DateTime.'
            );
        }

        return $value;
    }

    /**
     * Parse the property value as a "L10N" value type.
     *
     * @deprecated In favor of {@see TranslationString::isTranslatable()}
     * @param  mixed $value The value being localized.
     * @return TranslationString|null
     */
    public function parseAsTranslatable($value)
    {
        if (!TranslationString::isTranslatable($value)) {
            return null;
        }

        return new TranslationString($value);
    }

    /**
     * Alias of {@see self::parseAsTranslatable()}
     *
     * @deprecated In favor of {@see TranslationString::isTranslatable()}
     * @param  mixed $value The value being localized.
     * @return TranslationString|null
     */
    public function translatable($value)
    {
        return $this->parseAsTranslatable($value);
    }
}
