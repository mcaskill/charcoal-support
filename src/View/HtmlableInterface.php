<?php

namespace Charcoal\Support\View;

/**
 * Defines an object as HTML-renderable.
 */
interface HtmlableInterface
{
    /**
     * Retrieve the viewable object as a string of HTML.
     *
     * @return string
     */
    public function toHtml();
}
