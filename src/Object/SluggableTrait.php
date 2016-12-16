<?php

namespace Charcoal\Support\Object;

use UnexpectedValueException;

// From 'charcoal-translation'
use Charcoal\Translation\TranslationConfig;
use Charcoal\Translation\TranslationString;

// From 'charcoal-base'
use Charcoal\Object\RoutableTrait;

/**
 * Full implementation, as Trait, of the {@see \Charcoal\Object\RoutableInterface}.
 *
 * To ensure a unique slug across different routable models,
 * use {@see \Charcoal\Object\RoutableTrait}.
 */
trait SluggableTrait
{
    use RoutableTrait {
        RoutableTrait::generateSlug as generateUri;
    }

    /**
     * The object's URI path.
     *
     * @var string|null
     */
    protected $url;

    /**
     * Retrieve the object's URI.
     *
     * @param  string|null $lang If object is multilingual, return the object route for the specified locale.
     * @return TranslationString|string
     */
    public function url($lang = null)
    {
        if ($this->url === null) {
            $this->url = $this->generateUri();
        }

        if ($this->url instanceof TranslationString && $lang) {
            return $this->url->val($lang);
        }

        return strval($this->url);
    }

    /**
     * Generate a URL slug from the object's URL slug pattern.
     *
     * @throws UnexpectedValueException If the slug is empty.
     * @return TranslationString
     */
    public function generateSlug()
    {
        $translator = TranslationConfig::instance();
        $languages  = $translator->availableLanguages();
        $patterns   = $this->slugPattern();
        $curSlug    = $this->slug();
        $newSlug    = new TranslationString();

        $origLang = $translator->currentLanguage();
        foreach ($languages as $lang) {
            $pattern = $patterns[$lang];

            $translator->setCurrentLanguage($lang);
            if ($this->isSlugEditable() && isset($curSlug[$lang]) && strlen($curSlug[$lang])) {
                $newSlug[$lang] = $curSlug[$lang];
            } else {
                $newSlug[$lang] = $this->generateRoutePattern($pattern);
                if (!strlen($newSlug[$lang])) {
                    throw new UnexpectedValueException(
                        sprintf('The slug is empty. The pattern is "%s"', $pattern)
                    );
                }
            }
        }
        $translator->setCurrentLanguage($origLang);

        return $newSlug;
    }

    /**
     * Ignore ObjectRoute Generation.
     *
     * @param  mixed $slug Slug by langs.
     * @return void
     */
    protected function generateObjectRoute($slug = null)
    {
    }

    /**
     * Ignore the latest object route.
     *
     * @param  string|null $lang If object is multilingual, return the object route for the specified locale.
     * @return null
     */
    protected function getLatestObjectRoute($lang = null)
    {
        return null;
    }
}
