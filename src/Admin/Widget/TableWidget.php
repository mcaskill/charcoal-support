<?php

namespace Charcoal\Support\Admin\Widget;

// From `charcoal-core`
use \Charcoal\Model\ModelInterface;

// From `charcoal-property`
use \Charcoal\Property\PropertyInterface;

// From `charcoal-admin`
use Charcoal\Admin\Widget\TableWidget as CharcoalTableWidget;

// From 'charcoal-support'
use \Charcoal\Support\View\HtmlableTrait;

/**
 *
 */
class TableWidget extends CharcoalTableWidget
{
    use HtmlableTrait;

    /**
     * Properties to display in collection template, and their order, as set in object metadata
     *
     * @return  FormPropertyWidget         Generator function
     */
    public function collectionProperties()
    {
        $columns = parent::collectionProperties();

        foreach ($columns as $column) {
            if (!isset($column['attr'])) {
                $column['attr'] = [];
            }

            if (isset($column['classes'])) {
                $column['attr']['class'] = $column['classes'];
                unset($column['classes']);
            }

            $column['attr'] = $this->htmlAttributes($column['attr']);

            yield $column;
        }
    }

    /**
     * Filter the property before its assigned to the object row.
     *
     * This method is useful for classes using this trait.
     *
     * @param  ModelInterface    $object        The current row's object.
     * @param  PropertyInterface $property      The current property.
     * @param  string            $propertyValue The property $key's display value.
     * @return array
     */
    protected function parsePropertyCell(
        ModelInterface $object,
        PropertyInterface $property,
        $propertyValue
    ) {
        $cell = parent::parsePropertyCell($object, $property, $propertyValue);

        if (!isset($cell['attr'])) {
            $cell['attr'] = [];
        }

        if (isset($cell['classes'])) {
            $cell['attr']['class'] = $cell['classes'];
            unset($cell['classes']);
        }

        $cell['attr'] = $this->htmlAttributes($cell['attr']);

        return $cell;
    }
}
