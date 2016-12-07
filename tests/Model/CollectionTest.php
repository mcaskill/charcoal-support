<?php

namespace Charcoal\Support\Tests\Model;

use \ArrayIterator;
use \ArrayObject;
use \CachingIterator;
use \ReflectionClass;

// From 'mockery/mockery'
use \Mockery as m;

// From 'charcoal-core'
use \Charcoal\Model\Model;
use \Charcoal\Model\ModelInterface;

// From 'charcoal-support'
use \Charcoal\Support\Model\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    const OBJ_1 = '40ea';
    const OBJ_2 = '69c6';
    const OBJ_3 = '71b5';
    const OBJ_4 = 'dce3';
    const OBJ_5 = 'ea9f';

    /**
     * @var Model[] Ordered array of models.
     */
    protected $arr;

    /**
     * @var Model[] Associative arry of models.
     */
    protected $map;

    public function setUp()
    {
        $this->map = [
            self::OBJ_1 => m::mock(Model::class, [ 'id' => self::OBJ_1 ]),
            self::OBJ_2 => m::mock(Model::class, [ 'id' => self::OBJ_2 ]),
            self::OBJ_3 => m::mock(Model::class, [ 'id' => self::OBJ_3 ]),
            self::OBJ_4 => m::mock(Model::class, [ 'id' => self::OBJ_4 ]),
            self::OBJ_5 => m::mock(Model::class, [ 'id' => self::OBJ_5 ]),
        ];

        $i = 1;
        foreach ($this->map as &$mock) {
            $mock->shouldReceive('offsetGet')
                 ->with('position')
                 ->andReturn($i++);
        }

        $this->arr = array_values($this->map);
    }

    // Test \Charcoal\Support\Model\Collection
    // =============================================================================================

    public function testEmptyCollection()
    {
        $c = new Collection;

        $this->assertEquals(null, $c->shift());
        $this->assertEquals(null, $c->pop());
    }

    public function testPopLastItemInCollection()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);

        $this->assertEquals($o5, $c->pop());
        $this->assertEquals($o4, $c->last());
    }

    public function testShiftFirstItemInCollection()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);

        $this->assertEquals($o1, $c->shift());
        $this->assertEquals($o2, $c->first());
    }

    public function testSortByCallback()
    {
        $arr = $this->arr;
        shuffle($arr);

        $byKey = function ($obj, $key) {
            return $key;
        };

        $c = new Collection($arr);
        $c->sortBy($byKey);

        $this->assertEquals($this->map, $c->all());

        $c = new Collection($arr);
        $c->sortByDesc($byKey);

        $map = array_reverse($this->map, true);
        $this->assertEquals($map, $c->all());
    }

    public function testSortByString()
    {
        $arr = $this->arr;
        shuffle($arr);

        $c = new Collection($arr);
        $c->sortBy('position');

        $this->assertEquals($this->map, $c->all());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSortByThrowsAnException()
    {
        $arr = $this->arr;
        shuffle($arr);

        $c = new Collection($arr);
        $c->sortBy(42);
    }

    public function testReverse()
    {
        $arr = $this->arr;
        $map = array_reverse($this->map);

        $c = new Collection($arr);
        $c->reverse();

        $this->assertSame($map, $c->all());
    }

    public function testTakeFirst()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);

        $first = $c->take(2);
        $this->assertEquals([ self::OBJ_1 => $o1, self::OBJ_2 => $o2 ], $first->all());
    }

    public function testTakeLast()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);

        $last = $c->take(-2);
        $this->assertEquals([ self::OBJ_4 => $o4, self::OBJ_5 => $o5 ], $last->all());
    }

    public function testRandom()
    {
        $c = new Collection($this->arr);

        $random = $c->random();
        $this->assertInstanceOf(ModelInterface::class, $random);
        $this->assertContains($random, $c->all());

        $random = $c->random(3);
        $this->assertInstanceOf(Collection::class, $random);
        $this->assertArraySubset($random->all(), $c->all());
        $this->assertCount(3, $random);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRandomOnEmptyCollection()
    {
        (new Collection)->random();
    }

    public function testPaginate()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);

        $this->assertEquals(
            [ self::OBJ_1 => $o1, self::OBJ_2 => $o2, self::OBJ_3 => $o3 ],
            $c->forPage(1, 3)->all()
        );
        $this->assertEquals(
            [ self::OBJ_4 => $o4, self::OBJ_5 => $o5 ],
            $c->forPage(2, 3)->all()
        );
        $this->assertEquals([], $c->forPage(3, 3)->all());
    }

    public function testPrependAcceptableData()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $map = $this->map;
        array_pop($map);

        $c = new Collection([ $o1, $o2, $o3, $o4 ]);
        $expected = array_merge([ self::OBJ_5 => $o5 ], $map);
        $this->assertEquals($expected, $c->prepend($o5)->all());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrependUnacceptableData()
    {
        $c = new Collection($this->arr);
        $c->prepend('foo');
    }

    public function testOnly()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);

        $this->assertEquals(
            $c->all(),
            $c->only(null)->all()
        );
        $this->assertEquals(
            [ self::OBJ_1 => $o1 ],
            $c->only(...[ self::OBJ_1, 'missing' ])->all()
        );
        $this->assertEquals(
            [ self::OBJ_1 => $o1 ],
            $c->only(self::OBJ_1, 'missing')->all()
        );
        $this->assertEquals(
            [ self::OBJ_1 => $o1, self::OBJ_3 => $o3 ],
            $c->only(self::OBJ_1, self::OBJ_3)->all()
        );
    }

    public function testSliceOffset()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);
        $this->assertEquals([ $o3, $o4, $o5 ], $c->slice(2)->values());
    }

    public function testSliceNegativeOffset()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);
        $this->assertEquals([ $o3, $o4, $o5 ], $c->slice(-3)->values());
    }

    public function testSliceOffsetAndLength()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);
        $this->assertEquals([ $o3, $o4 ], $c->slice(2, 2)->values());
    }

    public function testSliceOffsetAndNegativeLength()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);

        $this->assertEquals([ $o3, $o4 ], $c->slice(2, -1)->values());
    }

    public function testSliceNegativeOffsetAndLength()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);
        $this->assertEquals([ $o2, $o3, $o4 ], $c->slice(-4, 3)->values());
    }

    public function testSliceNegativeOffsetAndNegativeLength()
    {
        list($o1, $o2, $o3, $o4, $o5) = $this->arr;
        $c = new Collection($this->arr);
        $this->assertEquals([ $o2, $o3, $o4 ], $c->slice(-4, -1)->values());
    }
}
