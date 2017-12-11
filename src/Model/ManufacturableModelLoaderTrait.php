<?php

namespace Charcoal\Support\Model;

use RuntimeException;

// From 'charcoal-core'
use Charcoal\Model\Service\ModelLoader;
use Charcoal\Model\Service\ModelLoaderBuilder;

/**
 * Provides model loader builder features.
 */
trait ManufacturableModelLoaderTrait
{
    /**
     * Store the builder instance.
     *
     * @var ModelLoaderBuilder
     */
    protected $modelLoaderBuilder;

    /**
     * Store all model loaders.
     *
     * @var ModelLoader[]
     */
    private static $modelLoaders = [];

    /**
     * Set an model loader builder.
     *
     * @param  ModelLoaderBuilder $builder The builder to create models.
     * @return void
     */
    protected function setModelLoaderBuilder(ModelLoaderBuilder $builder)
    {
        $this->modelLoaderBuilder = $builder;
    }

    /**
     * Retrieve the model loader builder.
     *
     * @throws RuntimeException If the model loader builder is missing.
     * @return FactoryInterface
     */
    protected function modelLoaderBuilder()
    {
        if (!isset($this->modelLoaderBuilder)) {
            throw new RuntimeException(sprintf(
                'Model Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->modelLoaderBuilder;
    }

    /**
     * Retrieve the object laoder for the given model.
     *
     * @param  string      $objType The target model.
     * @param  string|null $objKey  The target model's key to load by.
     * @throws InvalidArgumentException If the $objType or $objKey are invalid.
     * @return ModelLoader
     */
    protected function modelLoader($objType, $objKey = null)
    {
        if (!is_string($objType)) {
            throw new InvalidArgumentException(sprintf(
                'The object type must be a string, received %s',
                get_var_type($objType)
            ));
        }

        $key = $objKey;
        if ($key === null) {
            $key = '_';
        } elseif (!is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'The object property key must be a string, received %s',
                get_var_type($key)
            ));
        }

        if (isset(self::$modelLoaders[$objType][$key])) {
            return self::$modelLoaders[$objType][$key];
        }

        $builder = $this->modelLoaderBuilder();

        self::$modelLoaders[$objType][$key] = $builder($objType, $objKey);

        return self::$modelLoaders[$objType][$key];
    }
}
