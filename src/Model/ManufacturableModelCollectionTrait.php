<?php

namespace Charcoal\Support\Model;

use RuntimeException;
use Charcoal\Loader\CollectionLoader;
use Charcoal\Factory\FactoryInterface;

/**
 * Provides model collection features.
 */
trait ManufacturableModelCollectionTrait
{
    /**
     * Store the collection loader.
     *
     * @var CollectionLoader
     */
    protected $collectionLoader;

    /**
     * Store the factory instance.
     *
     * @var FactoryInterface
     */
    protected $collectionLoaderFactory;

    /**
     * Set a model collection loader.
     *
     * @param  CollectionLoader $loader The model collection loader.
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
     * @throws RuntimeException If the collection loader is missing.
     * @return CollectionLoader
     */
    public function collectionLoader()
    {
        if (!isset($this->collectionLoader)) {
            throw new RuntimeException(sprintf(
                'Collection Loader is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->collectionLoader;
    }

    /**
     * Set a model collection loader factory.
     *
     * @param  FactoryInterface $factory The factory to create model collection loaders.
     * @return self
     */
    protected function setCollectionLoaderFactory(FactoryInterface $factory)
    {
        $this->collectionLoaderFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the collection loader factory.
     *
     * @throws RuntimeException If the collection loader factory is missing.
     * @return FactoryInterface
     */
    public function collectionLoaderFactory()
    {
        if (!isset($this->collectionLoaderFactory)) {
            throw new RuntimeException(sprintf(
                'Collection Loader Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->collectionLoaderFactory;
    }

    /**
     * Create a model collection loader with optional constructor arguments and a post-creation callback.
     *
     * @param  array|null    $args     Optional. Constructor arguments.
     * @param  callable|null $callback Optional. Called at creation.
     * @return CollectionLoader
     */
    public function createCollectionLoaderWith(array $args = null, callable $callback = null)
    {
        $factory = $this->collectionLoaderFactory();

        return $factory->create($factory->defaultClass(), $args, $callback);
    }

    /**
     * Create a model collection loader.
     *
     * @return CollectionLoader
     */
    public function createCollectionLoader()
    {
        $factory = $this->collectionLoaderFactory();

        return $factory->create($factory->defaultClass());
    }
}
