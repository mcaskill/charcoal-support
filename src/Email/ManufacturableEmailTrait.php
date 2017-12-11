<?php

namespace Charcoal\Support\Email;

use RuntimeException;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

/**
 * Provides email factory features.
 */
trait ManufacturableEmailTrait
{
    /**
     * Store the factory instance.
     *
     * @var FactoryInterface
     */
    protected $emailFactory;

    /**
     * Set an email model factory.
     *
     * @param  FactoryInterface $factory The factory to create emails.
     * @return void
     */
    protected function setEmailFactory(FactoryInterface $factory)
    {
        $this->emailFactory = $factory;
    }

    /**
     * Retrieve the email model factory.
     *
     * @throws RuntimeException If the model factory is missing.
     * @return FactoryInterface
     */
    protected function emailFactory()
    {
        if (!isset($this->emailFactory)) {
            throw new RuntimeException(sprintf(
                'Email Factory is not defined for [%s]',
                get_class($this)
            ));
        }

        return $this->emailFactory;
    }
}
