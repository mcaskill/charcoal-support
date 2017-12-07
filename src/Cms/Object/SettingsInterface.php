<?php

namespace Charcoal\Support\Cms\Object;

// From 'charcoal-config'
use Charcoal\Config\DelegatesAwareInterface;

// From 'charcoal-core'
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
