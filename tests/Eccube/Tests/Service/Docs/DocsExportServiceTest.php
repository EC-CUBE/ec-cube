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

namespace Eccube\Tests\Service\Docs;

use Eccube\Service\Docs\DocsExportService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class DocsExportServiceTest extends TestCase
{
    private DocsExportService $service;

    private Filesystem $filesystem;

    private string $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocsExportService();
        $this->filesystem = new Filesystem();
        $this->workDir = sys_get_temp_dir().'/eccube-docs-export-test-'.uniqid();
        $this->filesystem->mkdir($this->workDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->workDir);
        parent::tearDown();
    }

    private function sampleHtml(): string
    {
        return <<<'HTML'
            <!DOCTYPE html>
            <html lang="ja"><head><meta charset="UTF-8"><title>サンプル — 仕様</title></head><body>
            <section data-section="overview" data-customer="true"><h2>概要</h2><p>顧客にも見せる章。</p></section>
            <section data-section="glossary" data-customer="true"><h2>用語</h2><p>受注・出荷。</p></section>
            <section data-section="extension" data-customer="false"><h2>拡張ポイント</h2><p>開発者向けの内部詳細。</p></section>
            <section data-section="notes" data-customer="false"><h2>注意点</h2><p>開発者向けの注意。</p></section>
            </body></html>
            HTML;
    }

    public function testFilterCustomerSectionsKeepsOnlyCustomerChapters(): void
    {
        $filtered = $this->service->filterCustomerSections($this->sampleHtml());

        // data-customer="true" の章は残る
        $this->assertStringContainsString('概要', $filtered);
        $this->assertStringContainsString('用語', $filtered);
        // data-customer="false" の章は除去される
        $this->assertStringNotContainsString('拡張ポイント', $filtered);
        $this->assertStringNotContainsString('開発者向けの注意', $filtered);
    }

    public function testFilterCustomerSectionsPreservesJapaneseWithoutMojibake(): void
    {
        $filtered = $this->service->filterCustomerSections($this->sampleHtml());

        // 文字化け（U+FFFD）が混入していない
        $this->assertStringNotContainsString("\u{FFFD}", $filtered);
        // 顧客向け本文の日本語がそのまま残る（数値実体参照へ変換されない）
        $this->assertStringContainsString('顧客にも見せる章。', $filtered);
        // 元の見出しがそのまま UTF-8 で残る
        $this->assertStringContainsString('<h2>概要</h2>', $filtered);
    }

    public function testFilterRemovesDevSectionEvenWhenBodyMentionsTheAttribute(): void
    {
        // 本文が data-customer="true" という文字列に言及していても、開始タグの属性で判定するため除去される
        $html = '<body>'
            .'<section data-section="a" data-customer="true"><p>残す</p></section>'
            .'<section data-section="b" data-customer="false"><p>属性 data-customer="true" について説明する開発者向けの章</p></section>'
            .'</body>';

        $filtered = $this->service->filterCustomerSections($html);

        $this->assertStringContainsString('残す', $filtered);
        $this->assertStringNotContainsString('開発者向けの章', $filtered);
    }

    public function testExportAllWritesEveryReadmePreservingStructure(): void
    {
        $source = $this->workDir.'/src';
        $out = $this->workDir.'/out';
        $this->filesystem->dumpFile($source.'/Foo/README.html', $this->sampleHtml());
        $this->filesystem->dumpFile($source.'/Foo/Bar/README.html', $this->sampleHtml());
        // README.html 以外・除外ディレクトリは対象外
        $this->filesystem->dumpFile($source.'/Foo/README.md', '# index');
        $this->filesystem->dumpFile($source.'/vendor/Baz/README.html', $this->sampleHtml());

        $written = $this->service->export($source, $out, DocsExportService::FILTER_ALL);

        $this->assertSame(['Foo/Bar/README.html', 'Foo/README.html'], $written);
        $this->assertFileExists($out.'/Foo/README.html');
        $this->assertFileExists($out.'/Foo/Bar/README.html');
        $this->assertFileDoesNotExist($out.'/vendor/Baz/README.html');
        // フィルタなしなら開発者向け章も残る
        $this->assertStringContainsString('拡張ポイント', (string) file_get_contents($out.'/Foo/README.html'));
    }

    public function testExportCustomerFiltersEachFile(): void
    {
        $source = $this->workDir.'/src';
        $out = $this->workDir.'/out';
        $this->filesystem->dumpFile($source.'/Foo/README.html', $this->sampleHtml());

        $this->service->export($source, $out, DocsExportService::FILTER_CUSTOMER);

        $content = (string) file_get_contents($out.'/Foo/README.html');
        $this->assertStringContainsString('概要', $content);
        $this->assertStringNotContainsString('拡張ポイント', $content);
    }

    public function testExportRejectsUnknownFilter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->export($this->workDir, $this->workDir.'/out', 'bogus');
    }
}
