<?php

namespace Charcoal\Support\Cms\Metatag;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Additional utilities for the HTML document.
 */
trait DocumentTrait
{
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
     * @param  array $parts The document title parts.
     * @return string
     */
    protected function parseDocumentTitle(array $parts)
    {
        $parts = array_map('strval', $parts);
        $parts = array_filter($parts, function ($segment, $key) use ($parts) {
            if (empty($segment) && !is_numeric($segment)) {
                return false;
            }

            if ($key !== 'site') {
                if (!empty($parts['site']) || is_numeric($parts['site'])) {
                    return (false === strpos($segment, $parts['site']));
                }
            }

            return $segment;
        }, ARRAY_FILTER_USE_BOTH);
        $delim = sprintf(' %s ', $this->documentTitleSeparator());
        $title = implode($delim, $parts);

        return $title;
    }

    /**
     * Retrieve the document title.
     *
     * @return string
     */
    final public function documentTitle()
    {
        $parts = array_merge([ 'title' => '', 'site' => '' ], $this->documentTitleParts());

        return $this->parseDocumentTitle($parts);
    }

    /**
     * Retrieve the site name.
     *
     * @return Translation|string|null
     */
    abstract public function siteName();

    /**
     * Retrieve the title of the page (from the context).
     *
     * @return Translation|string|null
     */
    abstract public function title();

    /**
     * Retrieve the current object relative to the context.
     *
     * @return ModelInterface
     */
    abstract public function contextObject();
}
