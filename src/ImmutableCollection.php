<?php

namespace Charcoal\Support;

use BadMethodCallException;

/**
 * Immutable Collection
 *
 * This class behaves the same as {@see Collection} except
 * it never modifies itself but returns a new object instead.
 *
 * Note: Adapted from _Slim_ and _Laravel_. See {@see CollectionInterface} for more details.
 */
class ImmutableCollection extends Collection
{
    /**
     * Reset the keys on the underlying array.
     *
     * @return static
     */
    public function values()
    {
        return new static(array_values($this->items));
    }

    /**
     * Set collection item.
     *
     * @param  string $key   The data key.
     * @param  mixed  $value The data value.
     * @return static
     */
    public function set($key, $value)
    {
        $clone = new static($this->items);
        $clone->set($key, $value);

        return $clone;
    }

    /**
     * Add item(s) to collection, replacing existing items with the same data key.
     *
     * @param  array $items Key-value array of data to append to this collection.
     * @return static
     */
    public function replace($items)
    {
        $clone = new static($this->items);
        $clone->replace($items);

        return $clone;
    }

    /**
     * Remove item from collection by key.
     *
     * @param  string $key The data key.
     * @return static
     */
    public function remove($key)
    {
        $clone = new static($this->items);
        $clone->remove($items);

        return $clone;
    }

    /**
     * Remove all items from collection.
     *
     * @throws BadMethodCallException Attempt to mutate immutable collection.
     * @return void
     */
    public function clear()
    {
        throw new BadMethodCallException(
            sprintf(
                'Attempt to mutate immutable %s object',
                get_class($this)
            )
        );
    }
}
