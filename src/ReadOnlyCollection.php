<?php

namespace Charcoal\Support;

use BadMethodCallException;

/**
 * Immutable Collection
 *
 * This class behaves the same as {@see ImmutableCollection} except
 * it throws exceptions if mutations are attempted.
 *
 * Note: Adapted from _Slim_ and _Laravel_. See {@see CollectionInterface} for more details.
 */
class ReadOnlyCollection extends Collection
{
    /**
     * Reset the keys on the underlying array.
     *
     * @throws BadMethodCallException Attempt to mutate immutable collection.
     * @return static
     */
    public function values()
    {
        throw new BadMethodCallException(
            sprintf(
                'Attempt to mutate immutable %s object',
                get_class($this)
            )
        );
    }

    /**
     * Set collection item.
     *
     * @param  string $key   The data key.
     * @param  mixed  $value The data value.
     * @throws BadMethodCallException Attempt to mutate immutable collection.
     * @return static
     */
    public function set($key, $value)
    {
        unset($key, $value);

        throw new BadMethodCallException(
            sprintf(
                'Attempt to mutate immutable %s object',
                get_class($this)
            )
        );
    }

    /**
     * Add item(s) to collection, replacing existing items with the same data key.
     *
     * @param  array $items Key-value array of data to append to this collection.
     * @throws BadMethodCallException Attempt to mutate immutable collection.
     * @return static
     */
    public function replace($items)
    {
        unset($items);

        throw new BadMethodCallException(
            sprintf(
                'Attempt to mutate immutable %s object',
                get_class($this)
            )
        );
    }

    /**
     * Remove item from collection by key.
     *
     * @param  string $key The data key.
     * @throws BadMethodCallException Attempt to mutate immutable collection.
     * @return static
     */
    public function remove($key)
    {
        unset($key);

        throw new BadMethodCallException(
            sprintf(
                'Attempt to mutate immutable %s object',
                get_class($this)
            )
        );
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
