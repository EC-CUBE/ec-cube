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
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Exception\ContentValidationException;
use Eccube\Exception\ContentWriteException;
use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\PageRepository;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

/**
 * ページ (dtb_page と app/template 配下の twig) を対で扱う.
 *
 * 管理画面 (PageController) と CLI (eccube:page:*) の双方から使用する.
 * 入力値の検証は管理画面と同じ MainEditType を submit して行うため,
 * ルーティング名 / ファイル名の重複チェックや TwigLint も同じものが効く.
 */
class PageContentService
{
    use TemplateRemovalTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PageRepository $pageRepository,
        private readonly PageLayoutRepository $pageLayoutRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twig,
        private readonly Filesystem $filesystem,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    /**
     * ルーティング名 (dtb_page.url) を鍵にページを取得する.
     *
     * dtb_page.url が保持するのは実際の URL ではなくルート名 (例: product_list) で,
     * 管理画面のページ管理も同じ値を「ルーティング名」列に表示する.
     */
    public function findByRoute(string $route): ?Page
    {
        return $this->pageRepository->findOneBy(['url' => $route]);
    }

    /**
     * ユーザーが作成したページか.
     *
     * 既定ページ (EDIT_TYPE_DEFAULT 以上) はルーティング名・ファイル名を変更できず,
     * テンプレートの配置先も user_data ではなくテーマのディレクトリになる.
     */
    public function isUserDataPage(Page $Page): bool
    {
        return $Page->getEditType() < Page::EDIT_TYPE_DEFAULT;
    }

    public function getTemplateDir(Page $Page): string
    {
        return $this->isUserDataPage($Page)
            ? (string) $this->eccubeConfig->get('eccube_theme_user_data_dir')
            : (string) $this->eccubeConfig->get('eccube_theme_front_dir');
    }

    public function getFilePath(Page $Page): string
    {
        return $this->getTemplateDir($Page).'/'.$Page->getFileName().'.twig';
    }

    /**
     * テンプレートの本文を取得する.
     *
     * 書き込み先のファイルがあればそれを読む. 無い場合は管理画面の編集欄と同じ経路
     * (twig のローダ) へフォールバックし, コアのテンプレートを取得する.
     * twig のローダはテンプレートの探索結果をプロセス内でキャッシュするため,
     * 直前に書き出したファイルを読み落とさないようファイルを優先する.
     */
    public function readTemplate(Page $Page): string
    {
        $filePath = $this->getFilePath($Page);
        if (is_file($filePath)) {
            return (string) file_get_contents($filePath);
        }

        $namespace = $this->isUserDataPage($Page) ? '@user_data/' : '';

        try {
            return $this->twig->getLoader()
                ->getSourceContext($namespace.$Page->getFileName().'.twig')
                ->getCode();
        } catch (LoaderError) {
            return '';
        }
    }

    /**
     * ルーティング名を鍵にページを登録・更新する (upsert).
     *
     * 指定しなかった項目は既存の値を維持するため, 同じ入力を複数回適用しても結果は変わらない.
     *
     * @param array{route: string, name?: string, file_name?: string, body?: string, author?: string, description?: string, keyword?: string, meta_robots?: string, meta_tags?: string, pc_layout?: string|int|null, sp_layout?: string|int|null} $payload
     *
     * @throws ContentValidationException
     * @throws ContentWriteException      テンプレートファイルを書き出せない場合
     */
    public function apply(array $payload, bool $dryRun = false): ContentResult
    {
        $route = $payload['route'];
        $Page = $this->findByRoute($route);
        $isNew = null === $Page;
        $Page ??= $this->pageRepository->newPage();

        $previousFileName = $Page->getFileName();
        // 新規登録時は比較対象が無い (未設定のゲッタは null を返すため呼び出さない)
        $before = $isNew ? [] : $this->snapshotOfCurrentLayouts($Page);
        $beforeBody = $isNew ? '' : StringUtil::convertLineFeed($this->readTemplate($Page));

        $form = $this->formFactory->create(MainEditType::class, $Page, ['csrf_protection' => false]);
        $form->submit($this->toFormData($payload, $Page, $isNew, $beforeBody), false);

        if (!$form->isValid()) {
            throw ContentValidationException::fromForm($form);
        }

        $body = StringUtil::convertLineFeed((string) $form->get('tpl_data')->getData());
        /** @var Layout|null $PcLayout */
        $PcLayout = $form['PcLayout']->getData();
        /** @var Layout|null $SpLayout */
        $SpLayout = $form['SpLayout']->getData();

        $fieldChanges = self::diffFields($before, $this->snapshotWithLayouts($Page, $PcLayout, $SpLayout));
        $filePath = $this->getFilePath($Page);
        $fileChanges = $beforeBody === $body && $previousFileName === $Page->getFileName()
            ? []
            : [$filePath => [$beforeBody, $body]];

        if ($dryRun) {
            if (!$isNew) {
                // submit で書き換えたエンティティを DB の内容へ戻す (dry-run は永続化しない)
                $this->entityManager->refresh($Page);
            }

            return new ContentResult(
                $this->resolveStatus($isNew, $fieldChanges, $fileChanges),
                $Page->getId(),
                $route,
                [],
                [],
                $fieldChanges,
                $fileChanges
            );
        }

        if (!$isNew && [] === $fieldChanges && [] === $fileChanges) {
            return new ContentResult(ContentStatus::Unchanged, $Page->getId(), $route);
        }

        return $this->save($Page, $body, $PcLayout, $SpLayout, $isNew ? null : $previousFileName)
            ->withChanges($fieldChanges, $fileChanges);
    }

