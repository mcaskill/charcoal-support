<?php

namespace Charcoal\Support\Cms\Metatag;

/**
 * Basic implementation of {@see HasMetatagInterface}
 *
 * @uses \Charcoal\Support\Property\ParsableValueTrait
 */
trait HasMetatagTrait
{
    /**
     * Name of the object.
     *
     * @var string|null
     */
    public $metaTitle;

    /**
     * Description of the object.
     *
     * @var string|null
     */
    public $metaDescription;

    /**
     * Thumbnail of the object.
     *
     * @var string|null
     */
    public $metaImage;

    /**
     * Retrieve the object's name or title.
     *
     * In a basic metadata structure, this should appear
     * in the `<title>` element.
     *
     * With the OpenGraph Metadata—as it should appear
     * in the graph—for the "og:title" meta-property.
     *
     * @return string|null
     */
    public function metaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set the object's name or title.
     *
     * @param  string|string[] $name The name for the object.
     * @return self
     */
    public function setMetaTitle($name)
    {
        $this->metaTitle = $this->parseAsTranslatable($name);

        return $this;
    }

    /**
     * Retrieve the object's description.
     *
     * @return string|null
     */
    public function metaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set the object's description.
     *
     * @param  string|string[] $description A short description for the object.
     * @return self
     */
    public function setMetaDescription($description)
    {
        $this->metaDescription = $this->parseAsTranslatable($description);

        return $this;
    }

    /**
     * Retrieve the object's thumbnail or preview image.
     *
     * @return string|null
     */
    public function metaImage()
    {
        return $this->metaImage;
    }

    /**
     * Set the object's thumbnail or preview image.
     *
     * @param  string|string[] $path A path to an image.
     * @return self
     */
    public function setMetaImage($path)
    {
        $this->metaImage = $this->parseAsTranslatable($path);

        return $this;
    }
}
