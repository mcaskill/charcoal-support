<?php

namespace Charcoal\Support\Model;

use InvalidArgumentException;
use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

// From 'charcoal-core'
use Charcoal\Model\CollectionInterface;
use Charcoal\Model\ModelInterface;

/**
 * Hierarchical Model Collection
 *
 * This class is not recommended. Currently, only designed and used by
 * {@see \Charcoal\Support\Property\HierarchicalObjectProperty} and
 * {@see \Charcoal\Support\Admin\Widget\HierarchicalTableWidget}.
 */
class HierarchicalCollection implements
    ArrayAccess,
    Countable,
    IteratorAggregate
{
    /**
     * The objects contained in the collection.
     *
     * @var array
     */
    protected $objects = [];

    /**
     * The current page (slice).
     *
     * @var integer
     */
    protected $page = 0;

    /**
     * The number of objects per page (slice).
     *
     * @var integer
     */
    protected $numPerPage = 0;

    /**
     * Create a new hierarchically-sorted collection.
     *
     * @param  array|Traversable $objects The collection of objects.
     * @param  boolean           $sort    Whether to sort the collection immediately.
     * @return void
     */
    public function __construct($objects = [], $sort = true)
    {
        $this->objects = $this->getArrayableItems($objects);

        if ($sort) {
            $this->sortTree();
        }
    }

    /**
     * Sort the hierarchical collection of objects.
     *
     * @return self
     */
    public function sortTree()
    {
        $level   = 0;
        $count   = 0;
        $pageNum = $this->page();
        $perPage = $this->numPerPage();

        $sortedObjects = [];
        $rootObjects   = [];
        $childObjects  = [];

        foreach ($this->objects as $object) {
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

        if (empty($rootObjects) && !empty($childObjects)) {
            foreach ($childObjects as $parentId => $children) {
                $parentObj = $children[0]->master();
                $parentObj->auxiliary = true;

                $rootObjects[] = $parentObj;
            }
        }

        $this->objects = &$rootObjects;

        if ($perPage < 1) {
            foreach ($this->objects as $object) {
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

            foreach ($this->objects as $object) {
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

        $this->objects = $sortedObjects;

        return $this;
    }

    /**
     * Given an object, display the nested hierarchy of descendants.
     *
     * @param  ModelInterface   $parentObj     The parent object from which to append its descendants for display.
     * @param  ModelInterface[] $childObjects  The list of descendants by parent object ID. Passed by reference.
     * @param  integer          $count         The current count of objects to display, for pagination.
     *     Passed by reference.
     * @param  integer          $level         The level directly below the $parentObj.
     * @param  ModelInterface[] $sortedObjects The list of objects to be displayed. Passed by reference.
     * @return void
     */
    private function sortDescendantObjects(
        ModelInterface $parentObj,
        array &$childObjects,
        &$count,
        $level,
        array &$sortedObjects
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
        }

        // Required in order to keep track of orphans
        unset($childObjects[$parentObj->id()]);
    }

    /**
     * @param integer $page The current page. Start at 0.
     * @throws InvalidArgumentException If the parameter is not numeric or < 0.
     * @return Pagination (Chainable)
     */
    public function setPage($page)
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'Page number needs to be numeric.'
            );
        }

        $page = (int)$page;
        if ($page < 0) {
            throw new InvalidArgumentException(
                'Page number needs to be >= 0.'
            );
        }

        $this->page = $page;

        return $this;
    }

    /**
     * @return integer
     */
    public function page()
    {
        return $this->page;
    }

    /**
     * @param integer $num The number of results to retrieve, per page.
     * @throws InvalidArgumentException If the parameter is not numeric or < 0.
     * @return Pagination (Chainable)
     */
    public function setNumPerPage($num)
    {
        if (!is_numeric($num)) {
            throw new InvalidArgumentException(
                'Num-per-page needs to be numeric.'
            );
        }

        $num = (int)$num;

        if ($num < 0) {
            throw new InvalidArgumentException(
                'Num-per-page needs to be >= 0.'
            );
        }

        $this->numPerPage = $num;

        return $this;
    }

    /**
     * @return integer
     */
    public function numPerPage()
    {
        return $this->numPerPage;
    }

    /**
     * Get all of the objects in the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->objects;
    }

    /**
     * Results array of objects from Collection or Arrayable.
     *
     * @param  mixed $objs Collections or Arrayable objects.
     * @return array
     */
    protected function getArrayableItems($objs)
    {
        if (is_array($objs)) {
            return $objs;
        } elseif ($objs instanceof self) {
            return $objs->all();
        } elseif ($objs instanceof CollectionInterface) {
            return $objs->objects();
        } elseif ($objs instanceof \Arrayable) {
            return $objs->toArray();
        } elseif ($objs instanceof \Jsonable) {
            return json_decode($objs->toJson(), true);
        } elseif ($objs instanceof \JsonSerializable) {
            return $objs->jsonSerialize();
        }

        return (array)$objs;
    }


    // Methods to satisfy ArrayAccess
    // =========================================================================

    /**
     * Add or replace an object in the collection.
     *
     * @see    ArrayAccess::offsetSet() Satisfies interface.
     * @param  mixed $offset Optional. The ID of the object to replace.
     * @throws InvalidArgumentException If the value is not an instance of ModelInterface.
     * @param  mixed $value  The object to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof ModelInterface) {
            throw new InvalidArgumentException(
                'Collection value must be a ModelInterface object.'
            );
        }

        if ($offset === null) {
            $this->objects[] = $value;
        } else {
            $found = false;

            foreach ($this->objects as $i => $node) {
                if ($offset == $node->id()) {
                    $this->objects[$i] = $value;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->offsetSet(null, $value);
            }
        }
    }

    /**
     * Determine if an object exists in the collection by its ID.
     *
     * @see    ArrayAccess::offsetExists() Satisfies interface.
     * @param  mixed $offset The ID of the object to look up.
     * @return boolean
     */
    public function offsetExists($offset)
    {
        foreach ($this->objects as $node) {
            if ($offset == $node->id()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove an object in the collection by its ID.
     *
     * @see    ArrayAccess::offsetUnset() Satisfies interface.
     * @param  mixed $offset The ID of the object to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        foreach ($this->objects as $i => $node) {
            if ($offset == $node->id()) {
                unset($this->objects[$i]);
                return;
            }
        }
    }

    /**
     * Retrieve an object in the collection by its ID.
     *
     * @see    ArrayAccess::offsetGet() Satisfies interface.
     * @param  mixed $offset The ID of the object to look up.
     * @return ModelInterface|null
     */
    public function offsetGet($offset)
    {
        foreach ($this->objects as $node) {
            if ($offset == $node->id()) {
                return $node;
            }
        }

        return null;
    }


    // Methods to satisfy Countable
    // =========================================================================

    /**
     * Count the objects in the collection.
     *
     * @see Countable::count() Satisfies interface.
     * @return integer The number of objects in the Collection.
     */
    public function count()
    {
        return count($this->objects);
    }


    // Methods to satisfy IteratorAggregate
    // =========================================================================

    /**
     * Retrieve the collection as an iterator.
     *
     * @see IteratorAggregate::getIterator() Satisfies interface.
     * @return mixed
     */
    public function getIterator()
    {
        if (empty($this->objects)) {
            return new ArrayIterator();
        }

        return new ArrayIterator($this->objects);
    }
}
