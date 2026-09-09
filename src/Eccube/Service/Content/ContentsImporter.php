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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Block;
use Eccube\Entity\BlockPosition;
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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * アーカイブからコンテンツ定義を取り込む (eccube:contents:import).
 *
 * ページ・ブロック・メールテンプレートは既存の *ContentService::apply() へ委譲するため,
 * 検証は管理画面と同じ FormType を通る. レイアウトだけは対になる Service が無いため,
 * ここで upsert する (LayoutController::edit と同じく BlockPosition は貼り直す).
 *
 * テンプレートの本文はアーカイブに無く, リポジトリ本来の位置にあるものが使われる.
 */
class ContentsImporter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PageContentService $pageContentService,
        private readonly BlockContentService $blockContentService,
        private readonly MailTemplateContentService $mailTemplateContentService,
        private readonly UserDataFileService $userDataFileService,
        private readonly PageRepository $pageRepository,
        private readonly BlockRepository $blockRepository,
        private readonly MailTemplateRepository $mailTemplateRepository,
        private readonly LayoutRepository $layoutRepository,
        private readonly DeviceTypeRepository $deviceTypeRepository,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    /**
     * @param list<string> $sections
     *
     * @return array{results: list<ContentsImportResult>, warnings: list<string>}
     *
     * @throws ContentValidationException アーカイブを読めない場合, および --continue-on-error なしで
     *                                    取り込みに失敗した場合
     * @throws ContentWriteException      書き込めない場合. 実行ユーザーの権限の問題は全件に及ぶため,
     *                                    個別のエラーにせず中断する
     */
    public function import(
        string $dir,
        array $sections,
        bool $dryRun = false,
        bool $prune = false,
        bool $continueOnError = false,
    ): array {
        $dir = rtrim($dir, '/');
        if (!is_dir($dir)) {
            throw new ContentValidationException([sprintf('アーカイブが見つかりません: %s', $dir)]);
        }

        $warnings = $this->readManifest($dir);
        $results = [];

        foreach ($sections as $section) {
            if (ContentsArchive::SECTION_USER_DATA === $section) {
                $this->collect($results, $continueOnError, $this->importUserData($dir, $dryRun, $prune, $warnings));
                continue;
            }

            $path = $dir.'/'.ContentsArchive::fileName($section);
            if (!is_file($path)) {
                // 管理していないセクションとして扱う. 空として扱うと --prune が全件削除してしまう
                $warnings[] = sprintf('%s が無いため %s は取り込みません.', $path, $section);
                continue;
            }

            $rows = $this->parse($path);
            $this->collect($results, $continueOnError, match ($section) {
                ContentsArchive::SECTION_LAYOUTS => $this->importLayouts($rows, $dryRun, $prune),
                ContentsArchive::SECTION_BLOCKS => $this->importBlocks($rows, $dryRun, $prune),
                ContentsArchive::SECTION_PAGES => $this->importPages($rows, $dryRun, $prune),
                ContentsArchive::SECTION_MAIL_TEMPLATES => $this->importMailTemplates($rows, $dryRun, $prune),
                default => throw new \InvalidArgumentException(sprintf('Unknown section "%s".', $section)),
            });
        }

        return ['results' => $results, 'warnings' => $warnings];
    }

    /**
     * セクションの取り込みを実行し, 結果を積む.
     *
     * --continue-on-error を指定していない場合は最初のエラーで中断する.
     * 途中まで適用済みの状態は残るため, 事前の --dry-run を案内できるようにエラーを保持する.
     *
     * @param list<ContentsImportResult> $results
     * @param list<ContentsImportResult> $sectionResults
     *
     * @throws ContentValidationException
     */
    private function collect(array &$results, bool $continueOnError, array $sectionResults): void
    {
        foreach ($sectionResults as $result) {
            $results[] = $result;
            if ($result->isError() && !$continueOnError) {
                throw new ContentValidationException([sprintf('[%s] %s: %s', $result->section, $result->identifier, (string) $result->error)], '取り込みを中断しました.');
            }
        }
    }

    /**
     * @return list<string> 警告
     *
     * @throws ContentValidationException
     */
    private function readManifest(string $dir): array
    {
        $path = $dir.'/'.ContentsArchive::MANIFEST_FILE;
        if (!is_file($path)) {
            return [sprintf('%s が見つかりません. 書式版とテンプレートコードを確認できません.', $path)];
        }

        $manifest = $this->parse($path);
        $schema = (int) ($manifest['schema'] ?? 0);
        if (ContentsArchive::SCHEMA_VERSION !== $schema) {
            throw new ContentValidationException([sprintf('アーカイブの書式版 %d はこのバージョンでは扱えません (対応: %d).', $schema, ContentsArchive::SCHEMA_VERSION)]);
        }

        $warnings = [];
        $templateCode = (string) ($manifest['template_code'] ?? '');
        $current = (string) $this->eccubeConfig->get('eccube_theme_code');
        if ('' !== $templateCode && $templateCode !== $current) {
            // テンプレートの配置先が変わるため, 本文の解決先が書き出した環境と一致しない
            $warnings[] = sprintf(
                'テンプレートコードが異なります (アーカイブ: %s / この環境: %s). テンプレートの配置先が変わります.',
                $templateCode,
                $current
            );
        }

        return $warnings;
    }

    /**
     * @return array<int|string, mixed>
     *
     * @throws ContentValidationException
     */
    private function parse(string $path): array
    {
        try {
            $parsed = Yaml::parseFile($path);
        } catch (ParseException $e) {
            throw new ContentValidationException([sprintf('%s を読み込めません: %s', $path, $e->getMessage())]);
        }

        if (null === $parsed) {
            return [];
        }
        if (!is_array($parsed)) {
            throw new ContentValidationException([sprintf('%s の書式が不正です.', $path)]);
        }

        return $parsed;
    }

    /**
     * @param array<int|string, mixed> $rows
     *
     * @return list<ContentsImportResult>
     */
    private function importPages(array $rows, bool $dryRun, bool $prune): array
    {
        $section = ContentsArchive::SECTION_PAGES;
        $results = [];
        $keys = [];

        foreach ($rows as $row) {
            if (!is_array($row) || !ContentsArchive::isValidKey((string) ($row['route'] ?? ''))) {
                $results[] = ContentsImportResult::failed($section, (string) ($row['route'] ?? ''), 'route が不正です.');
                continue;
            }

            $route = (string) $row['route'];
            $keys[$route] = true;

            try {
                $Page = $this->pageContentService->findByRoute($route);
                if (null !== $Page) {
                    $this->assertTemplateExists(
                        $this->pageContentService->readTemplate($Page),
                        $this->pageContentService->getFilePath($Page)
                    );
                }

                $payload = ['route' => $route];
                foreach (['name', 'file_name', 'author', 'description', 'keyword', 'meta_robots', 'meta_tags'] as $field) {
                    if (array_key_exists($field, $row)) {
                        $payload[$field] = (string) $row[$field];
                    }
                }
                foreach (['pc_layout', 'sp_layout'] as $field) {
                    if (array_key_exists($field, $row)) {
                        $payload[$field] = $this->resolveLayoutId($row[$field]);
                    }
                }

                /** @var array{route: string} $payload */
                $result = $this->pageContentService->apply($payload, $dryRun);
                $results[] = ContentsImportResult::ok($section, $route, $result->status);
            } catch (ContentValidationException|\InvalidArgumentException $e) {
                $results[] = ContentsImportResult::failed($section, $route, $this->describe($e));
            }
        }

        if ($prune) {
            /** @var Page $Page */
            foreach ($this->pageRepository->getPageList() as $Page) {
                $route = (string) $Page->getUrl();
                // ユーザーが作成したページだけを削除できる (PageContentService::remove と同じ判定)
                if (isset($keys[$route]) || Page::EDIT_TYPE_USER !== $Page->getEditType()) {
                    continue;
                }
                $results[] = $this->remove($section, $route, $dryRun, fn () => $this->pageContentService->remove($Page));
            }
        }

        return $results;
    }

    /**
     * @param array<int|string, mixed> $rows
     *
     * @return list<ContentsImportResult>
     */
    private function importBlocks(array $rows, bool $dryRun, bool $prune): array
    {
        $section = ContentsArchive::SECTION_BLOCKS;
        $results = [];
        $keys = [];

        foreach ($rows as $row) {
            if (!is_array($row) || !ContentsArchive::isValidKey((string) ($row['file_name'] ?? ''))) {
                $results[] = ContentsImportResult::failed($section, (string) ($row['file_name'] ?? ''), 'file_name が不正です.');
                continue;
            }

            $fileName = (string) $row['file_name'];
            $deviceType = (int) ($row['device_type'] ?? DeviceType::DEVICE_TYPE_PC);
            $keys[$deviceType.':'.$fileName] = true;

            try {
                $Block = $this->blockContentService->findByFileName($fileName, $this->blockContentService->getDeviceType($deviceType));
                if (null !== $Block) {
                    $this->assertTemplateExists(
                        $this->blockContentService->readTemplate($Block),
                        $this->blockContentService->getFilePath($Block)
                    );
                }

                $payload = ['file_name' => $fileName, 'device_type' => $deviceType];
                if (array_key_exists('name', $row)) {
                    $payload['name'] = (string) $row['name'];
                }

                $result = $this->blockContentService->apply($payload, $dryRun);
                $results[] = ContentsImportResult::ok($section, $fileName, $result->status);
            } catch (ContentValidationException|\InvalidArgumentException $e) {
                $results[] = ContentsImportResult::failed($section, $fileName, $this->describe($e));
            }
        }

        if ($prune) {
            foreach ($this->deviceTypes() as $DeviceType) {
                /** @var Block $Block */
                foreach ($this->blockRepository->getList($DeviceType) as $Block) {
                    $fileName = (string) $Block->getFileName();
                    if (isset($keys[$DeviceType->getId().':'.$fileName]) || !$Block->isDeletable()) {
                        continue;
                    }
                    $results[] = $this->remove($section, $fileName, $dryRun, fn () => $this->blockContentService->remove($Block));
                }
            }
        }

        return $results;
    }

    /**
     * @param array<int|string, mixed> $rows
     *
     * @return list<ContentsImportResult>
     */
    private function importMailTemplates(array $rows, bool $dryRun, bool $prune): array
    {
        $section = ContentsArchive::SECTION_MAIL_TEMPLATES;
        $results = [];
        $keys = [];

        foreach ($rows as $row) {
            if (!is_array($row) || !ContentsArchive::isValidKey((string) ($row['file_name'] ?? ''))) {
                $results[] = ContentsImportResult::failed($section, (string) ($row['file_name'] ?? ''), 'file_name が不正です.');
                continue;
            }

            $fileName = (string) $row['file_name'];
            $keys[$this->mailTemplateContentService->normalizeFileName($fileName)] = true;

            try {
                $Mail = $this->mailTemplateContentService->findByFileName($fileName);
                if (null !== $Mail) {
                    $this->assertTemplateExists(
                        $this->mailTemplateContentService->readTemplate($Mail),
                        $this->mailTemplateContentService->getFilePath($Mail)
                    );
                }

                $payload = ['file_name' => $fileName];
                if (array_key_exists('name', $row)) {
                    $payload['name'] = (string) $row['name'];
                }
                // apply() の payload は subject, DB の列と一覧の JSON は mail_subject
                if (array_key_exists('mail_subject', $row)) {
                    $payload['subject'] = (string) $row['mail_subject'];
                }

                $result = $this->mailTemplateContentService->apply($payload, $dryRun);
                $results[] = ContentsImportResult::ok($section, $fileName, $result->status);
            } catch (ContentValidationException|\InvalidArgumentException $e) {
                $results[] = ContentsImportResult::failed($section, $fileName, $this->describe($e));
            }
        }

        if ($prune) {
            /** @var MailTemplate $Mail */
            foreach ($this->mailTemplateRepository->findBy([], ['id' => 'ASC']) as $Mail) {
                $fileName = (string) $Mail->getFileName();
                if (isset($keys[$fileName]) || !$Mail->isDeletable()) {
                    continue;
                }
                $results[] = $this->remove(
                    $section,
                    pathinfo($fileName, PATHINFO_FILENAME),
                    $dryRun,
                    fn () => $this->mailTemplateContentService->remove($Mail)
                );
            }
        }

        return $results;
    }

    /**
     * レイアウトと, そこへのブロックの配置を取り込む.
     *
     * @param array<int|string, mixed> $rows
     *
     * @return list<ContentsImportResult>
     */
    private function importLayouts(array $rows, bool $dryRun, bool $prune): array
    {
        $section = ContentsArchive::SECTION_LAYOUTS;
        $results = [];
        $keys = [];

        foreach ($rows as $row) {
            $name = is_array($row) ? (string) ($row['name'] ?? '') : '';
            if ('' === $name) {
                $results[] = ContentsImportResult::failed($section, '', 'name が空です.');
                continue;
            }

            $keys[$name] = true;

            try {
                /** @var array<string, mixed> $row */
                $results[] = ContentsImportResult::ok($section, $name, $this->applyLayout($row, $name, $dryRun));
            } catch (ContentValidationException|\InvalidArgumentException $e) {
                $results[] = ContentsImportResult::failed($section, $name, $this->describe($e));
            }
        }

        if ($prune) {
            /** @var Layout $Layout */
            foreach ($this->layoutRepository->findBy([], ['id' => 'ASC']) as $Layout) {
                // ページから参照されているレイアウトは削除しない (Layout::isDeletable)
                if (isset($keys[$Layout->getName()]) || !$Layout->isDeletable()) {
                    continue;
                }
                $results[] = $this->remove($section, $Layout->getName(), $dryRun, function () use ($Layout): ContentResult {
                    $id = $Layout->getId();
                    $name = $Layout->getName();
                    $this->entityManager->remove($Layout);
                    $this->entityManager->flush();

                    return new ContentResult(ContentStatus::Removed, $id, $name);
                });
            }
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $row
     *
     * @throws ContentValidationException
     */
    private function applyLayout(array $row, string $name, bool $dryRun): ContentStatus
    {
        $Layout = $this->findLayoutByName($name);
        $isNew = null === $Layout;
        $deviceTypeId = (int) ($row['device_type'] ?? DeviceType::DEVICE_TYPE_PC);
        $DeviceType = $this->deviceTypeRepository->find($deviceTypeId);
        if (null === $DeviceType) {
            throw new ContentValidationException([sprintf('デバイス種別が存在しません: %s', (string) $deviceTypeId)]);
        }

        $desired = $this->desiredBlockPositions($row, $DeviceType, $name);

        if (null !== $Layout
            && $deviceTypeId === $Layout->getDeviceType()?->getId()
            && self::signature($desired) === self::signature($this->currentBlockPositions($Layout))) {
            return ContentStatus::Unchanged;
        }

        if ($dryRun) {
            return $isNew ? ContentStatus::Created : ContentStatus::Updated;
        }

        if (null === $Layout) {
            $Layout = new Layout();
        }
        $Layout->setName($name);
        $Layout->setDeviceType($DeviceType);

        return $this->entityManager->wrapInTransaction(function () use ($Layout, $desired, $isNew): ContentStatus {
            $this->entityManager->persist($Layout);
            $this->entityManager->flush();

            // 配置は差分更新せず貼り直す (LayoutController::edit と同じ)
            foreach ($Layout->getBlockPositions() as $BlockPosition) {
                $Layout->removeBlockPosition($BlockPosition);
                $this->entityManager->remove($BlockPosition);
            }
            $this->entityManager->flush();

            foreach ($desired as $position) {
                $BlockPosition = new BlockPosition();
                $BlockPosition->setLayout($Layout)
                    ->setLayoutId($Layout->getId())
                    ->setBlock($position['Block'])
                    ->setBlockId($position['Block']->getId())
                    ->setSection($position['section'])
                    ->setBlockRow($position['row']);
                $Layout->addBlockPosition($BlockPosition);
                $this->entityManager->persist($BlockPosition);
            }
            $this->entityManager->flush();

            return $isNew ? ContentStatus::Created : ContentStatus::Updated;
        });
    }

    /**
     * アーカイブが求める配置.
     *
     * @param array<string, mixed> $row
     *
     * @return list<array{section: int, row: int|null, block: string, Block: Block}>
     *
     * @throws ContentValidationException
     */
    private function desiredBlockPositions(array $row, DeviceType $DeviceType, string $layoutName): array
    {
        $positions = [];
        $blocks = $row['blocks'] ?? [];
        if (!is_array($blocks)) {
            throw new ContentValidationException([sprintf('レイアウト %s の blocks の書式が不正です.', $layoutName)]);
        }

        foreach ($blocks as $entry) {
            if (!is_array($entry)) {
                throw new ContentValidationException([sprintf('レイアウト %s の blocks の書式が不正です.', $layoutName)]);
            }

            $sectionId = ContentsArchive::blockSectionId((string) ($entry['section'] ?? ''));
            if (null === $sectionId) {
                throw new ContentValidationException([sprintf('レイアウト %s に未知の配置場所があります: %s', $layoutName, (string) ($entry['section'] ?? ''))]);
            }

            $fileName = (string) ($entry['block'] ?? '');
            // ブロックはデバイス種別との組で一意. 既定はレイアウトのデバイス種別を使う
            $blockDeviceTypeId = (int) ($entry['device_type'] ?? $DeviceType->getId());
            $BlockDeviceType = $this->deviceTypeRepository->find($blockDeviceTypeId);
            $Block = null === $BlockDeviceType ? null : $this->blockRepository->findOneBy([
                'file_name' => $fileName,
                'DeviceType' => $BlockDeviceType,
            ]);
            if (null === $Block) {
                throw new ContentValidationException([sprintf('レイアウト %s が参照するブロックが見つかりません: %s (デバイス種別 %d)', $layoutName, $fileName, $blockDeviceTypeId)]);
            }

            $positions[] = [
                'section' => $sectionId,
                'row' => null === ($entry['row'] ?? null) ? null : (int) $entry['row'],
                'block' => $fileName,
                'Block' => $Block,
            ];
        }

        return self::sortPositions($positions);
    }

    /**
     * 現在の配置. desiredBlockPositions() と同じ形にして比較する.
     *
     * @return list<array{section: int, row: int|null, block: string, Block: Block}>
     */
    private function currentBlockPositions(Layout $Layout): array
    {
        $positions = [];
        foreach ($Layout->getBlockPositions() as $BlockPosition) {
            $Block = $BlockPosition->getBlock();
            if (null === $Block) {
                continue;
            }
            $positions[] = [
                'section' => $BlockPosition->getSection(),
                'row' => $BlockPosition->getBlockRow(),
                'block' => (string) $Block->getFileName(),
                'Block' => $Block,
            ];
        }

        return self::sortPositions($positions);
    }

    /**
     * 比較用の, エンティティを含まない形.
     *
     * @param list<array{section: int, row: int|null, block: string, Block: Block}> $positions
     *
     * @return list<array{section: int, row: int|null, block: string}>
     */
    private static function signature(array $positions): array
    {
        return array_map(
            static fn (array $p): array => ['section' => $p['section'], 'row' => $p['row'], 'block' => $p['block']],
            $positions
        );
    }

    /**
     * @param list<array{section: int, row: int|null, block: string, Block: Block}> $positions
     *
     * @return list<array{section: int, row: int|null, block: string, Block: Block}>
     */
    private static function sortPositions(array $positions): array
    {
        usort($positions, static fn (array $a, array $b): int => [$a['section'], $a['row'] ?? 0, $a['block']]
            <=> [$b['section'], $b['row'] ?? 0, $b['block']]);

        return $positions;
    }

    /**
     * html/user_data をアーカイブの内容へ合わせる.
     *
     * @param list<string> $warnings
     *
     * @return list<ContentsImportResult>
     */
    private function importUserData(string $dir, bool $dryRun, bool $prune, array &$warnings): array
    {
        $section = ContentsArchive::SECTION_USER_DATA;
        $source = $dir.'/'.ContentsArchive::SECTION_USER_DATA;
        if (!is_dir($source)) {
            $warnings[] = sprintf('%s が無いため %s は取り込みません.', $source, $section);

            return [];
        }

        $results = [];
        $keys = [];
        $finder = Finder::create()->in($source)->files()->ignoreDotFiles(false)->sortByName();
        foreach ($finder as $file) {
            $relative = str_replace(\DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());
            $keys[$relative] = true;

            try {
                // 配置先の検証 (境界検査と拡張子の許可リスト) は UserDataFileService が行う
                $result = $this->userDataFileService->write($relative, (string) file_get_contents($file->getPathname()), $dryRun);
                $results[] = ContentsImportResult::ok($section, $relative, $result->status);
            } catch (ContentValidationException $e) {
                // アーカイブは Git 管理下にあり .gitkeep などが混ざり得るため, 中断せず知らせる
                $warnings[] = sprintf('%s は配置できないため読み飛ばしました: %s', $relative, implode(' / ', $e->getErrors()));
            }
        }

        if ($prune) {
            foreach ($this->userDataFileService->list(null, true) as $entry) {
                if ($entry['is_dir'] || isset($keys[$entry['path']])) {
                    continue;
                }
                $results[] = $this->remove(
                    $section,
                    $entry['path'],
                    $dryRun,
                    fn () => $this->userDataFileService->remove($entry['path'])
                );
            }
        }

        return $results;
    }

    /**
     * @param \Closure(): ContentResult $remove
     */
    private function remove(string $section, string $identifier, bool $dryRun, \Closure $remove): ContentsImportResult
    {
        if ($dryRun) {
            return ContentsImportResult::ok($section, $identifier, ContentStatus::Removed);
        }

        try {
            return ContentsImportResult::ok($section, $identifier, $remove()->status);
        } catch (ContentValidationException|\LogicException $e) {
            return ContentsImportResult::failed($section, $identifier, $this->describe($e));
        }
    }

    /**
     * ページに紐づけるレイアウトの ID を求める.
     *
     * dtb_layout.id は環境ごとに変わるため, アーカイブは名前で参照する.
     * 名前は一意とは限らないので, 複数一致したら当てずっぽうに選ばずエラーにする.
     *
     * @throws ContentValidationException
     */
    private function resolveLayoutId(mixed $name): ?int
    {
        if (null === $name || '' === $name) {
            return null;
        }

        $Layout = $this->findLayoutByName((string) $name);
        if (null === $Layout) {
            throw new ContentValidationException([sprintf('レイアウトが見つかりません: %s', (string) $name)]);
        }

        return $Layout->getId();
    }

    /**
     * @throws ContentValidationException 同名のレイアウトが複数ある場合
     */
    private function findLayoutByName(string $name): ?Layout
    {
        /** @var list<Layout> $Layouts */
        $Layouts = $this->layoutRepository->findBy(['name' => $name]);
        if (count($Layouts) > 1) {
            throw new ContentValidationException([sprintf('レイアウト名が重複しているため特定できません: %s. 名前を一意にしてください.', $name)]);
        }

        return $Layouts[0] ?? null;
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

    /**
     * 本文はアーカイブに含まれないため, 既存レコードのテンプレートが解決できないと取り込めない.
     * FormType の NotBlank だけでは原因が分からないので, 手前で理由を添えて弾く.
     *
     * @throws ContentValidationException
     */
    private function assertTemplateExists(string $body, string $path): void
    {
        if ('' !== trim($body)) {
            return;
        }

        throw new ContentValidationException([sprintf('テンプレートが見つかりません: %s. リポジトリへ配置するか, アーカイブから該当の行を削除してください.', $path)]);
    }

    private function describe(\Throwable $e): string
    {
        return $e instanceof ContentValidationException
            ? implode(' / ', $e->getErrors())
            : $e->getMessage();
    }
}
