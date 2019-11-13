<?php

namespace Charcoal\Support\Cms\Object;

// From 'charcoal-object'
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
}
