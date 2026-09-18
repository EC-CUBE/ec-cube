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

namespace Eccube\Tests\Form\Validator;

use Eccube\Form\Validator\PasswordBlocklist;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PasswordBlocklistValidatorTest extends AbstractTypeTestCase
{
    protected ?ValidatorInterface $validator = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    #[DataProvider(methodName: 'passwordProvider')]
    public function testValidate(?string $password, bool $expectedValid): void
    {
        $errors = $this->validator->validate($password, new PasswordBlocklist());

        $this->assertSame($expectedValid, count($errors) === 0);
    }

    /**
     * @return array<array{0: string|null, 1: bool}>
     */
    public static function passwordProvider(): \Iterator
    {
        // ブロックリストに掲載された値は不可
        yield ['passwordpassword', false];
        // 拡充エントリ(キーボード配列 walk)も不可
        yield ['qwertyuiop123456', false];
        // 大文字小文字を無視して照合する
        yield ['PASSWORDPASSWORD', false];
        // NFKC 正規化して照合する(全角は半角に正規化されて一致)
        yield ['ｐａｓｓｗｏｒｄｐａｓｓｗｏｒｄ', false];
        // 掲載されていない値は許可
        yield ['correct horse battery staple', true];
        // 空文字・null は対象外(他の制約で扱う)
        yield ['', true];
        yield [null, true];
    }
}
