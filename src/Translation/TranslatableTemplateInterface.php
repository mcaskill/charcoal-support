<?php

namespace Charcoal\Support\Translation;

// From 'charcoal-translation'
use Charcoal\Polyglot\MultilingualAwareInterface;
use Charcoal\Translation\Catalog\CatalogAwareInterface;
use Charcoal\Translation\TranslatorAwareInterface;

/**
 * Describes a translatable template controller.
 */
interface TranslatableTemplateInterface extends
    MultilingualAwareInterface,
    CatalogAwareInterface,
    TranslatorAwareInterface
{
}
