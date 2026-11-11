<?php

namespace Charcoal\Tests\Support\Model;

use ArrayAccess;
use Charcoal\Model\Model;
use Charcoal\Model\ModelInterface;
use Charcoal\Support\Model\Collection;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * Test the enhanced model collection class.
 */
class CollectionTest extends TestCase
{
    public const OBJ_1 = '40ea';
    public const OBJ_2 = '69c6';
    public const OBJ_3 = '71b5';
    public const OBJ_4 = 'dce3';
    public const OBJ_5 = 'ea9f';

    /**
     * @var Model[] Ordered array of models.
     */
    protected $arr;

    /**
     * @var Model[] Associative arry of models.
     */
    protected $map;

    protected function setUp(): void
    {
        $logger = new NullLogger();

        $this->map = [
            self::OBJ_1 => (new Model([ 'logger' => $logger ]))->setData([ 'id' => self::OBJ_1, 'position' => 1 ]),
            self::OBJ_2 => (new Model([ 'logger' => $logger ]))->setData([ 'id' => self::OBJ_2, 'position' => 2 ]),
            self::OBJ_3 => (new Model([ 'logger' => $logger ]))->setData([ 'id' => self::OBJ_3, 'position' => 3 ]),
            self::OBJ_4 => (new Model([ 'logger' => $logger ]))->setData([ 'id' => self::OBJ_4, 'position' => 4 ]),
            self::OBJ_5 => (new Model([ 'logger' => $logger ]))->setData([ 'id' => self::OBJ_5, 'position' => 5 ]),
        ];

        $this->arr = array_values($this->map);
    }

    // Test \Charcoal\Support\Model\Collection
    // =============================================================================================

    public function testEmptyCollection()
    {
        $c = new Collection();

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

    public function testSortByThrowsAnException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $arr = $this->arr;
        shuffle($arr);

        $c = new Collection($arr);
        $c->sortBy(42);
    }

    public function testReverse()
    {
        $arr = $this->arr;
        $map = array_reverse($this->map, true);

        $c = new Collection($arr);
        $c = $c->reverse();

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

    public function testRandomOnEmptyCollection()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Collection())->random();
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

    public function testPrependUnacceptableData()
    {
        $this->expectException(\InvalidArgumentException::class);

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

    /**
     * Asserts that an array has a specified subset.
     *
     * Note: This is a poor-man's fallback for a method previously available
     * in PHPUnit, deprecated in PHPUnit 8, and removed in PHPUnit 9.
     *
     * @param  array|ArrayAccess|mixed[] $subset                 The expected subset.
     * @param  array|ArrayAccess|mixed[] $array                  The actual haystack.
     * @param  string                    $message                The error to report.
     * @throws InvalidArgumentException
     * @return void
     */
    protected function assertArraySubset($subset, $array, $message = ''): void
    {
        if (!(is_array($subset) || $subset instanceof ArrayAccess)) {
            throw InvalidArgumentException::create(
                1,
                'array or ArrayAccess'
            );
        }

        if (!(is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentException::create(
                2,
                'array or ArrayAccess'
            );
        }

        foreach ($subset as $key => $value) {
            $this->assertArrayHasKey($key, $array, $message);

            if (is_array($value) || $value instanceof ArrayAccess) {
                $this->assertArraySubset($value, $array[$key], $message);
            } else {
                $this->assertSame($value, $array[$key], $message);
            }
        }
    }
}
