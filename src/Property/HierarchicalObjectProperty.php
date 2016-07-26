<?php

namespace Charcoal\Support\Property;

// From Pimple
use \Pimple\Container;

// From 'charcoal-property'
use Charcoal\Property\GenericProperty;
use Charcoal\Property\ObjectProperty;

// From 'charcoal-support'
use Charcoal\Support\Admin\Property\Display\HierarchicalDisplay;
use Charcoal\Support\Model\HierarchicalCollection;
use Charcoal\Support\Property\ManufacturablePropertyDisplayTrait;

/**
 * Hierarchical Object Property
 */
class HierarchicalObjectProperty extends ObjectProperty
{
    use ManufacturablePropertyDisplayTrait;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setPropertyDisplayFactory($container['property/display/factory']);
    }

    /**
     * Retrieve the choices as a linear list.
     *
     * @return array
     */
    public function flatChoices()
    {
        return parent::choices();
    }
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

    /**
     * Render the choice from the object.
     *
     * @param ModelInterface|ViewableInterface $object The object or view to render as a label.
     * @return string
     */
    protected function objPattern($object)
    {
        $pattern  = parent::objPattern($object);

        $property = $this->propertyFactory()->create(GenericProperty::class);
        $property->setVal($pattern);

        $display = $this->propertyDisplayFactory()->create(HierarchicalDisplay::class);
        $display->setProperty($property);
        $display->setCurrentLevel($object->hierarchyLevel());

        return $display->displayVal();
    }
}
