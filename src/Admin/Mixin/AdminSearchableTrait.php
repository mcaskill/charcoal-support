<?php

namespace Charcoal\Support\Admin\Mixin;

// From 'charcoal-property'
use Charcoal\Property\ObjectProperty;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 *
 */
trait AdminSearchableTrait
{
    /**
     * The search keywords for the Charcoal Admin.
     *
     * @var Translation|string|null
     */
    private $adminSearchKeywords;

    /**
     * Get the search keywords for the Charcoal Admin.
     *
     * @return Translation|string|null
     */
    public function getAdminSearchKeywords()
    {
        return $this->adminSearchKeywords;
    }

    /**
     * Set the search keywords for the Charcoal Admin.
     *
     * @param  mixed $searchKeywords The admin search keywords.
     * @return self
     */
    public function setAdminSearchKeywords($searchKeywords)
    {
        $this->adminSearchKeywords = $this->property('adminSearchKeywords')->parseVal($searchKeywords);

        return $this;
    }

    /**
     * Generate this object's search keywords for the Charcoal Admin.
     *
     * @return void
     */
    protected function generateAdminSearchable()
    {
        $translator = $this->translator();
        $languages  = $translator->availableLocales();

        $searchKeywords = [];

        $searchableProperties = $this->metadata()->get('admin.search.properties');

        foreach ($searchableProperties as $propIdent => $searchData) {
            $property = $this->property($propIdent);
            $objValue    = $this[$propIdent];

            if (empty($objValue)) {
                continue;
            }

            if ($property instanceof ObjectProperty) {
                if (empty($searchData['properties'])) {
                    continue;
                }

                $searchProps  = $searchData['properties'];
                $fillKeywords = function ($relObj) use (&$searchKeywords, $searchProps, $translator, $languages) {
                    foreach ($searchProps as $searchProp) {
                        foreach ($languages as $lang) {
                            $relValue = $relObj->get($searchProp);
                            $searchKeywords[$lang][] = $translator->translate($relValue, [], null, $lang);
                        }
                    }
                };

                $relObjType = $property->objType();
                if ($property->multiple()) {
                    if (!count($objValue)) {
                        continue;
                    }

                    $relObjIds = implode($property->multipleSeparator(), $objValue);
                    $this->collectionLoader()
                         ->setModel($relObjType)
                         ->addFilter([
                            'condition' => sprintf('FIND_IN_SET(objTable.id, "%s")', $relObjIds),
                         ])
                         ->setCallback($fillKeywords)
                         ->load();
                } else {
                    $relObj = $this->modelFactory()->create($relObjType)->load($objValue);
                    $fillKeywords($relObj);
                }
            } else {
                foreach ($languages as $lang) {
                    $objValue = $property->parseVal($objValue);
                    $searchKeywords[$lang][] = $translator->translate($objValue, [], null, $lang);
                }
            }
        }

        $this->setAdminSearchKeywords($searchKeywords);
    }

    /**
     * Retrieve the property instance for the given property.
     *
     * @param  string $propertyIdent The property (ident) to get.
     * @return \Charcoal\Property\PropertyInterface
     */
    abstract public function property($propertyIdent);

    /**
     * Retrieve the model factory.
     *
     * @return \Charcoal\Factory\FactoryInterface
     */
    abstract protected function modelFactory();

    /**
     * Retrieve the model collection loader.
     *
     * @return \Charcoal\Loader\CollectionLoader
     */
    abstract protected function collectionLoader();

    /**
     * Retrieve the translator service.
     *
     * @return \Charcoal\Translator\Translator
     */
    abstract protected function translator();
}
