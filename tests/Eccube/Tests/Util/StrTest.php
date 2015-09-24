<?php

namespace Eccube\Tests\Util;

use Eccube\Util\Str;

/**
 * Str test cases.
 *
 * @author Kentaro Ohkouchi
 */
class StrTest extends \PHPUnit_Framework_TestCase
{
    private $actual;
    private $expected;

    public function testRandom()
    {
        $this->expected = 16;
        $result = Str::random();
        $this->actual = strlen($result);
        // デフォルトは16桁
        $this->assertEquals($this->expected, $this->actual);
        $this->assertTrue(preg_match('/[A-Za-z0-9]{16}/', $result) === 1);
    }

    public function testRandomWithParams()
    {
        $this->expected = 5;
        $result = Str::random($this->expected);
        $this->actual = strlen($result);

        $this->assertEquals($this->expected, $this->actual);
        $this->assertTrue(preg_match('/[A-Za-z0-9]{'.$this->expected.'}/', $result) === 1);
    }

    public function testRandomException()
    {
        $this->expected = 'Unable to generate random string.';
        try {
            $result = Str::random(0);
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->actual = $e->getMessage();
        }
        $this->assertEquals($this->expected, $this->actual);
    }
}
