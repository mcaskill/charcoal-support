<?php

namespace Charcoal\Support\Admin\Mixin;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Default implementation, as Trait, of the AdminSearchableInterface
 */
trait AdminSearchableTrait
{
    /**
     * @var Translation|string|null
     */
    private $adminSearchKeywords;

    /**
     * @return Translation|null|string
     */
    public function adminSearchKeywords()
    {
        return $this->adminSearchKeywords;
    }

    /**
     * @param Translation|null|string $adminSearchKeywords The admin search keywords.
     * @return self
     */
    public function setAdminSearchKeywords($adminSearchKeywords)
    {
        $this->adminSearchKeywords = $adminSearchKeywords;

        return $this;
    }
}
