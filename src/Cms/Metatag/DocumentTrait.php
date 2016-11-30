<?php

namespace Charcoal\Support\Cms\Metatag;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

/**
 * Additional utilities for the HTML document.
 */
trait DocumentTrait
{
    /**
     * Retrieve the site name.
     *
     * @return string|null
     */
    public function siteName()
    {
        return 'Charcoal Project';
    }

    /**
     * Parse the document title parts.
     *
     * @return string[]
     */
    protected function documentTitleParts()
    {
        return [
            'title' => $this->title(),
            'site'  => $this->siteName(),
        ];
    }

    /**
     * Retrieve the document title separator.
     *
     * @return string
     */
    protected function documentTitleSeparator()
    {
        return '—';
    }

    /**
     * Retrieve the document title.
     *
     * @return string
     */
    final public function documentTitle()
    {
        $parts = array_merge([ 'title' => '', 'site' => '' ], $this->documentTitleParts());
        $parts = array_filter($parts, function ($segment, $key) use ($parts) {
            if ($key !== 'site') {
                return (false === strpos($segment, $parts['site']));
            }

            return $segment;
        }, ARRAY_FILTER_USE_BOTH);
        $delim = sprintf(' %s ', $this->documentTitleSeparator());
        $title = implode($delim, $parts);

        return $title;
    }

    /**
     * Retrieve the title of the page (from the context).
     *
     * @return string
     */
    abstract public function title();

    /**
     * Retrieve the current object relative to the context.
     *
     * @return \Charcoal\Model\ModelInterface
     */
    abstract public function contextObject();
}
