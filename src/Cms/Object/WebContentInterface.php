<?php

namespace Charcoal\Support\Cms\Object;

// From 'charcoal-base'
use Charcoal\Object\RoutableInterface;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Cms\Metatag\HasMetatagInterface;
use Charcoal\Support\Cms\Metatag\HasOpenGraphInterface;
use Charcoal\Support\Cms\Metatag\HasTwitterCardInterface;

/**
 * Defines a web page.
 */
interface WebContentInterface extends
    RoutableInterface,
    HasMetatagInterface,
    HasOpenGraphInterface,
    HasTwitterCardInterface
{
    /**
     * Determine if the object is locked or not.
     *
     * @return boolean
     */
    public function locked();
}