    /**
     * ページを永続化し, テンプレートファイルを書き出す.
     *
     * 検証済みのエンティティを受け取る前提のため, 管理画面からは
     * $form->isValid() を通過した後に呼び出す.
     *
     * DB とファイルはトランザクションで対にする. 先にコミットするとテンプレートの書き出しに
     * 失敗したときレコードだけが残り, そのページの表示が 500 になる. 権限を分離した構成では
     * app/template への書き込みだけが失敗し得るため, ロールバックして整合を保つ.
     *
     * @throws ContentWriteException テンプレートファイルを書き出せない場合
     */
    public function save(Page $Page, string $body, ?Layout $PcLayout, ?Layout $SpLayout, ?string $previousFileName): ContentResult
    {
        $isNew = null === $Page->getId();

        return $this->entityManager->wrapInTransaction(function () use ($Page, $body, $PcLayout, $SpLayout, $previousFileName, $isNew): ContentResult {
            $this->entityManager->persist($Page);
            $this->entityManager->flush();

            $templateDir = $this->getTemplateDir($Page);
            $filePath = $templateDir.'/'.$Page->getFileName().'.twig';

            try {
                $this->filesystem->dumpFile($filePath, StringUtil::convertLineFeed($body));
            } catch (IOException $e) {
                throw ContentWriteException::forWrite($filePath, $e);
            }

            $removedPaths = [];
            // 更新でファイル名を変更した場合, 以前のファイルを削除する
            if (null !== $previousFileName && $Page->getFileName() !== $previousFileName) {
                $oldFilePath = $templateDir.'/'.$previousFileName.'.twig';
                if ($this->filesystem->exists($oldFilePath)) {
                    try {
                        $this->filesystem->remove($oldFilePath);
                    } catch (IOException $e) {
                        throw ContentWriteException::forRemove($oldFilePath, $e);
                    }
                    $removedPaths[] = $oldFilePath;
                }
            }

            $this->replaceLayouts($Page, $PcLayout, $SpLayout);

            return new ContentResult(
                $isNew ? ContentStatus::Created : ContentStatus::Updated,
                $Page->getId(),
                (string) $Page->getUrl(),
                [$filePath],
                $removedPaths
            );
        });
    }

    /**
     * ページとテンプレートファイルを削除する.
     *
     * ユーザーが作成したページ (EDIT_TYPE_USER) のみ削除できる.
     *
     * テンプレートを一時退避してから DB を削除する (TemplateRemovalTrait 参照).
     *
     * @throws ContentWriteException テンプレートファイルを削除できない場合
     */
    public function remove(Page $Page): ContentResult
    {
        if (Page::EDIT_TYPE_USER !== $Page->getEditType()) {
            throw new \LogicException(sprintf('Page "%s" is not removable.', (string) $Page->getUrl()));
        }

        $id = $Page->getId();
        $route = (string) $Page->getUrl();

        $removedPaths = $this->removeTemplatesAround([$this->getFilePath($Page)], function () use ($Page): void {
            $this->entityManager->remove($Page);
            $this->entityManager->flush();
        });

        return new ContentResult(ContentStatus::Removed, $id, $route, [], $removedPaths);
    }

