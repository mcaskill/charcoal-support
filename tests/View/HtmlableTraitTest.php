<?php

namespace Charcoal\Tests\Support\View;

use Charcoal\Support\View\HtmlableTrait;
use PHPUnit\Framework\TestCase;

/**
 * Test the HTML-renderable trait.
 */
class HtmlableTraitTest extends TestCase
{
    /**
     * Store an instance of the mock object.
     *
     * @var HtmlableTrait
     */
    private $obj;

    /**
     * Invoked before the test is run.
     */
    protected function setUp(): void
    {
        $this->obj = $this->getMockForTrait('\Charcoal\Support\View\HtmlableTrait');
    }

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
