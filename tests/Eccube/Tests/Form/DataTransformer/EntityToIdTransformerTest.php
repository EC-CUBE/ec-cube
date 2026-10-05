<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Form\DataTransformer;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\Pref;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class EntityToIdTransformerTest extends EccubeTestCase
{
    public function testReverseTransform(): void
    {
        $Customer = $this->createCustomer();
        $transformer = new EntityToIdTransformer($this->entityManager, Customer::class);

        $this->assertSame($Customer, $transformer->reverseTransform((string) $Customer->getId()));
        $this->assertNotInstanceOf(Customer::class, $transformer->reverseTransform(''));
        $this->assertNotInstanceOf(Customer::class, $transformer->reverseTransform(null));
    }

    /**
     * 整数型の ID に数値でない値・範囲外の値を渡しても、DB のエラーではなく変換の失敗になる.
     */
    #[DataProvider(methodName: 'provideInvalidId')]
    public function testReverseTransformWithInvalidId(string $class, mixed $id): void
    {
        $transformer = new EntityToIdTransformer($this->entityManager, $class);

        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform($id);
    }

    /**
     * @return \Iterator<string, array{class-string, mixed}>
     */
    public static function provideInvalidId(): \Iterator
    {
        yield 'alpha' => [Customer::class, 'abc'];
        yield 'integer out of range' => [Customer::class, '2147483648'];
        yield 'array' => [Customer::class, ['1']];
        yield 'not found' => [Customer::class, '2147483647'];
        yield 'smallint out of range' => [Pref::class, '32768'];
    }
}
