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

namespace Eccube\Service\Content;

use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Block;
use Eccube\Entity\Layout;
use Eccube\Entity\MailTemplate;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Exception\ContentValidationException;
use Eccube\Exception\ContentWriteException;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\LayoutRepository;
use Eccube\Repository\MailTemplateRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageRepository;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * コンテンツ定義をアーカイブへ書き出す (eccube:contents:export).
 *
 * 書き出すのは DB 側の定義だけで, テンプレートの本文は複製しない.
 * 理由は ContentsArchive のクラスコメントを参照.
 */
class ContentsExporter
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly BlockRepository $blockRepository,
        private readonly MailTemplateRepository $mailTemplateRepository,
        private readonly LayoutRepository $layoutRepository,
        private readonly DeviceTypeRepository $deviceTypeRepository,
        private readonly UserDataFileService $userDataFileService,
        private readonly EccubeConfig $eccubeConfig,
        private readonly Filesystem $filesystem,
    ) {
    }

    /**
     * @param list<string> $sections
     *
     * @return list<array{section: string, count: int, path: string}>
     *
     * @throws ContentValidationException レイアウト名が一意でない場合
     * @throws ContentWriteException      書き出せない場合
     */
    public function export(string $dir, array $sections, bool $dryRun = false): array
    {
        $dir = rtrim($dir, '/');
        $summary = [];

        $this->write($dir.'/'.ContentsArchive::MANIFEST_FILE, ContentsArchive::dump($this->manifest()), $dryRun);
        $summary[] = ['section' => 'manifest', 'count' => 1, 'path' => $dir.'/'.ContentsArchive::MANIFEST_FILE];

        foreach ($sections as $section) {
            if (ContentsArchive::SECTION_USER_DATA === $section) {
                $summary[] = $this->exportUserData($dir, $dryRun);
                continue;
            }

            $rows = match ($section) {
                ContentsArchive::SECTION_LAYOUTS => $this->layoutRows(),
                ContentsArchive::SECTION_BLOCKS => $this->blockRows(),
                ContentsArchive::SECTION_PAGES => $this->pageRows(),
                ContentsArchive::SECTION_MAIL_TEMPLATES => $this->mailTemplateRows(),
                default => throw new \InvalidArgumentException(sprintf('Unknown section "%s".', $section)),
            };

            $path = $dir.'/'.ContentsArchive::fileName($section);
            $this->write($path, ContentsArchive::dump($rows), $dryRun);
            $summary[] = ['section' => $section, 'count' => count($rows), 'path' => $path];
        }

        return $summary;
    }

    /**
     * @return array<string, int|string>
     */
    private function manifest(): array
    {
        // 再エクスポートで差分が出ないよう, 時刻は含めない
        return [
            'schema' => ContentsArchive::SCHEMA_VERSION,
            'eccube_version' => Constant::VERSION,
            'template_code' => (string) $this->eccubeConfig->get('eccube_theme_code'),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function pageRows(): array
    {
        $rows = [];
        /** @var Page $Page */
        foreach ($this->pageRepository->getPageList() as $Page) {
            $layouts = ['pc_layout' => null, 'sp_layout' => null];
            foreach ($Page->getLayouts() as $Layout) {
                $key = DeviceType::DEVICE_TYPE_PC === $Layout->getDeviceType()?->getId() ? 'pc_layout' : 'sp_layout';
                $layouts[$key] = $Layout->getName();
            }

            $rows[] = [
                'route' => (string) $Page->getUrl(),
                'name' => (string) $Page->getName(),
                'file_name' => (string) $Page->getFileName(),
                'author' => self::nullIfEmpty($Page->getAuthor()),
                'description' => self::nullIfEmpty($Page->getDescription()),
                'keyword' => self::nullIfEmpty($Page->getKeyword()),
                'meta_robots' => self::nullIfEmpty($Page->getMetaRobots()),
                'meta_tags' => self::nullIfEmpty($Page->getMetaTags()),
                'pc_layout' => $layouts['pc_layout'],
                'sp_layout' => $layouts['sp_layout'],
            ];
        }

        return self::sortBy($rows, 'route');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function blockRows(): array
    {
        $rows = [];
        foreach ($this->deviceTypes() as $DeviceType) {
            /** @var Block $Block */
            foreach ($this->blockRepository->getList($DeviceType) as $Block) {
                $rows[] = [
                    'file_name' => (string) $Block->getFileName(),
                    'name' => (string) $Block->getName(),
                    'device_type' => (int) $DeviceType->getId(),
                ];
            }
        }

        return self::sortBy($rows, 'file_name');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function mailTemplateRows(): array
    {
        $rows = [];
        /** @var MailTemplate $Mail */
        foreach ($this->mailTemplateRepository->findBy([], ['id' => 'ASC']) as $Mail) {
            $rows[] = [
                // dtb_mail_template.file_name は "Mail/xxx.twig". 鍵は基底名にする
                'file_name' => pathinfo((string) $Mail->getFileName(), PATHINFO_FILENAME),
                'name' => (string) $Mail->getName(),
                'mail_subject' => (string) $Mail->getMailSubject(),
            ];
        }

        return self::sortBy($rows, 'file_name');
    }

    /**
     * @return list<array<string, mixed>>
     *
     * @throws ContentValidationException
     */
    private function layoutRows(): array
    {
        $rows = [];
        /** @var list<Layout> $Layouts */
        $Layouts = $this->layoutRepository->findBy([], ['id' => 'ASC']);
        $this->assertLayoutNamesAreUnique($Layouts);

        foreach ($Layouts as $Layout) {
            $layoutDeviceType = (int) $Layout->getDeviceType()?->getId();
            $blocks = [];
            foreach ($Layout->getBlockPositions() as $BlockPosition) {
                $Block = $BlockPosition->getBlock();
                $sectionName = ContentsArchive::blockSectionName((int) $BlockPosition->getSection());
                if (null === $Block || null === $sectionName) {
                    // 対応するブロックが無い / 未知の配置場所は再現できないため書き出さない
                    continue;
                }

                $entry = [
                    'section' => $sectionName,
                    'block' => (string) $Block->getFileName(),
                    'row' => null === $BlockPosition->getBlockRow() ? null : (int) $BlockPosition->getBlockRow(),
                ];
                // 取り込み時はレイアウトのデバイス種別でブロックを探すため, 食い違う場合だけ明示する
                $blockDeviceType = (int) $Block->getDeviceType()?->getId();
                if ($blockDeviceType !== $layoutDeviceType) {
                    $entry['device_type'] = $blockDeviceType;
                }
                $blocks[] = $entry;
            }

            usort($blocks, static fn (array $a, array $b): int => [$a['section'], $a['row'] ?? 0, $a['block']]
                <=> [$b['section'], $b['row'] ?? 0, $b['block']]);

            $rows[] = [
                'name' => $Layout->getName(),
                'device_type' => $layoutDeviceType,
                'blocks' => $blocks,
            ];
        }

        return self::sortBy($rows, 'name');
    }

    /**
     * @param list<Layout> $Layouts
     *
     * @throws ContentValidationException
     */
    private function assertLayoutNamesAreUnique(array $Layouts): void
    {
        // dtb_layout.id は環境ごとに変わる (IDENTITY 採番) ため, アーカイブは名前で参照する.
        // layout_name に一意制約が無いので, 一意でない状態はここで検出して知らせる.
        // 名前で当てずっぽうに解決すると, 取り込み先で別のレイアウトへ静かに貼り替わる
        $errors = [];
        $seen = [];
        foreach ($Layouts as $Layout) {
            $name = $Layout->getName();
            if ('' === $name) {
                $errors[] = sprintf('レイアウト名が空です (id=%s). 名前を設定してください.', (string) $Layout->getId());
                continue;
            }
            if (isset($seen[$name])) {
                $errors[] = sprintf(
                    'レイアウト名が重複しています: %s (id=%s, id=%s). 名前を一意にしてください.',
                    $name,
                    $seen[$name],
                    (string) $Layout->getId()
                );
                continue;
            }
            $seen[$name] = (string) $Layout->getId();
        }

        if ([] !== $errors) {
            throw new ContentValidationException($errors);
        }
    }

    /**
     * html/user_data をアーカイブへミラーする.
     *
     * @return array{section: string, count: int, path: string}
     *
     * @throws ContentWriteException
     */
    private function exportUserData(string $dir, bool $dryRun): array
    {
        $target = $dir.'/'.ContentsArchive::SECTION_USER_DATA;
        $count = 0;
        foreach ($this->userDataFileService->list(null, true) as $entry) {
            if ($entry['is_dir']) {
                continue;
            }
            $this->write(
                $target.'/'.$entry['path'],
                $this->userDataFileService->read($entry['path']),
                $dryRun
            );
            ++$count;
        }

        return ['section' => ContentsArchive::SECTION_USER_DATA, 'count' => $count, 'path' => $target];
    }

    /**
     * @throws ContentWriteException
     */
    private function write(string $path, string $contents, bool $dryRun): void
    {
        if ($dryRun) {
            return;
        }

        try {
            $this->filesystem->dumpFile($path, $contents);
        } catch (IOException $e) {
            throw ContentWriteException::forWrite($path, $e);
        }
    }

    /**
     * @return list<DeviceType>
     */
    private function deviceTypes(): array
    {
        /** @var list<DeviceType> $DeviceTypes */
        $DeviceTypes = $this->deviceTypeRepository->findBy([], ['id' => 'ASC']);

        return $DeviceTypes;
    }

    private static function nullIfEmpty(?string $value): ?string
    {
        return null === $value || '' === $value ? null : $value;
    }

    /**
     * 差分が安定するように鍵で並べ替える.
     *
     * @param list<array<string, mixed>> $rows
     *
     * @return list<array<string, mixed>>
     */
    private static function sortBy(array $rows, string $key): array
    {
        usort($rows, static fn (array $a, array $b): int => strcmp((string) $a[$key], (string) $b[$key]));

        return $rows;
    }
}
