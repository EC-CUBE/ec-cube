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
use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\PageRepository;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

/**
 * ページ (dtb_page と app/template 配下の twig) を対で扱う.
 *
 * 管理画面 (PageController) と CLI (eccube:page:*) の双方から使用する.
 * 入力値の検証は管理画面と同じ MainEditType を submit して行うため,
 * URL / ファイル名の重複チェックや TwigLint も同じものが効く.
 */
class PageContentService
{
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

    public function findByUrl(string $url): ?Page
    {
        return $this->pageRepository->findOneBy(['url' => $url]);
    }

    /**
     * ユーザーが作成したページか.
     *
     * 既定ページ (EDIT_TYPE_DEFAULT 以上) は URL・ファイル名を変更できず,
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
     * URL を鍵にページを登録・更新する (upsert).
     *
     * 指定しなかった項目は既存の値を維持するため, 同じ入力を複数回適用しても結果は変わらない.
     *
     * @param array{url: string, name?: string, file_name?: string, body?: string, author?: string, description?: string, keyword?: string, meta_robots?: string, meta_tags?: string, pc_layout?: string|int|null, sp_layout?: string|int|null} $payload
     *
     * @throws ContentValidationException
     */
    public function apply(array $payload, bool $dryRun = false): ContentResult
    {
        $url = $payload['url'];
        $Page = $this->findByUrl($url);
        $isNew = null === $Page;
        if (null === $Page) {
            $Page = $this->pageRepository->newPage();
        }

        $previousFileName = $Page->getFileName();
        // 新規登録時は比較対象が無い (未設定のゲッタは null を返すため呼び出さない)
        $before = $isNew ? [] : $this->snapshot($Page);
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

        $fieldChanges = self::diffFields($before, $this->snapshot($Page, $PcLayout, $SpLayout));
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
                $url,
                [],
                [],
                $fieldChanges,
                $fileChanges
            );
        }

        if (!$isNew && [] === $fieldChanges && [] === $fileChanges) {
            return new ContentResult(ContentStatus::Unchanged, $Page->getId(), $url);
        }

        return $this->save($Page, $body, $PcLayout, $SpLayout, $isNew ? null : $previousFileName)
            ->withChanges($fieldChanges, $fileChanges);
    }

    /**
     * ページを永続化し, テンプレートファイルを書き出す.
     *
     * 検証済みのエンティティを受け取る前提のため, 管理画面からは
     * $form->isValid() を通過した後に呼び出す.
     */
    public function save(Page $Page, string $body, ?Layout $PcLayout, ?Layout $SpLayout, ?string $previousFileName): ContentResult
    {
        $isNew = null === $Page->getId();

        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        $templateDir = $this->getTemplateDir($Page);
        $filePath = $templateDir.'/'.$Page->getFileName().'.twig';
        $this->filesystem->dumpFile($filePath, StringUtil::convertLineFeed($body));

        $removedPaths = [];
        // 更新でファイル名を変更した場合, 以前のファイルを削除する
        if (null !== $previousFileName && $Page->getFileName() !== $previousFileName) {
            $oldFilePath = $templateDir.'/'.$previousFileName.'.twig';
            if ($this->filesystem->exists($oldFilePath)) {
                $this->filesystem->remove($oldFilePath);
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
    }

    /**
     * ページとテンプレートファイルを削除する.
     *
     * ユーザーが作成したページ (EDIT_TYPE_USER) のみ削除できる.
     */
    public function remove(Page $Page): ContentResult
    {
        if (Page::EDIT_TYPE_USER !== $Page->getEditType()) {
            throw new \LogicException(sprintf('Page "%s" is not removable.', (string) $Page->getUrl()));
        }

        $id = $Page->getId();
        $url = (string) $Page->getUrl();
        $filePath = $this->getFilePath($Page);

        $removedPaths = [];
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
            $removedPaths[] = $filePath;
        }

        $this->entityManager->remove($Page);
        $this->entityManager->flush();

        return new ContentResult(ContentStatus::Removed, $id, $url, [], $removedPaths);
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
        // 既定ページは URL・ファイル名を変更できない (管理画面と同じ制約)
        if ($this->isUserDataPage($Page)) {
            $fields[] = 'file_name';
        }
        foreach ($fields as $field) {
            if (array_key_exists($field, $payload)) {
                $data[$field] = (string) $payload[$field];
            }
        }

        if ($isNew) {
            $data['url'] = (string) $payload['url'];
            $data['file_name'] ??= (string) $payload['url'];
        }

        foreach (['pc_layout' => 'PcLayout', 'sp_layout' => 'SpLayout'] as $key => $field) {
            if (array_key_exists($key, $payload)) {
                $data[$field] = null === $payload[$key] ? '' : (string) $payload[$key];
            }
        }

        return $data;
    }

    /**
     * @return array<string, string>
     */
    private function snapshot(Page $Page, ?Layout $PcLayout = null, ?Layout $SpLayout = null): array
    {
        $snapshot = [
            'name' => (string) $Page->getName(),
            'url' => (string) $Page->getUrl(),
            'file_name' => (string) $Page->getFileName(),
            'author' => (string) $Page->getAuthor(),
            'description' => (string) $Page->getDescription(),
            'keyword' => (string) $Page->getKeyword(),
            'meta_robots' => (string) $Page->getMetaRobots(),
            'meta_tags' => (string) $Page->getMetaTags(),
        ];

        if (null === $PcLayout && null === $SpLayout) {
            // 変更前のスナップショット: 紐づいているレイアウトから取得する
            $snapshot['PcLayout'] = '';
            $snapshot['SpLayout'] = '';
            foreach ($Page->getLayouts() as $Layout) {
                $field = DeviceType::DEVICE_TYPE_PC === $Layout->getDeviceType()->getId() ? 'PcLayout' : 'SpLayout';
                $snapshot[$field] = (string) $Layout->getId();
            }

            return $snapshot;
        }

        $snapshot['PcLayout'] = null === $PcLayout ? '' : (string) $PcLayout->getId();
        $snapshot['SpLayout'] = null === $SpLayout ? '' : (string) $SpLayout->getId();

        return $snapshot;
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
