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

namespace Eccube\Tests\Form\Type\Front;

use Eccube\Form\Type\Front\RefundRequestType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class RefundRequestTypeTest extends AbstractTypeTestCase
{
    private ?FormInterface $form = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->formFactory
            ->createBuilder(RefundRequestType::class, null, [
                'csrf_protection' => false,
                'max_quantity' => 10,
            ])
            ->getForm();
    }

    public function testValidData(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertTrue($this->form->isValid());
    }

    public function testValidMaxQuantity(): void
    {
        $this->form->submit([
            'quantity' => '10',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidQuantityZero(): void
    {
        $this->form->submit([
            'quantity' => '0',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantityExceed(): void
    {
        $this->form->submit([
            'quantity' => '11',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidQuantityNegative(): void
    {
        $this->form->submit([
            'quantity' => '-1',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidReasonBlank(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => '',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidReasonTooLong(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => str_repeat('あ', 4001),
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testValidReasonMaxLength(): void
    {
        $this->form->submit([
            'quantity' => '1',
            'reason' => str_repeat('あ', 4000),
        ]);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidQuantityBlank(): void
    {
        $this->form->submit([
            'quantity' => '',
            'reason' => 'テスト返品理由',
        ]);

        $this->assertFalse($this->form->isValid());
    }

    public function testValidWithFiles(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'refund_type_').'.gif';
        // GIF89a ヘッダ
        file_put_contents($tmpFile, "GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x00\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;");

        $form = $this->formFactory
            ->createBuilder(RefundRequestType::class, null, [
                'csrf_protection' => false,
                'max_quantity' => 10,
            ])
            ->getForm();

        $form->submit([
            'quantity' => '1',
            'reason' => 'テスト返品理由',
            'files' => [
                new UploadedFile($tmpFile, 'test.gif', 'image/gif', null, true),
            ],
        ]);

        $this->assertTrue($form->isValid());
    }

    public function testInvalidFilesExceedMaxCount(): void
    {
        $files = [];
        for ($i = 0; $i < 4; $i++) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'refund_type_');
            file_put_contents($tmpFile, str_repeat("\x00", 100));
            $files[] = new UploadedFile($tmpFile, "test_{$i}.jpg", 'image/jpeg', null, true);
        }

        $form = $this->formFactory
            ->createBuilder(RefundRequestType::class, null, [
                'csrf_protection' => false,
                'max_quantity' => 10,
            ])
            ->getForm();

        $form->submit([
            'quantity' => '1',
            'reason' => 'テスト返品理由',
            'files' => $files,
        ]);

        $this->assertFalse($form->isValid());
    }

    public function testInvalidFileDisallowedMimeType(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'refund_type_');
        file_put_contents($tmpFile, '%PDF-1.4');

        $form = $this->formFactory
            ->createBuilder(RefundRequestType::class, null, [
                'csrf_protection' => false,
                'max_quantity' => 10,
            ])
            ->getForm();

        $form->submit([
            'quantity' => '1',
            'reason' => 'テスト返品理由',
            'files' => [
                new UploadedFile($tmpFile, 'test.pdf', 'application/pdf', null, true),
            ],
        ]);

        $this->assertFalse($form->isValid());
    }

    public function testInvalidFileExceedMaxSize(): void
    {
        // 許可MIME(GIF)ヘッダを書いた上で MAX_FILE_SIZE(15M) を超えるサイズにし, サイズ超過のみで弾かれることを検証する.
        $tmpFile = tempnam(sys_get_temp_dir(), 'refund_type_').'.gif';
        $fp = fopen($tmpFile, 'wb');
        fwrite($fp, "GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x00\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;");
        fseek($fp, 16 * 1024 * 1024); // 16MB(>15M)。sparseファイルなので実書き込みは僅か.
        fwrite($fp, "\x00");
        fclose($fp);

        $form = $this->formFactory
            ->createBuilder(RefundRequestType::class, null, [
                'csrf_protection' => false,
                'max_quantity' => 10,
            ])
            ->getForm();

        $form->submit([
            'quantity' => '1',
            'reason' => 'テスト返品理由',
            'files' => [
                new UploadedFile($tmpFile, 'test.gif', 'image/gif', null, true),
            ],
        ]);

        $this->assertFalse($form->isValid());

        @unlink($tmpFile);
    }
}
