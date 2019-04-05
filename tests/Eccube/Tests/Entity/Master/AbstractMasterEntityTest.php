<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Entity\Master;

use Eccube\Entity\Master\Sex;
use Eccube\Tests\EccubeTestCase;

/**
 * AbstractMasterEntity test cases.
 *
 * @author Kentaro Ohkouchi
 */
class AbstractMasterEntityTest extends EccubeTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetConstant()
    {
        self::assertEquals(1, TestSexDecorator::TEST_MALE, 'constant access');
        self::assertEquals(1, TestSexDecorator::TEST_MALE, 'enum like access');
    }

    public function testGetConstantWithTrait()
    {
        self::assertEquals(2, TestSexDecorator::$TEST_FEMALE, 'enum like access via trait');
    }

    public function testExplicitOverwriteConstant()
    {
        try {
            $c = new TestSexDecorator();
            // クラス変数を上書きしようとすると InvalidArgumentException になる
            $c->TEST_FEMALE = 3;
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function testInvalidFields()
    {
        // id, name, sortNo は取得できない
        try {
            $c = TestSexDecorator::id();
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
        try {
            $c = TestSexDecorator::name();
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
        try {
            $c = TestSexDecorator::sortNo();
            self::fail();
        } catch (\InvalidArgumentException $e) {
            self::assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }
}

class TestSexDecorator extends Sex
{
    use TestSexTrait;
    const TEST_MALE = 1;
}

trait TestSexTrait
{
    public static $TEST_FEMALE = 2;
}
