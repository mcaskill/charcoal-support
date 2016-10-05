<?php

namespace Charcoal\Support\Model;

use RuntimeException;
use Charcoal\Loader\CollectionLoader;

/**
 * Provides object model factory features.
 */
trait ManufacturableModelCollectionTrait
{
    /**
     * Store the collection loader for the current class.
     *
     * @var CollectionLoader
     */
    protected $collectionLoader;

    /**
     * Set a model collection loader.
     *
     * @param CollectionLoader $loader The collection loader.
     * @return self
     */
    protected function setCollectionLoader(CollectionLoader $loader)
    {
        $this->collectionLoader = $loader;

        return $this;
    }

    /**
     * Retrieve the model collection loader.
     *
     * @throws RuntimeException If the collection loader was not previously set.
     * @return CollectionLoader
     */
    public function collectionLoader()
    {
        if (!isset($this->collectionLoader)) {
            throw new RuntimeException(
                sprintf('Collection Loader is not defined for "%s"', get_class($this))
            );
        }

        return $this->collectionLoader;
    }
}
