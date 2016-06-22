<?php

namespace Charcoal\Support\Property;

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
    public function parseAsMultiple($val, $separator = ',')
    {
        if (is_var_empty($val)) {
            return [];
        }

        /**
         * This property is marked as "multiple".
         * Manually handling the resolution to array
         * until the property itself manages this.
         */
        if (is_string($val)) {
            return explode(',', $val);
        }

        /**
         * If the parameter isn't an array yet,
         * means we might be dealing with an integer,
         * an empty string, or an object.
         */
        if (!is_array($val)) {
            return [ $val ];
        }

        return $val;
    }

    /**
     * Parse the property value as a "L10N" value type.
     *
     * @param  mixed $val The value being localized.
     * @return TranslationString|null
     */
    public function parseAsTranslatable($val)
    {
        if (is_var_empty($val)) {
            return null;
        }

        return new TranslationString($val);
    }

    /**
     * Alias of {@see self::parseAsTranslatable()}
     *
     * @param  mixed $val The value being localized.
     * @return TranslationString|null
     */
    public function translatable($val)
    {
        return $this->parseAsTranslatable($val);
    }
}
