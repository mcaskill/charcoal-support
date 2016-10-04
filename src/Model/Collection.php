<?php

namespace Charcoal\Support\Model;

use LogicException;
use InvalidArgumentException;

// From 'illuminate/support'
use Illuminate\Support\Arr;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Support\Collection as BaseCollection;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Model\HierarchicalCollection;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface as Model;
use Charcoal\Model\CollectionInterface as CharcoalCollection;

/**
 * Object Collection
 *
 * The object collection specializes in handling instantiated models.
 * Unacceptable values (@see self::isAcceptable()) are rejected.
 *
 * The model collection extends the base collection with a fluent interface
 * for querying the model's source (database).
 */
class Collection extends BaseCollection implements QueueableCollection
{
    /**
     * Determine if a model is acceptable.
     *
     * @param  mixed $value The value being evaluated..
     * @return boolean
     */
    public function isAcceptable($value)
    {
        return $value instanceof Model;
    }

    /**
     * Find a model in the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return Model
     */
    public function find($key, $default = null)
    {
        if ($this->isAcceptable($key)) {
            $key = $key->id();
        }

        return Arr::first($this->items, function ($model) use ($key) {
            return $model->id() == $key;
        }, $default);
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     * @return $this
     */
    public function add($item)
    {
        if (!$this->isAcceptable($item)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Must be a model, received %s',
                    get_var_type($value)
                )
            );
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (!$this->isAcceptable($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Must be a model, received %s',
                    get_var_type($value)
                )
            );
        }

        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Get the array of primary keys.
     *
     * @return array
     */
    public function modelKeys()
    {
        return array_map(function ($model) {
            return $model->id();
        }, $this->items);
    }

    /**
     * Alias of {@see self::modelKeys}.
     *
     * @return array
     */
    public function modelIds()
    {
        return $this->modelKeys();
    }

    /**
     * Get a dictionary keyed by primary keys.
     *
     * @param  \ArrayAccess|array|null  $items
     * @return array
     */
    public function getDictionary($items = null)
    {
        $items = is_null($items) ? $this->items : $items;

        $dictionary = [];

        foreach ($items as $value) {
            $dictionary[$value->id()] = $value;
        }

        return $dictionary;
    }



    // Extends BaseCollection
    // =================================================================================================================

    /**
     * Determine if a key exists in the collection.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $value = null)
    {
        if (func_num_args() == 2) {
            return parent::contains($key, $value);
        }

        if ($this->useAsCallable($key)) {
            return parent::contains($key);
        }

        $key = $this->isAcceptable($key) ? $key->id() : $key;

        return parent::contains(function ($model) use ($key) {
            return $model->id() == $key;
        });
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  \ArrayAccess|array  $items
     * @return static
     */
    public function merge($items)
    {
        $dictionary = $this->getDictionary();

        foreach ($items as $item) {
            $dictionary[$item->id()] = $item;
        }

        return new static(array_values($dictionary));
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable  $callback
     * @return \Illuminate\Support\Collection
     */
    public function map(callable $callback)
    {
        $result = parent::map($callback);

        return $result->contains(function ($item) {
            return ! $this->isAcceptable($item);
        }) ? $result->toBase() : $result;
    }

    /**
     * Diff the collection with the given items.
     *
     * @param  \ArrayAccess|array  $items
     * @return static
     */
    public function diff($items)
    {
        $diff = new static;

        $dictionary = $this->getDictionary($items);

        foreach ($this->items as $item) {
            if (! isset($dictionary[$item->id()])) {
                $diff->add($item);
            }
        }

        return $diff;
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param  \ArrayAccess|array  $items
     * @return static
     */
    public function intersect($items)
    {
        $intersect = new static;

        $dictionary = $this->getDictionary($items);

        foreach ($this->items as $item) {
            if (isset($dictionary[$item->id()])) {
                $intersect->add($item);
            }
        }

        return $intersect;
    }

    /**
     * Return only unique items from the collection.
     *
     * @param  string|callable|null  $key
     * @param  bool  $strict
     * @return static|\Illuminate\Support\Collection
     */
    public function unique($key = null, $strict = false)
    {
        if (! is_null($key)) {
            return parent::unique($key, $strict);
        }

        return new static(array_values($this->getDictionary()));
    }

    /**
     * Returns only the models from the collection with the specified keys.
     *
     * @param  mixed  $keys
     * @return static
     */
    public function only($keys)
    {
        $dictionary = Arr::only($this->getDictionary(), $keys);

        return new static(array_values($dictionary));
    }

    /**
     * Returns all models in the collection except the models with specified keys.
     *
     * @param  mixed  $keys
     * @return static
     */
    public function except($keys)
    {
        $dictionary = Arr::except($this->getDictionary(), $keys);

        return new static(array_values($dictionary));
    }

    /**
     * Parse the given items into an array.
     *
     * @param  mixed  $items The variable being parsed.
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if ($items instanceof HierarchicalCollection) {
            return $items->all();
        } elseif ($items instanceof CharcoalCollection) {
            return $items->objects();
        }

        return parent::getArrayableItems($items);
    }



    // Intercepted to always return base collections
    // =================================================================================================================

    /**
     * Get an array with the values of a given key.
     *
     * @param  string  $value
     * @param  string|null  $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($value, $key = null)
    {
        return $this->toBase()->pluck($value, $key);
    }

    /**
     * Get the keys of the collection items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function keys()
    {
        return $this->toBase()->keys();
    }

    /**
     * Zip the collection together with one or more arrays.
     *
     * @param  mixed ...$items
     * @return \Illuminate\Support\Collection
     */
    public function zip($items)
    {
        return call_user_func_array([$this->toBase(), 'zip'], func_get_args());
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collapse()
    {
        return $this->toBase()->collapse();
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param  int  $depth
     * @return \Illuminate\Support\Collection
     */
    public function flatten($depth = INF)
    {
        return $this->toBase()->flatten($depth);
    }

    /**
     * Flip the items in the collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function flip()
    {
        return $this->toBase()->flip();
    }



    // Satisfies QueueableCollection
    // =================================================================================================================

    /**
     * Get the type of the entities being queued.
     *
     * @return string|null
     */
    public function getQueueableClass()
    {
        if ($this->count() === 0) {
            return;
        }

        $class = get_class($this->first());

        $this->each(function ($model) use ($class) {
            if (get_class($model) !== $class) {
                throw new LogicException('Queueing collections with multiple model types is not supported.');
            }
        });

        return $class;
    }

    /**
     * Get the identifiers for all of the entities.
     *
     * @return array
     */
    public function getQueueableIds()
    {
        return $this->modelKeys();
    }
}
