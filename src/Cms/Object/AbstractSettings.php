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
                'Entity array access only supports non-numeric keys.'
            );
        }

        $key = $this->camelize($key);
        if ($key === '') {
            return false;
        }

        if (is_callable([ $this, $key ])) {
            $value = $this->{$key}();
        } else {
            if (!isset($this->{$key})) {
                return $this->hasInDelegates($key);
            }
            $value = $this->{$key};
        }
        return ($value !== null);
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
                'Entity array access only supports non-numeric keys.'
            );
        }

        $key = $this->camelize($key);
        if ($key === '') {
            return null;
        }

        if (is_callable([ $this, $key ])) {
            return $this->{$key}();
        } else {
            if (isset($this->{$key})) {
                return $this->{$key};
            } else {
                return $this->getInDelegates($key);
            }
        }
    }

    /**
     * Assign a value to the specified key of the configuration.
     *
     * Set the value either by:
     * - a setter method (`set_{$key}()`)
     * - setting (or overriding)
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
                'Entity array access only supports non-numeric keys.'
            );
        }

        $key = $this->camelize($key);
        if ($key === '') {
            return;
        }

        $setter = 'set'.ucfirst($key);
        if (is_callable([ $this, $setter ])) {
            $this->{$setter}($value);
        } else {
            $this->{$key} = $value;
        }

        $this->keys[$key] = true;
    }

    /**
     * Add data to settings, replacing existing values with the same data key.
     *
     * @param  array|\Traversable $data Datas to merge.
     * @return SettingsInterface
     */
    public function merge($data)
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }

        return $this;
    }
}
