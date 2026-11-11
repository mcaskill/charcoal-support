<?php

namespace Charcoal\Support\Cms\Object;

use Charcoal\Config\DelegatesAwareInterface;
use Charcoal\Model\ModelInterface;

/**
 * Describes a configuration container model.
 */
interface SettingsInterface extends
    ModelInterface,
    DelegatesAwareInterface
{
    /**
     * Add data to settings, replacing existing values with the same data key.
     *
     * @param  array|\Traversable $data Datas to merge.
     * @return SettingsInterface
     */
    public function merge($data);
}
