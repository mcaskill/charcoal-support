<?php

namespace Charcoal\Support\Email;

use Charcoal\Factory\FactoryInterface;
use RuntimeException;

/**
 * Provides email factory features.
 */
trait ManufacturableEmailTrait
{
    /**
     * Store the factory instance.
     *
     * @var \Charcoal\Factory\GenericFactory
     */
    protected $emailFactory;

    /**
     * Set an email model factory.
     *
     * @param  \Charcoal\Factory\GenericFactory $factory The factory to create emails.
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
     * @return \Charcoal\Factory\GenericFactory
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
