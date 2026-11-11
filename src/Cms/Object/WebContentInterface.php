<?php

namespace Charcoal\Support\Cms\Object;

use Charcoal\Object\RoutableInterface;
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
