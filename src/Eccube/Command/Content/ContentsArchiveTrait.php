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

namespace Eccube\Command\Content;

use Eccube\Service\Content\ContentsArchive;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * eccube:contents:export / import が共有する, アーカイブの場所とセクションの解決.
 *
 * eccubeConfig は ContentCommandTrait が提供する.
 */
trait ContentsArchiveTrait
{
    /**
     * アーカイブの場所. 相対パスはプロジェクトルート起点で解決する
     * (bin/console はどの作業ディレクトリからでも実行できるため).
     */
    protected function resolveArchiveDir(string $dir): string
    {
        if (str_starts_with($dir, '/')) {
            return $dir;
        }

        return rtrim((string) $this->eccubeConfig->get('kernel.project_dir'), '/').'/'.ltrim($dir, '/');
    }

    protected function addSectionOptions(): void
    {
        $sections = implode(',', ContentsArchive::DEFAULT_SECTIONS);
        $optional = implode(',', ContentsArchive::OPTIONAL_SECTIONS);

        $this
            ->addOption('only', null, InputOption::VALUE_REQUIRED, sprintf('対象を絞る (カンマ区切り: %s)', $sections))
            ->addOption('exclude', null, InputOption::VALUE_REQUIRED, '対象から除く (カンマ区切り)')
            ->addOption('include', null, InputOption::VALUE_REQUIRED, sprintf('既定では扱わないセクションを追加する (%s)', $optional));
    }

    /**
     * 適用順は ContentsArchive::DEFAULT_SECTIONS の並びを保つ
     * (ページはレイアウトを, レイアウトはブロックを参照するため).
     *
     * @return list<string>
     *
     * @throws \InvalidArgumentException 未知のセクションを指定した場合
     */
    protected function resolveSections(InputInterface $input): array
    {
        $only = self::split($input->getOption('only'));
        $exclude = self::split($input->getOption('exclude'));
        $include = self::split($input->getOption('include'));

        foreach ([...$only, ...$exclude, ...$include] as $section) {
            if (!ContentsArchive::isSection($section)) {
                throw new \InvalidArgumentException(sprintf('未知のセクションです: %s (指定できるのは %s)', $section, implode(' / ', ContentsArchive::allSections())));
            }
        }

        $selected = [] === $only ? ContentsArchive::DEFAULT_SECTIONS : $only;
        $selected = [...$selected, ...$include];

        $sections = [];
        foreach (ContentsArchive::allSections() as $section) {
            if (in_array($section, $selected, true) && !in_array($section, $exclude, true)) {
                $sections[] = $section;
            }
        }

        if ([] === $sections) {
            throw new \InvalidArgumentException('対象のセクションがありません.');
        }

        return $sections;
    }

    /**
     * @return list<string>
     */
    private static function split(mixed $value): array
    {
        if (null === $value || '' === $value) {
            return [];
        }

        return array_values(array_filter(array_map(trim(...), explode(',', (string) $value)), static fn (string $v): bool => '' !== $v));
    }
}
