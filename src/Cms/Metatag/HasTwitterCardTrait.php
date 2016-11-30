<?php

namespace Charcoal\Support\Cms\Metatag;

/**
 * Basic implementation of {@see HasTwitterCardInterface}
 *
 * @uses \Charcoal\Support\Property\ParsableValueTrait
 */
trait HasTwitterCardTrait
{
    /**
     * The object's Twitter card type.
     *
     * Depending on the type you specify, other properties may also be required.
     *
     * @var string|null
     */
    protected $twitterCardType;

    /**
     * The path to object's thumbnail.
     *
     * @var string|null
     */
    protected $twitterCardImage;

    /**
     * Retrieve the object's {@link https://dev.twitter.com/cards/types card type},
     * for the "twitter:card" meta-property.
     *
     * @return string|null
     */
    public function twitterCardType()
    {
        return $this->twitterCardType;
    }

    /**
     * Set object's Twitter card type or a fallback.
     *
     * @see    HasTwitterCardInterface::DEFAULT_TWITTER_CARD_TYPE The default card type.
     * @param  string|string[] $type The Twitter card type.
     * @return self
     */
    public function setTwitterCardType($type)
    {
        $this->twitterCardType = $type;

        return $this;
    }

    /**
     * Retrieve the URL to the object's social image for the "twitter:image" meta-property.
     *
     * @return string|null
     */
    public function twitterCardImage()
    {
        return $this->twitterCardImage;
    }

    /**
     * Set the object's Twitter Card thumbnail image path.
     *
     * @param  string|string[] $path A path to an image.
     * @return self
     */
    public function setTwitterCardImage($path)
    {
        $this->twitterCardImage = $this->parseAsTranslatable($path);

        return $this;
    }
}
