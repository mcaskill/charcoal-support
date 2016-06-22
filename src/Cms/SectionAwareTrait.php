<?php

namespace Charcoal\Support\Cms;

use Charcoal\Cms\SectionInterface;

/**
 * Provides awareness for CMS a singular Section object.
 *
 * ## Requirements
 *
 * - A factory for creating section models (e.g., {@see \Charcoal\Model\ModelFactory})
 * - A section model implementing {@see \Charcoal\Cms\SectionInterface}.
 *
 */
trait SectionAwareTrait
{
    /**
     * The current section object.
     *
     * @var SectionInterface
     */
    private $section;

    /**
     * The class name of the section model.
     *
     * A fully-qualified PHP namespace. Used for the model factory.
     *
     * @var string
     */
    private $sectionClass = 'charcoal/cms/section';

    /**
     * Set the class name of the section model.
     *
     * @param  string $displayClass The class name of the section model.
     * @throws InvalidArgumentException If the class name is not a string.
     * @return AbstractPropertyDisplay Chainable
     */
    public function setSectionClass($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(
                'Section class name must be a string.'
            );
        }

        $this->sectionClass = $className;

        return $this;
    }

    /**
     * Retrieve the class name of the section model.
     *
     * @return string
     */
    public function sectionClass()
    {
        return $this->sectionClass;
    }

    /**
     * Create a new section object from the section model name.
     *
     * @return SectionInterface
     */
    private function createSection()
    {
        return $this->section = $this->modelFactory()->create($this->sectionClass());
    }

    /**
     * Assign the current section object.
     *
     * @see Charcoal\Object\RoutableInterface The section object is usually determined by the route.
     * @param SectionInterface|integer $obj A section object or ID.
     * @return self
     */
    public function setSection($obj)
    {
        if ($obj instanceof SectionInterface) {
            $this->section = $obj;
        } else {
            $this->createSection();

            $this->section->load($obj);
        }

        return $this;
    }

    /**
     * Retrieve the current section object.
     *
     * If no Section is set, an empty model is generated to prevent errors.
     *
     * @return SectionInterface
     */
    public function section()
    {
        if (!isset($this->section)) {
            $this->createSection();
        }

        return $this->section;
    }

    /**
     * Retrieve the template options from the current section.
     *
     * @return array
     */
    public function templateOptions()
    {
        $section = $this->section();

        if ($section->templateOptions()) {
            return json_decode($section->templateOptions());
        }

        return [];
    }
}
