<?php

namespace Charcoal\Support\Cms;

use ArrayIterator;
use Traversable;

// From Pimple
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-object'
use Charcoal\Object\RoutableInterface;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Cms\AbstractWebTemplate;
use Charcoal\Support\Cms\LocaleAwareTrait;

/**
 * Multilingual Template Controller
 *
 * This class acts as an enhancer to Charcoal's abstract template.
 * All templates for this project should inherit this class.
 */
abstract class AbstractL10nTemplate extends AbstractWebTemplate
{
    use LocaleAwareTrait;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setLocales($container['locales/manager']->locales());
    }
}
