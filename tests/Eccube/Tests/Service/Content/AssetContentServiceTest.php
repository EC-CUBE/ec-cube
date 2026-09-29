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

namespace Eccube\Tests\Service\Content;

use Eccube\Exception\ContentValidationException;
use Eccube\Exception\ContentWriteException;
use Eccube\Service\Content\AssetContentService;
use Eccube\Service\Content\ContentStatus;
use Eccube\Service\Content\UserDataFileService;
use Eccube\Tests\EffectiveUserTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * 一時ディレクトリを root にして直接インスタンス化するため, 実際の html/user_data には触れない.
 */
final class AssetContentServiceTest extends TestCase
{
    use EffectiveUserTrait;

    private string $root;

    private Filesystem $fs;

    private AssetContentService $assetContentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->root = sys_get_temp_dir().'/eccube-asset-'.bin2hex(random_bytes(6));
        $this->fs->mkdir($this->root);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id): string => $id);

        $this->assetContentService = new AssetContentService(new UserDataFileService(
            $this->root,
            ['css', 'js'],
            new Filesystem(),
            $translator
        ));
    }

    protected function tearDown(): void
    {
        if (isset($this->fs)) {
            $this->fs->chmod($this->root, 0755, 0000, true);
            $this->fs->remove($this->root);
        }

        parent::tearDown();
    }

    public function testReadReturnsEmptyStringWhenFileIsMissing(): void
    {
        $this->assertSame('', $this->assetContentService->read('css'));
    }

    public function testApplyCreatesFile(): void
    {
        $result = $this->assetContentService->apply('css', 'body{}');

        $this->assertSame(ContentStatus::Created, $result->status);
        $this->assertSame('css', $result->identifier, '識別子は利用者が指定した種別にする');
        $this->assertSame('body{}', file_get_contents($this->root.'/assets/css/customize.css'));
        $this->assertSame('body{}', $this->assetContentService->read('css'));
    }

    public function testApplyIsIdempotent(): void
    {
        $this->assetContentService->apply('js', 'console.log(1);');
        $result = $this->assetContentService->apply('js', 'console.log(1);');

        $this->assertSame(ContentStatus::Unchanged, $result->status);
    }

    public function testApplyDryRunDoesNotWrite(): void
    {
        $result = $this->assetContentService->apply('css', 'body{}', true);

        $this->assertSame(ContentStatus::Created, $result->status);
        $this->assertFileDoesNotExist($this->root.'/assets/css/customize.css');
    }

    public function testApplyRejectsUnknownType(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->assetContentService->apply('scss', 'body{}');
    }

    /**
     * 書き込めない場合でも現在の内容は読み取れること.
     *
     * 判定に is_writable() を混ぜると, 権限を分離した構成 (html/user_data がレーン S) で
     * 管理画面の CSS 管理 / JS 管理が空欄になり, 現在の内容を確認できなくなる.
     */
    public function testReadReturnsContentEvenWhenNotWritable(): void
    {
        $this->skipIfRoot();

        $this->assetContentService->apply('css', 'body{color:red}');
        $dir = $this->root.'/assets/css';
        $this->fs->chmod($dir.'/customize.css', 0444);
        $this->fs->chmod($dir, 0555);

        $this->assertSame('body{color:red}', $this->assetContentService->read('css'));

        $this->expectException(ContentWriteException::class);
        $this->assetContentService->apply('css', 'body{color:blue}');
    }
}
