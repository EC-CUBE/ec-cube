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

namespace Eccube\Tests\Service\Composer;

use Eccube\Service\Composer\ComposerApiService;
use Eccube\Tests\Service\AbstractServiceTestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;

final class ComposerApiServiceTest extends AbstractServiceTestCase
{
    private ?string $workingDir = null;

    // EccubeTestCase::cleanUpProperties() が全プロパティに null を代入するため nullable にする
    private ?string $originalMemoryLimit = null;

    protected function setUp(): void
    {
        parent::setUp();

        // ComposerApiService::init() が ini_set('memory_limit', ...) を行うため、
        // phpunit.xml.dist の memory_limit=-1 が上書きされたまま後続テストに
        // リークしないよう保存しておき tearDown() で復元する
        $limit = ini_get('memory_limit');
        $this->originalMemoryLimit = $limit === false ? null : $limit;

        // プロジェクト本体の composer.json を変更しないよう、一時ディレクトリを作業ディレクトリにする
        $this->workingDir = sys_get_temp_dir().'/eccube_composer_api_service_test_'.uniqid();
        mkdir($this->workingDir);
        file_put_contents($this->workingDir.'/composer.json', '{}');
    }

    protected function tearDown(): void
    {
        if ($this->originalMemoryLimit !== null) {
            ini_set('memory_limit', $this->originalMemoryLimit);
        }

        if ($this->workingDir !== null) {
            (new Filesystem())->remove($this->workingDir);
        }

        parent::tearDown();
    }

    /**
     * OutputInterface を渡した場合 (CLI 経由の eccube:composer:require 等) も string が返ること.
     */
    public function testRunCommandWithOutputReturnsString()
    {
        $service = static::getContainer()->get(ComposerApiService::class);
        $service->setWorkingDir($this->workingDir);

        $result = $service->runCommand(['command' => 'about'], new BufferedOutput());

        $this->assertSame('', $result);
    }
}
