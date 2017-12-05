<?php

namespace Charcoal\Support\Cms\Metatag;

use InvalidArgumentException;

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
     * @throws InvalidArgumentException If the document title structure is invalid.
     * @return string
     */
    final public function documentTitle()
    {
        $parts = $this->documentTitleParts();
        if (array_diff_key([ 'title' => true, 'site' => true ], $parts)) {
            throw new InvalidArgumentException(
                'The document title parts requires at least a "title" and a "site"'
            );
        }

        return $this->parseDocumentTitle($parts);
    }

    /**
     * Retrieve the site name.
     *
     * @return string|null
     */
    abstract public function siteName();

    /**
     * Retrieve the title of the page (from the context).
     *
     * @return string|null
     */
    abstract public function title();

    /**
     * Retrieve the current object relative to the context.
     *
     * @return \Charcoal\Model\ModelInterface
     */
    abstract public function contextObject();
}
