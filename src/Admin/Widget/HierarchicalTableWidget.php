<?php

namespace Charcoal\Support\Admin\Widget;

// From `charcoal-core`
use Charcoal\Model\ModelInterface;

// From `charcoal-property`
use Charcoal\Property\PropertyInterface;

// From `charcoal-admin`
use Charcoal\Admin\Widget\TableWidget;

// From 'charcoal-support'
use Charcoal\Support\Model\HierarchicalCollection;
use Charcoal\Support\Admin\Property\Display\HierarchicalDisplay;

/**
 * The hierarchical table widget displays a collection in a tabular (table) format.
 */
class HierarchicalTableWidget extends TableWidget
{
    /**
     * Provide a template to fullfill UIItem interface.
     *
     * @return string
     */
    public function template()
    {
        return 'charcoal/admin/widget/table';
    }

    /**
     * Setup the property's display value before its assigned to the object row.
     *
     * This method is useful for classes using this trait.
     *
     * @param  ModelInterface    $object   The current row's object.
     * @param  PropertyInterface $property The current property.
     * @return void
     */
    protected function setupDisplayPropertyValue(
        ModelInterface $object,
        PropertyInterface $property
    ) {
        parent::setupDisplayPropertyValue($object, $property);

        if ($this->display instanceof HierarchicalDisplay) {
            $this->display->setCurrentLevel($object->hierarchyLevel());
        }
    }

    /**
     * Sort the objects before they are displayed as rows.
     *
     * @see \Charcoal\Admin\Ui\CollectionContainerTrait::sortObjects()
     * @return array
     */
    public function sortObjects()
    {
        $collection = new HierarchicalCollection($this->objects(), false);
        $collection
            ->setPage($this->page())
            ->setNumPerPage($this->numPerPage())
            ->sortTree();

        return $collection->all();
    }
}
