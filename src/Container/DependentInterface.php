<?php

namespace Charcoal\Support\Container;

use Pimple\Container;

/**
 * Defines an object with dependencies from a DI container.
 */
interface DependentInterface
{
    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container);
}
