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

namespace Eccube\Service\Docs;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * コード近接の仕様書（README.html）を集約・出力するサービス（Issue #6906 §8.2）.
 *
 * リポジトリ内の全 README.html を走査し、そのまま（開発者向けフル版）または
 * data-customer="true" の章だけを残した顧客提出向けフィルタ版として出力ディレクトリへ書き出す。
 * 出力 HTML はブラウザの「印刷 → PDF」で顧客提出用 PDF に変換できる。
 *
 * 静的サイトジェネレータ（Astro Starlight / VitePress / Antora 等）の入力にもこの出力を用いる想定。
 */
class DocsExportService
{
    /** すべての章を出力する（開発者向けフル版）. */
    public const FILTER_ALL = 'all';

    /** data-customer="true" の章だけを残す（顧客提出向け）. */
    public const FILTER_CUSTOMER = 'customer';

    /** 走査から除外するディレクトリ名. */
    private const EXCLUDED_DIRS = ['vendor', 'node_modules', 'var', '.git'];

    public function __construct(private readonly Filesystem $filesystem = new Filesystem())
    {
    }

    /**
     * $sourceDir 配下の全 README.html を $outputDir へ出力する.
     *
     * @param string $filter self::FILTER_ALL | self::FILTER_CUSTOMER
     *
     * @return list<string> 出力したファイルの $outputDir からの相対パス（ソート済み）
     *
     * @throws \InvalidArgumentException 未知のフィルタが指定された場合
     */
    public function export(string $sourceDir, string $outputDir, string $filter = self::FILTER_ALL): array
    {
        if (!\in_array($filter, [self::FILTER_ALL, self::FILTER_CUSTOMER], true)) {
            throw new \InvalidArgumentException(sprintf('Unknown filter "%s". Use "%s" or "%s".', $filter, self::FILTER_ALL, self::FILTER_CUSTOMER));
        }

        $finder = (new Finder())
            ->files()
            ->name('README.html')
            ->exclude(self::EXCLUDED_DIRS)
            ->in($sourceDir);

        $written = [];
        foreach ($finder as $file) {
            $html = $file->getContents();
            if (self::FILTER_CUSTOMER === $filter) {
                $html = $this->filterCustomerSections($html);
            }
            $relative = $file->getRelativePathname();
            $this->filesystem->dumpFile(rtrim($outputDir, '/').'/'.$relative, $html);
            $written[] = $relative;
        }

        sort($written);

        return $written;
    }

    /**
     * data-customer="true" 以外の <section> を除去した HTML を返す.
     *
     * README.html は自己完結 HTML で <section data-customer="true|false"> により章立てされている
     * （本プロジェクトが生成する、入れ子にならないフラットな section 構造が前提）。
     * 顧客提出時は data-customer="true" の章だけを残す。元の HTML の整形・文字コード（UTF-8）は保つ。
     *
     * DOMDocument の saveHTML() は非 ASCII 文字を数値実体参照へ変換し整形も崩すため用いない。
     * 判定は開始タグの属性のみで行い、本文が "data-customer=\"true\"" に言及していても誤判定しない。
     */
    public function filterCustomerSections(string $html): string
    {
        $result = preg_replace_callback(
            '#[ \t]*<section\b([^>]*)>.*?</section>\R?#s',
            static fn (array $matches): string => preg_match('/\bdata-customer\s*=\s*"true"/', $matches[1]) ? $matches[0] : '',
            $html,
        );

        return $result ?? $html;
    }
}
