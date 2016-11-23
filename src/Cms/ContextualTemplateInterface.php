<?php

namespace Charcoal\Support\Cms;

// From PSR-7
use Psr\Http\Message\UriInterface;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

/**
 * Defines a template intrinsically related to routing.
 */
interface ContextualTemplateInterface
{
    /**
     * Set the current renderable object relative to the context.
     *
     * @param  ModelInterface $context The context / view to render the template with.
     * @return self
     */
    public function setContextObject(ModelInterface $context);

    /**
     * Retrieve the current object relative to the context.
     *
     * This method is meant to be reimplemented in a child template controller
     * to return the resolved object that the module considers "the context".
     *
     * @return ModelInterface|null
     */
    public function contextObject();

    /**
     * Retrieve the current URI of the context.
     *
     * @return string|UriInterface
     */
    public function currentUrl();
}
