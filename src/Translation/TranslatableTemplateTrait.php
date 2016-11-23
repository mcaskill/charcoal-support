<?php

namespace Charcoal\Support\Translation;

use LogicException;

use Mustache_LambdaHelper as LambdaHelper;

// From 'charcoal-translation'
use Charcoal\Polyglot\MultilingualAwareTrait;
use Charcoal\Translation\TranslationString;
use Charcoal\Translation\Catalog\CatalogAwareTrait;
use Charcoal\Translation\TranslatorAwareTrait;

/**
 * An implementation of the `MultilingualAwareInterface`.
 *
 * For objects needed to interact with the applications's `LanguageManager`.
 *
 * Behavioral difference:
 *
 * • Most methods access an instance of `LanguageManager` through
 *   the `\Charcoal\Translation\TranslatorAwareInterface`.
 *
 * @see \Charcoal\Translation\ConfigurableTranslationTrait For a similar delegated trait.
 */
trait TranslatableTemplateTrait
{
    use MultilingualAwareTrait;
    use CatalogAwareTrait;
    use TranslatorAwareTrait;

    /**
     * @var array The template's contextual translation reference.
     */
    protected $translations;

    /**
     * Retrieve the manager's list of available languages.
     *
     * @param  (LanguageInterface|string)[] $langs If an array of one or more lanagues is provided,
     *     the method returns a subset of the manager's available languages (if any).
     * @return string[] An array of available languages.
     */
    public function languages(array $langs = [])
    {
        return $this->translator()->languages($langs);
    }

    /**
     * Retrieve the manager's list of available language identifiers.
     *
     * @param  (LanguageInterface|string)[] $langs If an array of one or more lanagues is provided,
     *     the method returns a subset of the manager's available languages (if any).
     * @return string[] An array of available language identifiers.
     */
    public function availableLanguages(array $langs = [])
    {
        return $this->translator()->availableLanguages($langs);
    }

    /**
     * Assign a list of languages to the manager.
     *
     * When updating the list of available languages, the default and current language
     * is checked against the new list. If the either doesn't exist in the new list,
     * the first of the new set is used as the default language and the current language
     * is reset to NULL (which falls onto the default language).
     *
     * @param  (LanguageInterface|string)[] $langs An array of zero or more language
     *     objects or language identifiers to set on the manager.
     *
     *     If an empty array is provided, the method should consider this a request
     *     to empty the languages store.
     * @return MultilingualAwareInterface Chainable
     */
    public function setLanguages(array $langs = [])
    {
        throw new LogicException(
            sprintf(
                'Cannot add languages from translatable template [%s]',
                get_class($this)
            )
        );

        return $this;
    }

    /**
     * Add an available language to the manager.
     *
     * @param  LanguageInterface|array|string $lang A language object or identifier.
     * @return MultilingualAwareInterface Chainable
     */
    public function addLanguage($lang)
    {
        throw new LogicException(
            sprintf(
                'Cannot add language from translatable template [%s]',
                get_class($this)
            )
        );

        return $this;
    }

    /**
     * Remove an available language from the manager.
     *
     * @param  LanguageInterface|string $lang A language object or identifier.
     * @return MultilingualAwareInterface Chainable
     */
    public function removeLanguage($lang)
    {
        throw new LogicException(
            sprintf(
                'Cannot remove language from translatable template [%s]',
                get_class($this)
            )
        );

        return $this;
    }

    /**
     * Retrieve an available language from the manager.
     *
     * @param  LanguageInterface|string $lang A language object or identifier.
     * @return LanguageInterface|string|null A language object or identifier.
     */
    public function language($lang)
    {
        return $this->translator()->language($lang);
    }

    /**
     * Determine if the manager has a specified language.
     *
     * @param  LanguageInterface|string $lang A language object or identifier.
     * @return boolean Whether the language is available.
     */
    public function hasLanguage($lang)
    {
        return $this->translator()->hasLanguage($lang);
    }

    /**
     * Retrieve the manager's default language.
     *
     * The default language acts as a fallback when the current language
     * is not available. This is especially useful when dealing with translations.
     *
     * @return string A language identifier.
     */
    public function defaultLanguage()
    {
        return $this->translator()->defaultLanguage();
    }

    /**
     * Set the manager's default language.
     *
     * Must be one of the available languages assigned to the manager.
     *
     * @param  LanguageInterface|string|null $lang A language object or identifier.
     * @return MultilingualAwareInterface Chainable
     */
    public function setDefaultLanguage($lang = null)
    {
        $this->translator()->setDefaultLanguage($lang);

        return $this;
    }

    /**
     * Retrieve the manager's current language.
     *
     * The current language acts as the first to be used when interacting
     * with data in a context where the language isn't explicitly specified.
     *
     * @return string A language identifier.
     */
    public function currentLanguage()
    {
        return $this->translator()->currentLanguage();
    }

    /**
     * Set the manager's current language.
     *
     * Must be one of the available languages assigned to the manager.
     *
     * Defaults to resetting the manager's current language to NULL,
     * (which falls onto the default language).
     *
     * @param  LanguageInterface|string|null $lang A language object or identifier.
     * @return MultilingualAwareInterface Chainable
     */
    public function setCurrentLanguage($lang = null)
    {
        $this->translator()->setCurrentLanguage($lang);

        return $this;
    }
}
