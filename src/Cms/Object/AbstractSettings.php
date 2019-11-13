<?php

namespace Charcoal\Support\Cms\Object;

use InvalidArgumentException;

// From 'charcoal-config'
use Charcoal\Config\DelegatesAwareTrait;

// From 'charcoal-object'
use Charcoal\Object\Content;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Cms\Object\SettingsInterface;

/**
 * Configset Model
 */
class AbstractSettings extends Content implements
    SettingsInterface
{
    use DelegatesAwareTrait;

    /**
     * Determine if a configuration key exists.
     *
     * @see    \ArrayAccess::offsetExists()
     * @param  string $key The key of the configuration item to look for.
     * @throws InvalidArgumentException If the key argument is invalid.
     * @return boolean
     */
    public function offsetExists($key)
    {
        if (is_numeric($key)) {
            throw new InvalidArgumentException(
                'Settings array access only supports non-numeric keys'
            );
        }

        $key = $this->camelize($key);

        /** @internal Edge Case: "_" → "" */
        if ($key === '') {
            return false;
        }

        $getter = 'get'.ucfirst($key);
        if (!isset($this->mutatorCache[$getter])) {
            $this->mutatorCache[$getter] = is_callable([ $this, $getter ]);
        }

        if ($this->mutatorCache[$getter]) {
            return ($this->{$getter}() !== null);
        }

        if (isset($this->{$key})) {
            return true;
        }

        return $this->hasInDelegates($key);
    }

    /**
     * Find an entry of the configuration by its key and retrieve it.
     *
     * @see    \ArrayAccess::offsetGet()
     * @param  string $key The key of the configuration item to look for.
     * @throws InvalidArgumentException If the key argument is invalid.
     * @return mixed The value or NULL.
     */
    public function offsetGet($key)
    {
        if (is_numeric($key)) {
            throw new InvalidArgumentException(
                'Settings array access only supports non-numeric keys'
            );
        }

        $key = $this->camelize($key);

        /** @internal Edge Case: "_" → "" */
        if ($key === '') {
            return null;
        }

        $getter = 'get'.ucfirst($key);
        if (!isset($this->mutatorCache[$getter])) {
            $this->mutatorCache[$getter] = is_callable([ $this, $getter ]);
        }

        if ($this->mutatorCache[$getter]) {
            return $this->{$getter}();
        }

        if (isset($this->{$key})) {
            return $this->{$key};
        }

        return $this->getInDelegates($key);
    }

    /**
     * Assign a value to the specified key of the configuration.
     *
     * @see    \ArrayAccess::offsetSet()
     * @param  string $key   The key to assign $value to.
     * @param  mixed  $value Value to assign to $key.
     * @throws InvalidArgumentException If the key argument is invalid.
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_numeric($key)) {
            throw new InvalidArgumentException(
                'Settings array access only supports non-numeric keys'
            );
        }

        $key = $this->camelize($key);

        /** @internal Edge Case: "_" → "" */
        if ($key === '') {
            return;
        }

        $setter = 'set'.ucfirst($key);
        if (!isset($this->mutatorCache[$setter])) {
            $this->mutatorCache[$setter] = is_callable([ $this, $setter ]);
        }

        if ($this->mutatorCache[$setter]) {
            $this->{$setter}($value);
        } else {
            $this->{$key} = $value;
        }

        $this->keyCache[$key] = true;
    }

    /**
     * Replaces the value from the specified key.
     *
     * Routine:
     * - When the value in the Config and the new value are both arrays,
     *   the method will replace their respective value recursively.
     * - Then or otherwise, the new value is {@see self::offsetSet() assigned} to the Config.
     *
     * @uses   \Charcoal\Config\AbstractConfig::offsetReplace()
     * @uses   array_replace_recursive()
     * @param  string $key   The data key to assign or merge $value to.
     * @param  mixed  $value The data value to assign to or merge with $key.
     * @throws InvalidArgumentException If the $key is not a string or is a numeric value.
     * @return void
     */
    public function offsetReplace($key, $value)
    {
        if (is_numeric($key)) {
            throw new InvalidArgumentException(
                'Settings array access only supports non-numeric keys'
            );
        }

        $key = $this->camelize($key);

        /** @internal Edge Case: "_" → "" */
        if ($key === '') {
            return;
        }

        if (is_array($value) && isset($this[$key])) {
            $data = $this[$key];
            if (is_array($data)) {
                $value = array_replace_recursive($data, $value);
            }
        }

        $this[$key] = $value;
    }

    /**
     * Adds new data, replacing / merging existing data with the same key.
     *
     * @see    \Charcoal\Config\AbstractConfig::merge()
     * @param  array|\Traversable $data Key-value dataset to merge.
     * @return self
     */
    public function merge($data)
    {
        foreach ($data as $key => $value) {
            $this->offsetReplace($key, $value);
        }

        return $this;
    }
}
