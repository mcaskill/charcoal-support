<?php

namespace Charcoal\Support\Email;

use RuntimeException;
use Pimple\Container;
use Charcoal\Factory\FactoryInterface;

/**
 * Provides email factory features.
 */
trait ManufacturableEmailTrait
{
    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    private $emailFactory;

    /**
     * Set an email model factory.
     *
     * @param FactoryInterface $factory The email factory, to create emails.
     * @return self
     */
    protected function setEmailFactory(FactoryInterface $factory)
    {
        $this->emailFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the email model factory.
     *
     * @throws RuntimeException If the model factory was not previously set.
     * @return FactoryInterface
     */
    protected function emailFactory()
    {
        if (!isset($this->emailFactory)) {
            throw new RuntimeException(
                sprintf('Email Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->emailFactory;
    }
}
