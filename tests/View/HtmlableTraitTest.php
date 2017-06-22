<?php

namespace Charcoal\Tests\Support\View;

// From 'charcoal-view'
use Charcoal\View\AbstractView;
use Charcoal\View\GenericView;

// From 'charcoal-support'
use Charcoal\Support\View\HtmlableTrait;

/**
 * Test the HTML-renderable trait.
 */
class HtmlableTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Store an instance of the mock object.
     *
     * @var HtmlableTrait
     */
    private $obj;

    /**
     * Store the view renderer.
     *
     * @var AbtractView
     */
    private $view;

    /**
     * Invoked before the test is run.
     */
    public function setUp()
    {
        $this->obj = $this->getMockForTrait('\Charcoal\Support\View\HtmlableTrait');
    }

    /**
     *
     */
    public function testHtmlAttributes()
    {
        $obj  = $this->obj;
        $attr = [
            'href'     => 'https://github.com/locomotivemtl/charcoal-view',
            'hreflang' => 'en',
            'title'    => '"Charcoal Views" on GitHub',
            'data-foo' => 'qux'
        ];

        $actual   = $obj->htmlAttributes($attr, null, false);
        $expected = 'href="https://github.com/locomotivemtl/charcoal-view" ' .
                    'hreflang="en" ' .
                    'title="&quot;Charcoal Views&quot; on GitHub" ' .
                    'data-foo="qux"';

        $this->assertEquals($expected, $actual);
    }
}
