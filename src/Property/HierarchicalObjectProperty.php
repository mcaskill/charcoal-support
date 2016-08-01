<?php

namespace Charcoal\Support\Property;

// From Pimple
use \Pimple\Container;

// From 'charcoal-property'
use Charcoal\Property\GenericProperty;
use Charcoal\Property\ObjectProperty;

// From 'charcoal-support'
use Charcoal\Support\Model\HierarchicalCollection;

/**
 * Hierarchical Object Property
 */
class HierarchicalObjectProperty extends ObjectProperty
{
    /**
     * Retrieve the choices segmented as a tree.
     *
     * @return array
     */
    public function choices()
    {
        $loader = $this->collectionLoader();
        $orders = $this->orders();
        if ($orders) {
            $loader->setOrders($orders);
        }

        $filters = $this->filters();
        if ($filters) {
            $loader->setFilters($filters);
        }

        $collection = new HierarchicalCollection($loader->load(), false);
        $collection
            ->setPage($loader->page())
            ->setNumPerPage($loader->numPerPage())
            ->sortTree();

        $choices = [];
        foreach ($collection as $obj) {
            $choice = $this->choice($obj);

            if ($choice !== null) {
                $choices[$obj->id()] = $choice;
            }
        }

        return $choices;
    }
}
