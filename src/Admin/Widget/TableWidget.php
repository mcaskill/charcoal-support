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
     * Retrieve the widget type.
     *
     * @return string
     */
    public function type()
    {
        return 'charcoal/admin/widget/table';
    }

    /**
     * Properties to display in collection template, and their order, as set in object metadata
     *
     * @return  FormPropertyWidget         Generator function
     */
    public function collectionProperties()
    {
        $props = $this->properties();

        foreach ($props as $propertyIdent => $property) {
            $propertyMetadata = $props[$propertyIdent];

            $p = $this->propertyFactory()->create($propertyMetadata['type']);
            $p->setIdent($propertyIdent);
            $p->setData($propertyMetadata);

            $options = $this->viewOptions($propertyIdent);
            $classes = $this->parsePropertyCellClasses($p);

            $column = [
                'label' => trim($p->label())
            ];

            if (!isset($column['attr'])) {
                $column['attr'] = [];
            }

            if (isset($options['attr'])) {
                $column['attr'] = array_merge($column['attr'], $options['attr']);
            }

            if (isset($classes)) {
                if (isset($column['attr']['class'])) {
                    if (is_string($classes)) {
                        $classes = explode(' ', $column['attr']['class']);
                    }

                    if (is_string($column['attr']['class'])) {
                        $column['attr']['class'] = explode(' ', $column['attr']['class']);
                    }

                    $column['attr']['class'] = array_unique(array_merge($column['attr']['class'], $classes));
                } else {
                    $column['attr']['class'] = $classes;
                }

                unset($classes);
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
        $cell    = $this->parseCollectionPropertyCell($object, $property, $propertyValue);
        $ident   = $property->ident();
        $options = $this->viewOptions($ident);
        $classes = $this->parsePropertyCellClasses($property, $object);

        if (!isset($cell['attr'])) {
            $cell['attr'] = [];
        }

        if (isset($options['attr'])) {
            $cell['attr'] = array_merge($cell['attr'], $options['attr']);
        }

        if (isset($classes)) {
            if (isset($cell['attr']['class'])) {
                if (is_string($classes)) {
                    $classes = explode(' ', $cell['attr']['class']);
                }

                if (is_string($cell['attr']['class'])) {
                    $cell['attr']['class'] = explode(' ', $cell['attr']['class']);
                }

                $cell['attr']['class'] = array_unique(array_merge($cell['attr']['class'], $classes));
            } else {
                $cell['attr']['class'] = $classes;
            }

            unset($classes);
        }

        $cell['attr'] = $this->htmlAttributes($cell['attr']);

        return $cell;
    }
}
