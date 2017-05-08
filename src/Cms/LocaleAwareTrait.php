<?php

namespace Charcoal\Support\Cms;

use InvalidArgumentException;

// From 'charcoal-translator'
use Charcoal\Translator\LocalesManager;

/**
 * Provides awareness of locales.
 *
 * ## Requirements
 *
 * - Translator (e.g., {@see \Charcoal\Translator\TranslatorAwareTrait})
 */
trait LocaleAwareTrait
{
    /**
     * Available languages as defined by the application configset.
     *
     * @var array
     */
    protected $availableLanguages;

    /**
     * Store the processed link structures to translations
     * for the current route, if any.
     *
     * @var array
     */
    private $alternateTranslations;

    /**
     * Store the application's locales manager.
     *
     * @var LocalesManager
     */
    private $localesManager;

    /**
     * Set the class name of the section model.
     *
     * @param  LocalesManager $manager The locales manager.
     * @return self
     */
    protected function setLocalesManager(LocalesManager $manager)
    {
        $this->localesManager = $manager;

        return $this;
    }

    /**
     * Retrieve the locales manager.
     *
     * @return LocalesManager
     */
    protected function localesManager()
    {
        return $this->localesManager;
    }

    /**
     * Set the available languages.
     *
     * @param  array $languages The list of languages.
     * @return self
     */
    protected function setAvailableLanguages(array $languages)
    {
        $this->availableLanguages = $languages;

        return $this;
    }

    /**
     * Retrieve the translator service.
     *
     * @return array
     */
    protected function availableLanguages()
    {
        return $this->availableLanguages;
    }

    /**
     * Render the alternate translations associated with the current route.
     *
     * This method _excludes_ the current route's canonical URI.
     *
     * @return Generator|null
     */
    public function alternateTranslations()
    {
        if ($this->alternateTranslations === null) {
            $context  = $this->contextObject();
            $origLang = $this->currentLanguage();

            foreach ($this->availableLanguages() as $lang) {
                if ($lang === $origLang) {
                    continue;
                }

                $this->localesManager()->setCurrentLocale($lang);

                $this->alternateTranslations[$lang] = $this->formatAlternateTranslation($context, $lang);
            }

            $this->localesManager()->setCurrentLocale($origLang);
        }

        foreach ($this->alternateTranslations as $lang => $trans) {
            yield $lang => $trans;
        }
    }

    /**
     * Format an alternate translation for the given translatable model.
     *
     * Note: The application's locale is already modified and will be reset
     * after processing all available languages.
     *
     * @param  mixed  $context The translated {@see \Charcoal\Model\ModelInterface model}
     *     or array-accessible structure.
     * @param  string $lang    The currently iterated language.
     * @return array Returns a link structure.
     */
    protected function formatAlternateTranslation($context, $lang)
    {
        $isModel    = ($context instanceof ModelInterface);
        $isRoutable = ($context instanceof RoutableInterface);

        $link = [
            'id'       => ($isModel ? $obj['id'] : $this->templateName()),
            'title'    => ($isModel ? (string)$obj['title'] : $this->title()),
            'url'      => ($isRoutable ? $obj->url($lang) : ($this->currentUrl() ? : $lang)),
            'hreflang' => $lang
        ];

        return $link;
    }

    /**
     * Determine if there exists alternate translations associated with the current route.
     *
     * @return boolean
     */
    public function hasAlternateTranslations()
    {
        return count(iterator_to_array($this->alternateTranslations())) > 0;
    }

    /**
     * Retrieve the translator service.
     *
     * @see    \Charcoal\Translator\TranslatorAwareTrait
     * @return \Charcoal\Translator\Translator
     */
    abstract protected function translator();

    /**
     * Retrieve the template's identifier.
     *
     * @return string
     */
    abstract public function templateName();

    /**
     * Retrieve the title of the page (from the context).
     *
     * @return string
     */
    abstract public function title();

    /**
     * Retrieve the current URI of the context.
     *
     * @return \Psr\Http\Message\UriInterface|string
     */
    abstract public function currentUrl();

    /**
     * Retrieve the current object relative to the context.
     *
     * @return \Charcoal\Model\ModelInterface|null
     */
    abstract public function contextObject();
}
