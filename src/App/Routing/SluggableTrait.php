<?php

namespace Charcoal\Support\App\Routing;

use UnexpectedValueException;

// From 'charcoal-base'
use Charcoal\Object\RoutableTrait;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

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
     * @var Translation|string|null
     */
    protected $url;

    /**
     * Retrieve the object's URI.
     *
     * @param  string|null $lang If object is multilingual, return the object route for the specified locale.
     * @return Translation|string|null
     */
    public function url($lang = null)
    {
        if ($this->url === null) {
            if (!$this->id()) {
                return null;
            }

            $this->url = $this->generateUri();
        }

        if ($lang && $this->url instanceof Translation) {
            return $this->url[$lang];
        }

        return strval($this->url);
    }

    /**
     * Generate a URL slug from the object's URL slug pattern.
     *
     * @throws UnexpectedValueException If the slug is empty.
     * @return Translation|string|null
     */
    public function generateSlug()
    {
        $translator = $this->translator();
        $languages  = $translator->availableLocales();
        $patterns   = $this->slugPattern();
        $curSlug    = $this->slug();
        $newSlug    = [];

        $origLang = $translator->getLocale();
        foreach ($languages as $lang) {
            $pattern = $patterns[$lang];

            $this->translator()->setLocale($lang);
            if ($this->isSlugEditable() && isset($curSlug[$lang]) && strlen($curSlug[$lang])) {
                $newSlug[$lang] = $curSlug[$lang];
            } else {
                $newSlug[$lang] = $this->generateRoutePattern($pattern);
                if (!strlen($newSlug[$lang])) {
                    throw new UnexpectedValueException(sprintf(
                        'The slug is empty; the pattern is "%s"',
                        $pattern
                    ));
                }
            }
        }
        $this->translator()->setLocale($origLang);

        return $this->translator()->translation($newSlug);
    }

    /**
     * Ignore ObjectRoute Generation.
     *
     * @param  mixed $slug Slug by langs.
     * @return void
     */
    protected function generateObjectRoute($slug = null)
    {
        // Do Nothing
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
