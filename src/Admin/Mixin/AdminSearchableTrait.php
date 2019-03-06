<?php

namespace Charcoal\Support\Admin\Mixin;

// From charcoal-property
use Charcoal\Property\ObjectProperty;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;
use Charcoal\Translator\Translator;

/**
 * Default implementation, as Trait, of the AdminSearchableInterface
 */
trait AdminSearchableTrait
{
    /**
     * @var Translation|string|null
     */
    private $adminSearchKeywords;

    /**
     * @return Translation|null|string
     */
    public function adminSearchKeywords()
    {
        return $this->adminSearchKeywords;
    }

    /**
     * @param Translation|null|string $adminSearchKeywords The admin search keywords.
     * @return self
     */
    public function setAdminSearchKeywords($adminSearchKeywords)
    {
        $this->adminSearchKeywords = $adminSearchKeywords;

        return $this;
    }

    /**
     * Generate the Searchable column data.
     * @return void
     */
    private function generateAdminSearchable()
    {
        $searchable = [];

        $searchableProperties = $this->metadata()->get('admin.search.properties');

        foreach ($searchableProperties as $propIdent => $data) {
            $property = $this->property($propIdent);
            $value = $this[$propIdent];

            if (!$value) {
                continue;
            }

            if ($property instanceof ObjectProperty) {
                $objType = $property->objType();
                $searchProps = $data['properties'];

                if (!$searchProps) {
                    continue;
                }

                if ($property->multiple()) {
                    if (!count($value)) {
                        continue;
                    }

                    $values = implode($property->multipleSeparator(), $value);
                    $this->collectionLoader()
                         ->setModel($objType)
                         ->addFilter(['condition' => sprintf('FIND_IN_SET(id, "%s")', $cat)])
                         ->setCallback(function ($item) use (&$searchable, $searchProps) {
                             foreach ($searchProps as $searchProp) {
                                 foreach ($this->translator()->availableLocales() as $lang) {
                                     $searchable[$lang][] = $this->translator()->translation($item->get($searchProp))[$lang];
                                 }
                             }
                         })
                         ->load();

                    continue;
                }

                $model = $this->modelFactory()
                               ->create($objType)
                               ->load($value);

                foreach ($searchProps as $searchProp) {
                    foreach ($this->translator()->availableLocales() as $lang) {
                        $searchable[$lang][] = $this->translator()->translation($model->get($searchProp))[$lang];
                    }
                }

                continue;
            }

            foreach ($this->translator()->availableLocales() as $lang) {
                $searchable[$lang][] = $this->translator()->translation($property->parseVal($value))[$lang];
            }
        }

        $this->setAdminSearchKeywords($searchable);

        return;
    }

    /**
     * Retrieve the model factory.
     *
     * @throws RuntimeException If the model factory is missing.
     * @return FactoryInterface
     */
    abstract public function modelFactory();

    /**
     * Retrieve the model collection loader.
     *
     * @throws RuntimeException If the collection loader is missing.
     * @return CollectionLoader
     */
    abstract public function collectionLoader();

    /**
     * Retrieve the translator service.
     *
     * @throws RuntimeException If the translator is accessed before having been set.
     * @return Translator
     */
    abstract protected function translator();
}
