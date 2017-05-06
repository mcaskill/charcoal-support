<?php

namespace Charcoal\Support\Cms;

// From Pimple
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-object'
use Charcoal\Object\RoutableInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-app'
use Charcoal\App\Template\AbstractTemplate as CharcoalTemplate;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\App\Template\SupportTrait as TemplateSupportTrait;
use Charcoal\Support\Cms\ContextualTemplateInterface;
use Charcoal\Support\Cms\ContextualTemplateTrait;
use Charcoal\Support\Cms\Metatag\DocumentTrait;
use Charcoal\Support\Cms\Metatag\HasMetatagInterface;
use Charcoal\Support\Cms\Metatag\HasOpenGraphInterface;
use Charcoal\Support\Cms\Metatag\HasTwitterCardInterface;
use Charcoal\Support\Cms\Metatag\MetadataAwareInterface;

/**
 * Hypertext Template Controller
 *
 * This class acts as an enhancer to Charcoal's abstract template.
 * All templates for this project should inherit this class.
 */
abstract class AbstractWebTemplate extends CharcoalTemplate implements
    ContextualTemplateInterface,
    MetadataAwareInterface
{
    use ContextualTemplateTrait;
    use DocumentTrait;
    use TemplateSupportTrait;
    use TranslatorAwareTrait;

    /**
     * The default image for social media sharing.
     *
     * @var string
     */
    const DEFAULT_SOCIAL_MEDIA_IMAGE = '';

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
     * @var array
     */
    private $localesManager;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setDebug($container['debug']);
        $this->setTranslator($container['translator']);
        $this->setAvailableLanguages($container['locales/available-languages']);
        $this->localesManager = $container['locales/manager'];
        $this->setAppConfig($container['config']);
        $this->setBaseUrl($container['base-url']);
    }

    /**
     * Retrieve the title of the page (the context).
     *
     * @return Translation|string|null
     */
    public function title()
    {
        $context = $this->contextObject();

        if ($context instanceof ModelInterface) {
            return $context['title'];
        }

        return '';
    }

    /**
     * Retrieve the current URI of the context.
     *
     * @return UriInterface|string|null
     */
    public function currentUrl()
    {
        $context = $this->contextObject();

        if ($context && isset($context['url'])) {
            return $this->createAbsoluteUrl($context['url']);
        }

        return null;
    }

    /**
     * Retrieve the canonical URI of the object.
     *
     * @return string|null
     */
    public function currentLocale()
    {
        $langCode = $this->translator()->getLocale();
        $locales  = $this->translator()->locales();
        if (isset($locales[$langCode])) {
            $locale = $locales[$langCode];
            if (isset($locale['locale'])) {
                return $locale['locale'];
            } else {
                return $langCode;
            }
        }

        return null;
    }



    // Metadata
    // =========================================================================

    /**
     * Retrieve the canonical URI of the object.
     *
     * @return string|null
     */
    public function canonicalUrl()
    {
        return $this->currentUrl();
    }

    /**
     * Parse the document title parts.
     *
     * @return string[]
     */
    protected function documentTitleParts()
    {
        return [
            'title' => $this->metaTitle(),
            'site'  => $this->siteName(),
        ];
    }

    /**
     * Retrieve the name or title of the object.
     *
     * @return string|null
     */
    public function metaTitle()
    {
        $context = $this->contextObject();
        $title   = null;

        if ($context instanceof HasMetatagInterface) {
            $title = $context['meta_title'];
        }

        if (!$title) {
            $title = $this->title();
        }

        return $title;
    }

    /**
     * Retrieve the description of the object.
     *
     * @return string|null
     */
    public function metaDescription()
    {
        $context = $this->contextObject();

        $desc = null;
        if ($context instanceof HasMetatagInterface) {
            $desc = $context['meta_description'];
        }

        if (!$desc) {
            $desc = $this->fallbackMetaDescription();
        }

        return $desc;
    }

    /**
     * Hook called as a fallback if no meta description is set on the object.
     *
     * This method should be extended by child controllers.
     *
     * @return null
     */
    protected function fallbackMetaDescription()
    {
        return null;
    }

    /**
     * Retrieve the URL to the image representing the object.
     *
     * @return string|null
     */
    public function metaImage()
    {
        $context = $this->contextObject();

        $img = null;
        if ($context instanceof HasMetatagInterface) {
            $img = $context['meta_image'];
        }

        if (!$img) {
            $img = $this->fallbackMetaImage();
        }

        return $this->resolveMetaImage($img);
    }

    /**
     * Hook called as a fallback if no meta image is set on the object.
     *
     * This method should be extended by child controllers.
     *
     * @return null
     */
    protected function fallbackMetaImage()
    {
        return null;
    }

    /**
     * Retrieve the URL to the image representing the object.
     *
     * @param  string|null $img A path to an image.
     * @return string|null
     */
    protected function resolveMetaImage($img = null)
    {
        if (!$img) {
            $img = static::DEFAULT_SOCIAL_MEDIA_IMAGE;
        }

        if ($img) {
            $uri = $this->baseUrl();
            return $uri->withPath(strval($img));
        }

        return null;
    }

    /**
     * Retrieve the object's {@link https://developers.facebook.com/docs/reference/opengraph/ OpenGraph type},
     * for the "og:type" meta-property.
     *
     * @return string|null
     */
    public function opengraphType()
    {
        $context = $this->contextObject();

        $type = null;

        if ($context instanceof HasMetatagInterface) {
            $type = $context['opengraph_type'];
        }

        if (!$type) {
            $type = HasOpenGraphInterface::DEFAULT_OPENGRAPH_TYPE;
        }

        return $type;
    }

    /**
     * Retrieve the URL to the object's social image for the "og:image" meta-property.
     *
     * This method can fallback onto {@see MetadataInterface::defaultMetaImage()}
     * for a common image between web annotation schemas.
     *
     * @return string|null
     */
    public function opengraphImage()
    {
        $context = $this->contextObject();

        $img = null;
        if ($context instanceof HasOpenGraphInterface) {
            $img = $context['opengraph_image'];
        }

        if (!$img) {
            $img = $this->fallbackOpengraphImage();
        }

        if ($img) {
            $uri = $this->baseUrl();
            return $uri->withPath(strval($img));
        }

        return $this->metaImage();
    }

    /**
     * Hook called as a fallback if no social image is set on the object.
     *
     * This method should be extended by child controllers.
     *
     * @return null
     */
    protected function fallbackOpengraphImage()
    {
        return null;
    }

    /**
     * Retrieve the object's {@link https://dev.twitter.com/cards/types card type},
     * for the "twitter:card" meta-property.
     *
     * @return string|null
     */
    public function twitterCardType()
    {
        $context = $this->contextObject();

        $type = null;

        if ($context instanceof HasTwitterCardInterface) {
            $type = $context['twitter_card_type'];
        }

        if (!$type) {
            $type = HasTwitterCardInterface::DEFAULT_TWITTER_CARD_TYPE;
        }

        return $type;
    }

    /**
     * Retrieve the URL to the object's social image for the "twitter:image" meta-property.
     *
     * @return string|null
     */
    public function twitterCardImage()
    {
        $context = $this->contextObject();

        $img = null;
        if ($context instanceof HasTwitterCardInterface) {
            $img = $context['twitter_card_image'];
        }

        if ($img) {
            $uri = $this->baseUrl();
            return $uri->withPath(strval($img));
        }

        return $this->metaImage();
    }



    // Polylingual
    // =================================================================================================================

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
     * Retrieve the locale shortcode
     *
     * @return string
     */
    public function currentLanguage()
    {
        return $this->translator()->getLocale();
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

            $origLang   = $this->currentLanguage();

            foreach ($this->availableLanguages() as $lang) {
                if ($lang === $origLang) {
                    continue;
                }

                $this->localesManager->setCurrentLocale($lang);

                $data = [
                    'id'    => ($isModel) ? $context['id'] : $this->templateName(),
                    'title' => ($isModel) ? (string)$context['title'] : $this->pageTitle(),
                    'url'   => ($isRoutable) ? $context->url($lang) : ($this->currentUrl()) ? : $lang,
                    'hreflang' => $lang
                ];

                $this->alternateTranslations[$lang] = $data;
            }

            $this->localesManager->setCurrentLocale($origLang);
        }

        foreach ($this->alternateTranslations as $lang => $trans) {
            yield $lang => $trans;
        }
    }
}
