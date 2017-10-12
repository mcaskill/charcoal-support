<?php

namespace Charcoal\Support\Property;

// From Pimple
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-object'
use Charcoal\Object\HierarchicalInterface;

// From 'charcoal-property'
use Charcoal\Property\ObjectProperty;

// From 'charcoal-support'
use Charcoal\Support\Model\HierarchicalCollection;

/**
 * Hierarchical Object Property
 */
class HierarchicalObjectProperty extends ObjectProperty
{
    /**
     * Retrieve the available choice structures, segmented as a tree.
     *
     * @return array
     */
    public function choices()
    {
        $choices = [];

        $proto = $this->proto();
        if (!$proto->source()->tableExists()) {
            return $choices;
        }

        $loader = $this->collectionModelLoader();

        $collection = new HierarchicalCollection($loader->load(), false);
        $collection->setPage($loader->page())
                   ->setNumPerPage($loader->numPerPage())
                   ->sortTree();

        return $this->parseChoices($collection);
    }

    /**
     * Parse the given value into a choice structure.
     *
     * @param  ModelInterface $obj An object to format.
     * @return array Returns a choice structure.
     */
    protected function parseChoice(ModelInterface $obj)
    {
        $choice = parent::parseChoice($obj);

        if (property_exists($obj, 'auxiliary') && $obj->auxiliary) {
            $choice['parent'] = true;
        } elseif ($obj instanceof HierarchicalInterface && $obj->hasMaster()) {
            $choice['group'] = parent::parseChoice($obj->master());
        } else {
            $choice['group'] = null;
        }

        if (is_callable([ $obj, 'name' ])) {
            $choice['title'] = $obj->name();
        } elseif (is_callable([ $obj, 'label' ])) {
            $choice['title'] = $obj->label();
        } elseif (is_callable([ $obj, 'title' ])) {
            $choice['title'] = $obj->title();
        }

        return $choice;
    }
}
