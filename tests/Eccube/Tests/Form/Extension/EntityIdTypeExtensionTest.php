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

namespace Eccube\Tests\Form\Extension;

use Eccube\Entity\Delivery;
use Eccube\Entity\Master\Pref;
use Eccube\Form\Type\Master\PrefType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class EntityIdTypeExtensionTest extends AbstractTypeTestCase
{
    /**
     * 範囲外の ID は DB へ問い合わせず、入力エラーになる.
     */
    #[DataProvider(methodName: 'provideInvalidValue')]
    public function testSubmitInvalidValue(mixed $value, bool $multiple): void
    {
        $form = $this->formFactory->createBuilder(EntityType::class, null, [
            'class' => Delivery::class,
            'multiple' => $multiple,
            'csrf_protection' => false,
        ])->getForm();

        $form->submit($value);

        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
    }

    /**
     * @return \Iterator<string, array{mixed, bool}>
     */
    public static function provideInvalidValue(): \Iterator
    {
        yield 'alpha' => ['abc', false];
        yield 'out of range' => ['2147483648', false];
        yield 'out of range in multiple' => [['1', '2147483648'], true];
    }

    public function testSubmitSmallintOutOfRange(): void
    {
        $form = $this->formFactory->createBuilder(PrefType::class, null, ['csrf_protection' => false])->getForm();

        $form->submit('32768');

        $this->assertFalse($form->isValid());
    }

    public function testSubmitValidValue(): void
    {
        $form = $this->formFactory->createBuilder(PrefType::class, null, ['csrf_protection' => false])->getForm();

        $form->submit('13');

        $this->assertTrue($form->isValid());
        $this->assertInstanceOf(Pref::class, $form->getData());
        $this->assertSame(13, $form->getData()->getId());
    }

    /**
     * choice_value を独自に指定した EntityType は、送信値が ID とは限らないため検証しない.
     */
    public function testCustomChoiceValueIsNotValidated(): void
    {
        $form = $this->formFactory->createBuilder(EntityType::class, null, [
            'class' => Pref::class,
            'choice_value' => 'name',
            'csrf_protection' => false,
        ])->getForm();

        $form->submit('東京都');

        $this->assertTrue($form->isValid());
        $this->assertSame(13, $form->getData()->getId());
    }
}
