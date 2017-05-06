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
     * Available languages as defined by the config.
     *
     * @var array
     */
    protected $availableLanguages;

    /**
     * @var array
     */
    private $alternateTranslations;

    /**
     * @var LocalesManager
     */
    private $localesManager;

    /**
     * Set the class name of the section model.
     *
     * @param  string $className The class name of the section model.
     * @throws InvalidArgumentException If the given object is invalid.
     * @return self
     */
    public function setLocalesManager($manager)
    {
        if (!$manager instanceof LocalesManager) {
            throw new InvalidArgumentException(sprintf(
                'Locales Manager must be an instance of %s; received %s',
                LocalesManager::class,
                (is_object($manager) ? get_class($manager) : gettype($manager))
            ));
        }

        $this->localesManager = $manager;

        return $this;
    }

    /**
     * Retrieve the locales manager.
     *
     * @return
     */
    public function localesManager()
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
            $context = $this->contextObject();
            $isModel = ($context instanceof ModelInterface);
            $isRoutable = ($context instanceof RoutableInterface);

            $origLang = $this->currentLanguage();

            foreach ($this->availableLanguages() as $lang) {
                if ($lang === $origLang) {
                    continue;
                }

                $this->localesManager()->setCurrentLocale($lang);

                $data = [
                    'id'    => ($isModel) ? $context['id'] : $this->templateName(),
                    'title' => ($isModel) ? (string)$context['title'] : $this->title(),
                    'url'   => ($isRoutable) ? $context->url($lang) : ($this->currentUrl()) ? : $lang,
                    'hreflang' => $lang
                ];

                $this->alternateTranslations[$lang] = $data;
            }

            $this->localesManager()->setCurrentLocale($origLang);
        }

        foreach ($this->alternateTranslations as $lang => $trans) {
            yield $lang => $trans;
        }
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
}
