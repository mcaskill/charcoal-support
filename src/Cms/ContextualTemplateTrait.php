<?php

namespace Charcoal\Support\Cms;

use InvalidArgumentException;

// From PSR-7
use Psr\Http\Message\UriInterface;

// From 'charcoal-core'
use Charcoal\Model\Model;
use Charcoal\Model\ModelInterface;

// From 'charcoal-translation'
use Charcoal\Translation\TranslationString;

/**
 * Additional utilities for the routing.
 */
trait ContextualTemplateTrait
{
    /**
     * The current rendering / data context.
     *
     * @var ModelInterface|null
     */
    protected $contextObject;

    /**
     * The route group path (base URI).
     *
     * @var string|null
     */
    protected $routeGroup;

    /**
     * The route endpoint path (path URI).
     *
     * @var string|null
     */
    protected $routeEndpoint;

    /**
     * The class name of the section model.
     *
     * A fully-qualified PHP namespace. Used for the model factory.
     *
     * @var string
     */
    protected $genericContextClass = Model::class;

    /**
     * Set the class name of the generic context model.
     *
     * @param  string $className The class name of the section model.
     * @throws InvalidArgumentException If the class name is not a string.
     * @return AbstractPropertyDisplay Chainable
     */
    public function setGenericContextClass($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(
                'Generic context class name must be a string.'
            );
        }

        $this->genericContextClass = $className;

        return $this;
    }

    /**
     * Retrieve the class name of the generic context model.
     *
     * @return string
     */
    public function genericContextClass()
    {
        return $this->genericContextClass;
    }

    /**
     * Set the current renderable object relative to the context.
     *
     * @param  ModelInterface $context The context / view to render the template with.
     * @return self
     */
    public function setContextObject(ModelInterface $context)
    {
        $this->contextObject = $context;

        return $this;
    }

    /**
     * Retrieve the current object relative to the context.
     *
     * This method is meant to be reimplemented in a child template controller
     * to return the resolved object that the module considers "the context".
     *
     * @return ModelInterface|null
     */
    public function contextObject()
    {
        if ($this->contextObject === null) {
            $this->contextObject = $this->createGenericContext();
        }

        return $this->contextObject;
    }

    /**
     * Create a generic object relative to the context.
     *
     * @return ModelInterface
     */
    protected function createGenericContext()
    {
        $obj = $this->modelFactory()->create($this->genericContextClass());

        $baseUrl = $this->baseUrl();
        if ($this->routeEndpoint) {
            $endpoint = new TranslationString($this->routeEndpoint);
            foreach ($endpoint->all() as $lang => $trans) {
                $uri = $baseUrl->withPath($trans);

                if ($this->setRouteGroup) {
                    $uri = $uri->withBasePath($this->setRouteGroup->fallback($lang));
                }

                $basePath = $uri->getBasePath();
                $path = $uri->getPath();
                $path = $basePath . '/' . ltrim($path, '/');

                $endpoint[$lang] = $path;
            }
        } else {
            $endpoint = null;
        }

        $obj['url']   = $endpoint;
        $obj['title'] = $this->title();

        return $obj;
    }

    /**
     * Retrieve the current URI of the context.
     *
     * @return string|UriInterface
     */
    public function currentUrl()
    {
        $context = $this->contextObject();
        $uri     = $this->baseUrl();

        if ($context) {
            return $uri->withPath(strval($context['url']));
        }

        return $uri;
    }

    /**
     * Append a path to the base URI.
     *
     * @param  string $path The base path.
     * @return self
     */
    public function setRouteGroup($path)
    {
        if (TranslationString::isTranslatable($path)) {
            $this->setRouteGroup = new TranslationString($path);

            foreach ($this->setRouteGroup->all() as $lang => $path) {
                $this->setRouteGroup[$lang] = trim($path, '/');
            }
        } else {
            $this->setRouteGroup = null;
        }

        return $this;
    }

    /**
     * Append a path to the URI.
     *
     * @param  string $path The main path.
     * @return self
     */
    public function setRouteEndpoint($path)
    {
        if (TranslationString::isTranslatable($path)) {
            $this->routeEndpoint = new TranslationString($path);

            foreach ($this->routeEndpoint->all() as $lang => $path) {
                $this->routeEndpoint[$lang] = trim($path, '/');
            }
        } else {
            $this->routeEndpoint = null;
        }

        return $this;
    }

    /**
     * Retrieve the base URI of the project.
     *
     * @return UriInterface|null
     */
    abstract public function baseUrl();

    /**
     * Retrieve the title of the page (from the context).
     *
     * @return string
     */
    abstract public function title();

    /**
     * Retrieve the object model factory.
     *
     * @return \Charcoal\Factory\FactoryInterface
     */
    abstract public function modelFactory();
}
