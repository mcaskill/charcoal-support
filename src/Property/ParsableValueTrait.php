<?php

namespace Charcoal\Support\Property;

use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

// From 'cahrcoal-core'
use Charcoal\Model\ModelInterface;

// From 'cahrcoal-property'
use Charcoal\Property\AbstractProperty;
use Charcoal\Property\PropertyInterface;

// From 'cahrcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Provides utilities for parsing property values.
 *
 * Optional Dependency:
 * - 'model/factory'
 */
trait ParsableValueTrait
{
    /**
     * Parse the property value as a "multiple" value type.
     *
     * @param  mixed                    $value     The value being converted to an array.
     * @param  string|PropertyInterface $separator The boundary string.
     * @return array
     */
    protected function parseAsMultiple($value, $separator = ',')
    {
        if (is_array($value) || $value instanceof Traversable) {
            $parsed = [];
            foreach ($value as $val) {
                if (empty($val) && !is_numeric($val)) {
                    continue;
                }

                $parsed[] = $val;
            }

            return $parsed;
        }

        if ($separator instanceof PropertyInterface) {
            $separator = $separator->multipleSeparator();
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            $value = strval($value);
        }

        if (empty($value) && !is_numeric($value)) {
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
            return (array)$value;
        }

        return $value;
    }

    /**
     * Parse the property value as a date/time object.
     *
     * @param  mixed $value The date/time value.
     * @throws InvalidArgumentException If the date/time value is invalid.
     * @return DateTimeInterface|null
     */
    protected function parseAsDateTime($value)
    {
        if (empty($value) && !is_numeric($value)) {
            return null;
        }

        if (is_string($value)) {
            try {
                $value = new DateTime($value);
            } catch (Exception $e) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid date/time value: %s',
                    $e->getMessage()
                ), $e->getCode(), $e);
            }
        }

        if (!$value instanceof DateTimeInterface) {
            throw new InvalidArgumentException(
                'Invalid date/time value. Must be a date/time string or an implementation of DateTimeInterface.'
            );
        }

        return $value;
    }

    /**
     * Parse the property value as a "L10N" value type.
     *
     * @deprecated In favor of 'locomotivemtl/charcoal-translator'
     * @param  mixed $value The value being localized.
     * @return Translation|string|null
     */
    protected function parseAsTranslatable($value)
    {
        trigger_error('parseAsTranslatable() is deprecated. Use Translator::translation() instead.', E_USER_DEPRECATED);

        $value = $this->translator()->translation($value);

        return $value === null ? '' : $value;
    }

    /**
     * Retrieve a fallback value from a translatable value.
     *
     * Note: Fallbacks are determined in your application settings, "locales.fallback_languages".
     *
     * @param  mixed $value A translatable value.
     * @return string|null
     */
    protected function parseAsTranslatableFallback($value)
    {
        $fallback = $this->translator()->translate($value);
        if (empty($fallback) && !is_numeric($fallback) && (is_array($value) || ($value instanceof ArrayAccess))) {
            foreach ($this->translator()->getFallbackLocales() as $lang) {
                $trans = $value[$lang];
                if (!empty($trans) || is_numeric($trans)) {
                    $fallback = $trans;
                    break;
                }
            }
        }

        return $fallback;
    }

    /**
     * Pair the translatable array items.
     *
     * Converts this:
     * ```
     * {
     *     "en": [ "Item A", "Item B", "Item C", "Item D" ],
     *     "fr": [ "Élément A", "Élément B", "Élément C" ]
     * }
     * ```
     *
     * Into:
     * ```
     * [
     *     {
     *         "en": "Item A",
     *         "fr": "Élément A",
     *     },
     *     {
     *         "en": "Item B",
     *         "fr": "Élément B",
     *     },
     *     {
     *         "en": "Item C",
     *         "fr": "Élément C",
     *     },
     *     {
     *         "en": "Item D",
     *         "fr": "",
     *     }
     * ],
     * ```
     *
     * @param  mixed $value     The value being converted to an array.
     * @param  mixed $separator The item delimiter. This can be a string or a function.
     * @return array
     */
    protected function pairTranslatableArrayItems($value, $separator = ',')
    {
        if (empty($value) && !is_numeric($value)) {
            return null;
        }

        if ($value instanceof Translation) {
            $value = $value->data();
        }

        if ($separator instanceof \Closure) {
            $value = $separator($value);
        } else {
            // Parse each locale's collection into an array
            foreach ($value as $k => $v) {
                $value[$k] = $this->parseAsMultiple($v, $separator);
            }
        }

        // Retrieve the highest collection count among the locales
        $count = max(array_map('count', $value));

        // Pair the items across locales
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $entry = [];

            foreach ($value as $lang => $arr) {
                if (isset($arr[$i])) {
                    $entry[$lang] = $arr[$i];
                }
            }

            $result[] = $this->translator()->translation($entry);
        }

        return $result;
    }

    /**
     * Cast the property value to a given data type.
     *
     * @param mixed $value    The related value.
     * @param mixed $castTo   The data type to cast the $value to.
     * @param mixed $fallback A default value or a property identifier
     *     to retrieve a default value from the current object.
     * @throws InvalidArgumentException If the $castTo parameter is invalid.
     * @return mixed|null
     */
    protected function castTo(
        $value,
        $castTo,
        $fallback = null
    ) {
        if (!is_string($castTo) && !is_array($castTo) && !($castTo instanceof PropertyInterface)) {
            throw new InvalidArgumentException('Invalid data casting type.');
        }

        if (empty($value) && !is_numeric($value)) {
            $value    = $fallback;
            $property = null;
            if ($fallback instanceof PropertyInterface) {
                $property = $fallback->ident();
            } elseif (is_string($fallback) && $this->hasProperty($fallback)) {
                $property = $fallback;
            }

            if ($property && method_exists($this, 'defaultData')) {
                $defaultData = $this->defaultData();
                if (isset($defaultData[$fallback])) {
                    $value = $defaultData[$fallback];
                }
            }
        }

        if (($castTo instanceof PropertyInterface) || is_array($castTo)) {
            if (is_object($castTo)) {
                $l10n   = $castTo->l10n();
                $multi = $castTo->multiple();
                $sep   = $castTo->multipleSeparator();
            } else {
                $l10n  = (isset($castTo['l10n']) && $castTo['l10n']);
                $multi = (isset($castTo['multiple']) && $castTo['multiple']);
                $sep   = (isset($castTo['multiple_options']['separator'])
                         ? $castTo['multiple_options']['separator']
                         : ',');
            }

            if ($l10n && $multi) {
                return $this->pairTranslatableArrayItems($value, $sep);
            } elseif ($l10n) {
                return $this->translator()->translation($value);
            } elseif ($multi) {
                return $this->parseAsMultiple($value, $sep);
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
                return (object)$value;

            case 'array':
                return (array)$value;

            default:
                if (method_exists($this, 'modelFactory')) {
                    if (is_string($value) || is_numeric($value)) {
                        $objId = $value;
                        $value = $this->modelFactory()->create($castTo);
                        $value->load($objId);
                    }
                }
                break;
        }

        return $value;
    }
}
