# Charcoal Support

Support package providing various recurring utilities for [Charcoal][charcoal-core] projects.

## Requirements

| Prerequisite    | How to check  | How to install |
| --------------- | ------------- | -------------- |
| PHP >= 5.6.x    | `php -v`      | [php.net](//php.net/manual/en/install.php)
| Composer 1.0.0  | `composer -v` | [getcomposer.org](//getcomposer.org/)
| Charcoal        |               | [charcoal-project-boilerplate][charcoal-project]

See [composer.json](blob/master/composer.json) for depenencides.

## Installation

```shell
composer require mcaskill/charcoal-support
```

## What's inside?

-   **`Charcoal\Support\Container\DependentInterface`**  
    for standardized dependency injection.
-   **`Charcoal\Support\Property\ParsableValueTrait`**  
    for parsing [Charcoal Property][charcoal-property] values marked as _translatable_ or _multiple_.

#### Factories

-   **`Charcoal\Support\Admin\Widget\ManufacturableWidgetTrait`**
-   **`Charcoal\Support\Email\ManufacturableEmailTrait`**
-   **`Charcoal\Support\Model\ManufacturableModelCollectionTrait`**
-   **`Charcoal\Support\Model\ManufacturableModelTrait`**

#### Widgets

**Tree-Sorted Object Collection Table**

-   **`Charcoal\Support\Admin\Widget\HierarchicalTableWidget`**  
    **`Charcoal\Support\Admin\Property\Display\HierarchicalDisplay`**  
    for listing the hierarchy of a collection of objects in a table layout.

#### Views

-   **`Charcoal\Support\Cms\SectionAwareTrait`**  
    for [`CMS\Section`][charcoal-cms] models in [template routes][charcoal-app].
-   **`Charcoal\Support\Translation\TranslatableTemplateInterface`**  
    **`Charcoal\Support\Translation\TranslatableTemplateTrait`**  
-   **`Charcoal\Support\Translation\TranslatorAwareInterface`**  
    **`Charcoal\Support\Translation\TranslatorAwareTrait`**  
    for multilingual management in viewable routes.
-   **`Charcoal\Support\View\Mustache\Helpers`**  
    for [views][charcoal-view].

[charcoal-app]: https://github.com/locomotivemtl/charcoal-app
[charcoal-cms]: https://github.com/locomotivemtl/charcoal-cms
[charcoal-core]: https://github.com/locomotivemtl/charcoal-core
[charcoal-base]: https://github.com/locomotivemtl/charcoal-base
[charcoal-project]: https://github.com/locomotivemtl/charcoal-project-boilerplate
[charcoal-property]: https://github.com/locomotivemtl/charcoal-property
[charcoal-view]: https://github.com/locomotivemtl/charcoal-view
