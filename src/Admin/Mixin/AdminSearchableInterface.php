<?php

namespace Charcoal\Support\Admin\Mixin;

use Charcoal\Translator\Translation;

/**
 * Interface AdminSearchableInterface
 *
 * @package Charcoal\Support\Admin\Mixin
 */
interface AdminSearchableInterface
{

    /**
     * @return Translation|null|string
     */
    public function adminSearchKeywords();

    /**
     * @param Translation|null|string $adminSearchKeywords The admin search keywords.
     * @return self
     */
    public function setAdminSearchKeywords($adminSearchKeywords);
}
