<?php

namespace Charcoal\Support\Admin\Mixin;

/**
 *
 */
interface AdminSearchableInterface
{
    /**
     * Get the search keywords for the Charcoal Admin.
     *
     * @return string|null
     */
    public function getAdminSearchKeywords();

    /**
     * Set the search keywords for the Charcoal Admin.
     *
     * @param  mixed $searchKeywords The admin search keywords.
     * @return self
     */
    public function setAdminSearchKeywords($searchKeywords);
}
