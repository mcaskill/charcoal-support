<?php

namespace Charcoal\Support\Cms\Object;

// From 'charcoal-core'
use Charcoal\Model\Model;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;

// From 'charcoal-property'
use Charcoal\Property\PropertyInterface;
use Charcoal\Property\SelectablePropertyInterface;
use Charcoal\Property\TemplateOptionsProperty;
use Charcoal\Property\TemplateProperty;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Exception\MissingDependencyException;

/**
 * Provides utilities for saving complex template options.
 */
trait CustomizableTemplateTrait
{
    /**
     * Track the state of the template options structure.
     *
     * @var boolean
     */
    private $areTemplateOptionsFinalized = false;

    /**
     * Retrieve the default template propert(y|ies).
     *
     * @return string[]
     */
    protected function defaultTemplateProperties()
    {
        return [ 'template_ident' ];
    }

    /**
     * Retrieve the template's structure interface(s).
     *
     * @see    TemplateProperty::__toString()
     * @see    \Charcoal\Admin\Widget\FormGroup\TemplateOptionsFormGroup::finalizeStructure()
     * @param  PropertyInterface|string ...$properties The properties to lookup.
     * @throws MissingDependencyException If the trait does not implement {@see TemplateableInterface::attachments}.
     * @return string[]|null
     */
    protected function extractTemplateInterfacesFrom(...$properties)
    {
        if (!$this instanceof TemplateableInterface) {
            throw new MissingDependencyException($this, TemplateableInterface::class);
        }

        $interfaces = [];
        foreach ($properties as $property) {
            if (!$property instanceof PropertyInterface) {
                $property = $this->property($property);
            }

            $key = $property->ident();
            $val = $this->propertyValue($key);
            if ($property instanceof SelectablePropertyInterface) {
                if ($property->hasChoice($val)) {
                    $choice = $property->choice($val);
                    $keys   = [ 'controller', 'template', 'class' ];
                    foreach ($keys as $key) {
                        if (isset($choice[$key])) {
                            $interfaces[] = $choice[$key];
                            break;
                        }
                    }
                }
            } else {
                $interfaces[] = $val;
            }
        }

        return $interfaces;
    }

    /**
     * Prepare the template options (structure) for use.
     *
     * @param  (PropertyInterface|string)[]|null $properties The template properties to parse.
     * @return boolean
     */
    public function prepareTemplateOptions(array $properties = null)
    {
        if ($properties === null) {
            $properties = $this->defaultTemplateProperties();
        }

        $interfaces = $this->extractTemplateInterfacesFrom(...$properties);
        if (empty($interfaces)) {
            return false;
        }

        $this->property('template_options')->addStructureInterfaces($interfaces);

        return true;
    }

    /**
     * Save the template options structure.
     *
     * @todo Once {@see \Charcoal\Translator\Translation} is integrated
     *     into charcoal property package.
     * @param  (PropertyInterface|string)[]|null $properties The template properties to parse.
     * @return void
     */
    protected function saveTemplateOptions(array $properties = null)
    {
        if ($properties === null) {
            $properties = $this->defaultTemplateProperties();
        }

        $this->prepareTemplateOptions($properties);

        $prop = $this->property('template_options');
        if ($prop->structureModelClass() === Model::class) {
            $struct = $this->propertyValue('template_options');
            $struct = $prop->structureVal($struct);
            foreach ($struct->properties() as $propertyIdent => $property) {
                $val = $struct[$propertyIdent];
                if ($property->l10n()) {
                    $val = $this->translator()->translation($struct[$propertyIdent]);
                }

                $struct[$propertyIdent] = $property->save($val);
            }
        }
    }

    /**
     * Retrieve the processed template options.
     *
     * @return mixed
     */
    public function getTemplateOptions()
    {
        if ($this->areTemplateOptionsFinalized === false) {
            $this->areTemplateOptionsFinalized = true;
            $this->prepareTemplateOptions();
        }

        $key  = 'template_options';
        $prop = $this->property($key);
        $val  = $this->propertyValue($key);

        return $prop->structureVal($val);
    }
}
