<?php

namespace Charcoal\Support\Model;

use Exception;
use Charcoal\Factory\FactoryInterface;

/**
 * Provides object model factory features.
 */
trait ManufacturableModelTrait
{
    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    private $modelFactory;

    /**
     * Set an object model factory.
     *
     * @param FactoryInterface $factory The model factory, to create objects.
     * @return self
     */
    protected function setModelFactory(FactoryInterface $factory)
    {
        $this->modelFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the object model factory.
     *
     * @throws Exception If the model factory was not previously set.
     * @return FactoryInterface
     */
    public function modelFactory()
    {
        if (!isset($this->modelFactory)) {
            throw new Exception(
                sprintf('Model Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->modelFactory;
    }
}
