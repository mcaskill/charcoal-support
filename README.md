# Charcoal Support

Support package providing various recurring utilities for [Charcoal][charcoal-core] projects.

## Requirements

| Prerequisite     | How to check  | How to install |
| ---------------- | ------------- | -------------- |
| PHP >= 5.6.x     | `php -v`      | [php.net](//php.net/manual/en/install.php)
| Composer 1.0.0   | `composer -v` | [getcomposer.org](//getcomposer.org/)
| Charcoal 2017-02 |               |

See [composer.json](blob/master/composer.json) for depenencides.

## Installation

```shell
composer require mcaskill/charcoal-support
```

## What's inside?

-   `Charcoal\Support\Container\DependentInterface`  
    for standardized dependency injection.
-   `Charcoal\Support\Property\ParsableValueTrait`  
    for parsing/casting various types of [Charcoal Property][charcoal-property] values.
-   `Charcoal\Support\Model\Collection`  
    for an enhanced version of [Charcoal Core][charcoal-core]'s basic collection class.
-   `Charcoal\Support\Model\HierarchicalCollection`

#### Factories

-   `Charcoal\Support\Email\ManufacturableEmailTrait`
-   `Charcoal\Support\Model\ManufacturableMetadataTrait`
-   `Charcoal\Support\Model\ManufacturableModelCollectionTrait`
-   `Charcoal\Support\Model\ManufacturableModelTrait`
-   `Charcoal\Support\Property\ManufacturablePropertyDisplayTrait`
-   `Charcoal\Support\Property\ManufacturablePropertyInputTrait`
-   `Charcoal\Support\Property\ManufacturablePropertyTrait`
-   `Charcoal\Support\Widget\ManufacturableWidgetTrait`

#### Middleware / Routing

-   `Charcoal\Support\App\Middleware\RouteAlias`
-   `Charcoal\Support\App\Routing\RouteRedirectionManager`

#### View / Templating

-   `Charcoal\Support\App\Template\SupportTrait`
-   `Charcoal\Support\Cms\SectionAwareTrait`  
    for [`CMS\Section`][charcoal-cms] models in [template routes][charcoal-app].
    for multilingual management in viewable routes.
-   `Charcoal\Support\View\HtmlableInterface`
-   `Charcoal\Support\View\HtmlableTrait`

**Mustache Templating**

-   `Charcoal\Support\View\Mustache\DateTimeHelpers`
-   `Charcoal\Support\View\Mustache\StringHelpers`

**Contextual**

Defines a template intrinsically related to routing.

-   `Charcoal\Support\Cms\ContextualTemplateInterface`
-   `Charcoal\Support\Cms\ContextualTemplateTrait`

**Web Page**

Defines a web page.

-   `Charcoal\Support\App\Routing\SluggableTrait`
-   `Charcoal\Support\Cms\Metatag\DocumentTrait`
-   `Charcoal\Support\Cms\Object\AbstractWebContent`
-   `Charcoal\Support\Cms\Object\WebContentInterface`
-   `Charcoal\Support\Object\WebContentInterface`

**HTML Page Metadata**

-   `Charcoal\Support\Cms\Metatag\HasMetadataInterface`
-   `Charcoal\Support\Cms\Metatag\HasMetatagInterface`
-   `Charcoal\Support\Cms\Metatag\HasMetatagTrait`
-   `Charcoal\Support\Cms\Metatag\HasOpenGraphInterface`
-   `Charcoal\Support\Cms\Metatag\HasOpenGraphTrait`
-   `Charcoal\Support\Cms\Metatag\HasTwitterCardInterface`
-   `Charcoal\Support\Cms\Metatag\HasTwitterCardTrait`
-   `Charcoal\Support\Cms\Metatag\MetadataAwareInterface`

#### Widgets

-   `Charcoal\Support\Admin\Widget\TableWidget`  
    with improved cell customization.

**Tree-Sorted Object Collection Table**

-   `Charcoal\Support\Admin\Widget\HierarchicalTableWidget`  
    for listing the hierarchy of a collection of objects in a table layout.
-   `Charcoal\Support\Admin\Property\Display\HierarchicalDisplay`  
-   `Charcoal\Support\Property\HierarchicalObjectProperty`  

[charcoal-app]: https://github.com/locomotivemtl/charcoal-app
[charcoal-cms]: https://github.com/locomotivemtl/charcoal-cms
[charcoal-core]: https://github.com/locomotivemtl/charcoal-core
[charcoal-base]: https://github.com/locomotivemtl/charcoal-base
[charcoal-project]: https://github.com/locomotivemtl/charcoal-project-boilerplate
[charcoal-property]: https://github.com/locomotivemtl/charcoal-property
[charcoal-view]: https://github.com/locomotivemtl/charcoal-view
