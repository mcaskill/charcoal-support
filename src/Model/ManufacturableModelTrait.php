<?php

namespace Charcoal\Support\Model;

use Charcoal\Factory\FactoryInterface;
use RuntimeException;

/**
 * Provides model factory features.
 */
trait ManufacturableModelTrait
{
    /**
     * Store the factory instance.
     *
     * @var \Charcoal\Factory\GenericFactory
     */
    protected $modelFactory;

    /**
     * Set an model factory.
     *
     * @param  \Charcoal\Factory\GenericFactory $factory The factory to create models.
     * @return void
     */
    protected function setModelFactory(FactoryInterface $factory)
    {
        $this->modelFactory = $factory;
    }

    /**
     * Retrieve the model factory.
     *
     * @throws RuntimeException If the model factory is missing.
     * @return \Charcoal\Factory\GenericFactory
     */
    public function modelFactory()
    {
        if (!isset($this->modelFactory)) {
            throw new RuntimeException(sprintf(
                'Model Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->modelFactory;
    }
}
