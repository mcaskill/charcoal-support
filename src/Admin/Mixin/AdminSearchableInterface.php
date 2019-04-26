<?php

namespace Charcoal\Support\Admin\Mixin;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
interface AdminSearchableInterface
{
    /**
     * Get the search keywords for the Charcoal Admin.
     *
     * @return Translation|string|null
     */
    public function adminSearchKeywords();

    /**
     * Set the search keywords for the Charcoal Admin.
     *
     * @param  mixed $searchKeywords The admin search keywords.
     * @return self
     */
    public function setAdminSearchKeywords($searchKeywords);
}
