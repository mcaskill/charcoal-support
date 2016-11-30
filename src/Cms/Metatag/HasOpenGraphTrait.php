<?php

namespace Charcoal\Support\Cms\Metatag;

/**
 * Basic implementation of {@see HasOpenGraphInterface}
 *
 * @uses \Charcoal\Support\Property\ParsableValueTrait
 */
trait HasOpenGraphTrait
{
    /**
     * The object's OpenGraph {@link http://ogp.me/#types content type}.
     *
     * Depending on the type you specify, other properties may also be required.
     *
     * @var string|null
     */
    protected $opengraphType;

    /**
     * The path to object's thumbnail.
     *
     * @var string|null
     */
    protected $opengraphImage;

    /**
     * Retrieve the object's {@link https://developers.facebook.com/docs/reference/opengraph/ OpenGraph type},
     * for the "og:type" meta-property.
     *
     * @return string
     */
    public function opengraphType()
    {
        return $this->opengraphType;
    }

    /**
     * Set object's OpenGraph content type.
     *
     * @see    HasOpenGraphInterface::DEFAULT_OPENGRAPH_TYPE The default content type.
     * @param  string|string[] $type The OpenGraph content type.
     * @return self
     */
    public function setOpengraphType($type)
    {
        $this->opengraphType = $type;

        return $this;
    }

    /**
     * Retrieve the URL to the object's social image for the "og:image" meta-property.
     *
     * @return string
     */
    public function opengraphImage()
    {
        return $this->opengraphImage;
    }

    /**
     * Set the object's OpenGraph thumbnail image path.
     *
     * @param  string|string[] $path A path to an image.
     * @return self
     */
    public function setOpengraphImage($path)
    {
        $this->opengraphImage = $this->parseAsTranslatable($path);

        return $this;
    }
}