    /**
     * ページに紐づくレイアウトを貼り替える.
     */
    private function replaceLayouts(Page $Page, ?Layout $PcLayout, ?Layout $SpLayout): void
    {
        foreach ($Page->getPageLayouts() as $PageLayout) {
            $Page->removePageLayout($PageLayout);
            $this->entityManager->remove($PageLayout);
            $this->entityManager->flush();
        }

        $LastPageLayout = $this->pageLayoutRepository->findOneBy([], ['sort_no' => 'DESC']);
        $sortNo = null === $LastPageLayout ? 0 : $LastPageLayout->getSortNo();

        foreach ([$PcLayout, $SpLayout] as $Layout) {
            if (null === $Layout) {
                continue;
            }

            $PageLayout = new PageLayout();
            $PageLayout->setLayoutId($Layout->getId());
            $PageLayout->setLayout($Layout);
            $PageLayout->setPageId($Page->getId());
            $PageLayout->setSortNo($sortNo++);
            $PageLayout->setPage($Page);

            $this->entityManager->persist($PageLayout);
            $this->entityManager->flush();
        }
    }

    /**
     * @param array<string, string|int|null> $payload
     *
     * @return array<string, string>
     */
    private function toFormData(array $payload, Page $Page, bool $isNew, string $currentBody): array
    {
        // 本文は常に送信する (未指定なら現在の内容を維持する)
        $data = ['tpl_data' => (string) ($payload['body'] ?? $currentBody)];

        $fields = ['name', 'author', 'description', 'keyword', 'meta_robots', 'meta_tags'];
        // 既定ページはルーティング名・ファイル名を変更できない (管理画面と同じ制約)
        if ($this->isUserDataPage($Page)) {
            $fields[] = 'file_name';
        }
        foreach ($fields as $field) {
            if (array_key_exists($field, $payload)) {
                $data[$field] = (string) $payload[$field];
            }
        }

        if ($isNew) {
            // フォーム側の項目名は dtb_page の列に合わせて url のまま
            $data['url'] = (string) $payload['route'];
            $data['file_name'] ??= (string) $payload['route'];
        }

        foreach (['pc_layout' => 'PcLayout', 'sp_layout' => 'SpLayout'] as $key => $field) {
            if (array_key_exists($key, $payload)) {
                $data[$field] = null === $payload[$key] ? '' : (string) $payload[$key];
            }
        }

        return $data;
    }

    /**
     * 変更前のスナップショット. レイアウトは現在紐づいているものから取得する.
     *
     * @return array<string, string>
     */
    private function snapshotOfCurrentLayouts(Page $Page): array
    {
        $snapshot = $this->snapshotFields($Page) + ['PcLayout' => '', 'SpLayout' => ''];
        foreach ($Page->getLayouts() as $Layout) {
            $field = DeviceType::DEVICE_TYPE_PC === $Layout->getDeviceType()->getId() ? 'PcLayout' : 'SpLayout';
            $snapshot[$field] = (string) $Layout->getId();
        }

        return $snapshot;
    }

    /**
     * 変更後のスナップショット. 引数のレイアウトをそのまま反映する.
     *
     * 引数の null 有無で変更前と変更後を区別してはいけない. 両方のレイアウトを外す操作では
     * 双方が null になり, 現在の値を読み直すと差分が出ず Unchanged で早期 return してしまう
     * (レイアウトが外れない).
     *
     * @return array<string, string>
     */
    private function snapshotWithLayouts(Page $Page, ?Layout $PcLayout, ?Layout $SpLayout): array
    {
        return $this->snapshotFields($Page) + [
            'PcLayout' => null === $PcLayout ? '' : (string) $PcLayout->getId(),
            'SpLayout' => null === $SpLayout ? '' : (string) $SpLayout->getId(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function snapshotFields(Page $Page): array
    {
        return [
            'name' => (string) $Page->getName(),
            'route' => (string) $Page->getUrl(),
            'file_name' => (string) $Page->getFileName(),
            'author' => (string) $Page->getAuthor(),
            'description' => (string) $Page->getDescription(),
            'keyword' => (string) $Page->getKeyword(),
            'meta_robots' => (string) $Page->getMetaRobots(),
            'meta_tags' => (string) $Page->getMetaTags(),
        ];
    }

    /**
     * @param array<string, string> $before
     * @param array<string, string> $after
     *
     * @return array<string, array{0: string, 1: string}>
     */
    private static function diffFields(array $before, array $after): array
    {
        $changes = [];
        foreach ($after as $field => $value) {
            $previous = $before[$field] ?? '';
            if ($previous !== $value) {
                $changes[$field] = [$previous, $value];
            }
        }

        return $changes;
    }

    /**
     * @param array<string, array{0: string, 1: string}> $fieldChanges
     * @param array<string, array{0: string, 1: string}> $fileChanges
     */
    private function resolveStatus(bool $isNew, array $fieldChanges, array $fileChanges): ContentStatus
    {
        if ($isNew) {
            return ContentStatus::Created;
        }

        return [] === $fieldChanges && [] === $fileChanges ? ContentStatus::Unchanged : ContentStatus::Updated;
    }
}
