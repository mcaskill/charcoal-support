<?php

namespace Charcoal\Support\Admin\Widget;

// From `charcoal-core`
use Charcoal\Model\ModelInterface;

// From `charcoal-property`
use Charcoal\Property\PropertyInterface;

// From `charcoal-admin`
use Charcoal\Admin\Widget\TableWidget;

// From 'charcoal-support'
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
        $objects = $this->objects();

        $level   = 0;
        $count   = 0;
        $pageNum = $this->page();
        $perPage = $this->numPerPage();

        $sortedObjects = [];
        $rootObjects   = [];
        $childObjects  = [];

        foreach ($objects as $object) {
            // Repair bad hierarchy.
            if ($object->hasMaster() && $object->master()->id() === $object->id()) {
                $object->setMaster(0);
                $object->update([ 'master' ]);
            }

            if ($object->hasMaster()) {
                $childObjects[$object->master()->id()][] = $object;
            } else {
                $rootObjects[] = $object;
            }
        }

        $objects = &$rootObjects;

        if ($perPage < 1) {
            foreach ($objects as $object) {
                $object->level = $level;
                $sortedObjects[$object->id()] = $object;

                $count++;

                if (isset($childObjects[$object->id()])) {
                    $this->sortDescendantObjects(
                        $object,
                        $childObjects,
                        $count,
                        ($level + 1),
                        $sortedObjects
                    );
                }
            }
        } else {
            $start = (( $pageNum - 1 ) * $perPage);
            $end   = ($start + $perPage);

            foreach ($objects as $object) {
                if ($count >= $end) {
                    break;
                }

                if ($count >= $start) {
                    $object->level = $level;
                    $sortedObjects[$object->id()] = $object;
                }

                $count++;

                if (isset($childObjects[$object->id()])) {
                    $this->sortDescendantObjects(
                        $object,
                        $childObjects,
                        $count,
                        ($level + 1),
                        $sortedObjects
                    );
                }
            }

            // If we are on the last page, display orphaned descendants.
            if ($childObjects && $count < $end) {
                foreach ($childObjects as $orphans) {
                    foreach ($orphans as $descendants) {
                        if ($count >= $end) {
                            break;
                        }

                        if ($count >= $start) {
                            $descendants->level = 0;
                            $sortedObjects[$descendants->id()] = $descendants;
                        }

                        $count++;
                    }
                }
            }
        }

        return $sortedObjects;
    }

    /**
     * Given an object, display the nested hierarchy of descendants.
     *
     * @param ModelInterface   $parentObj     The parent object from which to append its descendants for display.
     * @param ModelInterface[] $childObjects  The list of descendants by parent object ID. Passed by reference.
     * @param integer          $count         The current count of objects to display, for pagination.
     *     Passed by reference.
     * @param integer          $level         The level directly below the $parentObj.
     * @param ModelInterface[] $sortedObjects The list of objects to be displayed. Passed by reference.
     */
    private function sortDescendantObjects(
        $parentObj,
        &$childObjects,
        &$count,
        $level,
        &$sortedObjects
    ) {
        $pageNum = $this->page();
        $perPage = $this->numPerPage();

        if ($perPage < 1) {
            foreach ($childObjects[$parentObj->id()] as $object) {
                if ($count === 0 && $object->hasMaster()) {
                    $myParents = [];
                    $myParent  = $object->master();
                    while ($myParent) {
                        $myParents[] = $myParent;

                        if (!$myParent->hasMaster()) {
                            break;
                        }

                        $myParent = $myParent->master();
                    }

                    $numParents = count($myParents);
                    while ($myParent = array_pop($myParents)) {
                        $myParent->level = ($level - $numParents);
                        $sortedObjects[$myParent->id()] = $myParent;
                        $numParents--;
                    }
                }

                $object->level = $level;
                $sortedObjects[$object->id()] = $object;

                $count++;

                if (isset($childObjects[$object->id()])) {
                    $this->sortDescendantObjects(
                        $object,
                        $childObjects,
                        $count,
                        ($level + 1),
                        $sortedObjects
                    );
                }
            }
        } else {
            $start = (( $pageNum - 1 ) * $perPage);
            $end   = ($start + $perPage);

            foreach ($childObjects[$parentObj->id()] as $object) {
                if ($count >= $end) {
                    break;
                }

                // If the page starts in a subtree, print the parents.
                if ($count === $start && $object->hasMaster()) {
                    $myParents = [];
                    $myParent  = $object->master();
                    while ($myParent) {
                        $myParents[] = $myParent;

                        if (!$myParent->hasMaster()) {
                            break;
                        }

                        $myParent = $myParent->master();
                    }

                    $numParents = count($myParents);
                    while ($myParent = array_pop($myParents)) {
                        $myParent->level = ($level - $numParents);
                        $sortedObjects[$myParent->id()] = $myParent;
                        $numParents--;
                    }
                }

                if ($count >= $start) {
                    # $sortedObjects[$object->id()] = $level;
                    $object->level   = $level;
                    $sortedObjects[$object->id()] = $object;
                }

                $count++;

                if (isset($childObjects[$object->id()])) {
                    $this->sortDescendantObjects(
                        $object,
                        $childObjects,
                        $count,
                        ($level + 1),
                        $sortedObjects
                    );
                }
            }
        }

        // Required in order to keep track of orphans
        unset($childObjects[$parentObj->id()]);
    }
}
