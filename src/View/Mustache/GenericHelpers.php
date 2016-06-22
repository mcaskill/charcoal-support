<?php

namespace Charcoal\Support\View\Mustache;

use DateTime;
use DateTimeImmutable;
use Pimple\Container;
use Mustache_LambdaHelper as LambdaHelper;
use Charcoal\View\Mustache\Helpers\GenericHelpers as GenericCharcoalHelpers;
use Charcoal\Translation\Catalog\CatalogAwareInterface;
use Charcoal\Translation\Catalog\CatalogAwareTrait;

/**
 * Enhanced Mustache helpers for rendering.
 */
class GenericHelpers extends GenericCharcoalHelpers implements
    CatalogAwareInterface
{
    use CatalogAwareTrait;

    /**
     * A string concatenation of inline `<script>` elements.
     *
     * @var string $js
     */
    private static $now;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        $this->setCatalog($container['translator/catalog']);
    }

    /**
     * Retrieve the collection of helpers.
     *
     * @return array
     */
    public function toArray()
    {
        $helpers = parent::toArray();

        $helpers['_t']        = $this->translate();
        $helpers['preRender'] = $this->preRender();

        return $helpers;
    }

    /**
     * Pre-render, with Mustache, a block of text.
     *
     * Would be called `render()` but currently used by {@see Charcoal_Base::render())
     * for Charcoal-style pattern remplacements.
     *
     * @return callable
     */
    public function preRender()
    {
        /**
         * Returns a Closure that provides a way to recursively render any Mustache tags
         * within the block of text that will be rendered.
         *
         * Without this `pre_render()` method, Mustache would only render the initial tag
         * and ignore any Mustache tags within.
         *
         * @var string       $text   Text to translate.
         * @var LambdaHelper $helper For rendering strings in the current context.
         *
         * @return string
         */
        return function ($text, LambdaHelper $helper) {
            return $helper->render($text);
        };
    }

    /**
     * Retrieve a translation using a Mustache lambda tag.
     *
     * If there is no translation, or the text catalog isn't loaded, the original text is returned.
     *
     * Usage:
     *
     * ```mustache
     * {{# _t }}Hello, {{ planet }}!{{/ _t }}
     * ```
     *
     * Any contained Mustache tags will be rendered after the translation is returned.
     *
     * @return callable
     */
    public function translate()
    {
        $catalog = $this->catalog();

        /**
         * Returns a Closure that translates the $text and then renders any Mustache tags within.
         *
         * @var string       $text   Text to translate.
         * @var LambdaHelper $helper For rendering strings in the current context.
         *
         * @return string
         */
        return function ($text, LambdaHelper $helper) use ($catalog) {
            $text = $catalog->translate($text);

            /** @var string Render any Mustache tags in the translation. */
            return $helper->render($text);
        };
    }
}
