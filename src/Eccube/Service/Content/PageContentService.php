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
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

/**
 * ページ (dtb_page と app/template 配下の twig) を対で扱う.
 *
 * 管理画面 (PageController) と CLI (eccube:page:*) の双方から使用する.
 * 入力値の検証は管理画面と同じ MainEditType を submit して行うため,
 * ルーティング名 / ファイル名の重複チェックや TwigLint も同じものが効く.
 */
class PageContentService
{
    use TemplateBodyTrait;
    use TemplateRemovalTrait;

    /**
     * twig の名前空間を表す接頭辞.
     */
    private const TWIG_NAMESPACE_PREFIX = '@';

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

    /**
     * ページのファイル名が, プラグインの名前空間 (@PluginCode/...) を指すか.
     *
     * プラグインが用意したテンプレートは twig の名前空間で解決されるため,
     * プラグインが登録する既定ページの dtb_page.file_name には "@PluginCode/xxx" の形で入る.
     *
     * ユーザーが作成したページは "@" で始まっても対象外とする. フロント (UserDataController) は
     * "@user_data/" を前置して描画するため, "@Foo/x" は app/template/user_data/@Foo/x.twig を指す.
     */
    private function isNamespacedFileName(Page $Page, string $fileName): bool
    {
        return !$this->isUserDataPage($Page) && str_starts_with($fileName, self::TWIG_NAMESPACE_PREFIX);
    }

    public function getTemplateDir(Page $Page): string
    {
        return $this->resolveTemplateDir($Page, (string) $Page->getFileName());
    }

    /**
     * ファイル名に対応する配置先を返す.
     *
     * 名前空間の有無で配置先が変わるため, ファイル名の変更を跨ぐ処理
     * (save() の旧ファイル削除) では「そのファイル名が置かれていたディレクトリ」を
     * 引く必要がある. Page だけを見ると新しい配置先で旧ファイルを探してしまう.
     */
    private function resolveTemplateDir(Page $Page, string $fileName): string
    {
        // EccubeExtension::configureTwigPaths() が @PluginCode として登録するのは
        // app/template/plugin/[code] と app/Plugin/[code]/Resource/template の 2 つ.
        // 編集内容はプラグイン同梱側ではなく, 上書き用の app/template/plugin へ書く.
        if ($this->isNamespacedFileName($Page, $fileName)) {
            return $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin';
        }

        return $this->isUserDataPage($Page)
            ? (string) $this->eccubeConfig->get('eccube_theme_user_data_dir')
            : (string) $this->eccubeConfig->get('eccube_theme_front_dir');
    }

    /**
     * 名前空間付きのファイル名から, twig の名前空間 ("@PluginCode/xxx" なら PluginCode) を返す.
     * 名前空間を指さない場合は null.
     */
    private function resolvePluginNamespace(Page $Page, string $fileName): ?string
    {
        if (!$this->isNamespacedFileName($Page, $fileName)) {
            return null;
        }

        $namespace = strstr(substr($fileName, strlen(self::TWIG_NAMESPACE_PREFIX)), '/', true);

        return false === $namespace || '' === $namespace ? null : $namespace;
    }

    /**
     * 名前空間付きのページの上書き用ディレクトリが, twig の名前空間として登録されていないか.
     *
     * EccubeExtension::configureTwigPaths() が app/template/plugin/[code] を @PluginCode へ登録するのは,
     * コンテナのコンパイル時にそのディレクトリが有った場合だけ. 後から作ったディレクトリは,
     * コンテナを作り直すまで登録されず, フロントはプラグイン同梱のテンプレートを表示し続ける.
     *
     * ディレクトリの有無ではなく登録状況を見るため, 案内を見落としても次の保存で再び検出できる.
     *
     * 有効なプラグインの名前空間だけを対象にする. configureTwigPaths() は無効なプラグインの名前空間を登録しないため,
     * コンテナを作り直しても登録されず, 対象にすると保存のたびに再構築を求め続ける.
     */
    private function requiresContainerRebuild(Page $Page): bool
    {
        $namespace = $this->resolvePluginNamespace($Page, (string) $Page->getFileName());
        if (null === $namespace || !$this->isEnabledPlugin($namespace)) {
            return false;
        }

        $dir = $this->eccubeConfig->get('eccube_theme_app_dir').'/plugin/'.$namespace;
        if (!is_dir($dir)) {
            // 上書きしていなければ, 同梱のテンプレートを表示するのが正しい
            return false;
        }

        return !$this->isTwigPathRegistered($namespace, $dir);
    }

    private function isEnabledPlugin(string $code): bool
    {
        return in_array($code, (array) $this->eccubeConfig->get('eccube.plugins.enabled'), true);
    }

    /**
     * twig のローダに, 名前空間のパスとして $dir が登録されているか.
     *
     * スマートフォン向けの表示 (MobileTemplatePathListener) ではローダが ChainLoader で包まれるため, 中身を辿る.
     */
    private function isTwigPathRegistered(string $namespace, string $dir): bool
    {
        $expected = self::normalizeDir($dir);
        $loaders = [$this->twig->getLoader()];
        while ([] !== $loaders) {
            $loader = array_shift($loaders);
            if ($loader instanceof ChainLoader) {
                array_push($loaders, ...$loader->getLoaders());
                continue;
            }
            if (!$loader instanceof FilesystemLoader || !in_array($namespace, $loader->getNamespaces(), true)) {
                continue;
            }
            foreach ($loader->getPaths($namespace) as $path) {
                if (self::normalizeDir($path) === $expected) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function normalizeDir(string $dir): string
    {
        $real = realpath($dir);

        return false === $real ? rtrim($dir, '/\\') : $real;
    }

    public function getFilePath(Page $Page): string
    {
        return $this->buildFilePath($Page, $this->getTemplateDir($Page), (string) $Page->getFileName());
    }

    /**
     * テンプレートの書き込み先を組み立てる.
     *
     * 先頭の "@" は twig の名前空間の記法でディレクトリ名には含まれない
     * (@PluginCode は app/template/plugin/PluginCode を指す) ため取り除く.
     * 取り除かないと登録済みのパスと一致せず, 書き出しても画面へ反映されない.
     * ユーザーが作成したページの "@" はディレクトリ名の一部なので残す (isNamespacedFileName() 参照).
     */
    private function buildFilePath(Page $Page, string $templateDir, string $fileName): string
    {
        if ($this->isNamespacedFileName($Page, $fileName)) {
            $fileName = substr($fileName, strlen(self::TWIG_NAMESPACE_PREFIX));
        }

        return $templateDir.'/'.$fileName.'.twig';
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
        return (string) $this->findTemplate($Page);
    }

    /**
     * テンプレートの内容を取得する. どこからも解決できない場合は null を返す.
     *
     * 「解決できて中身が空」と「存在しない」を区別するため, readTemplate() と分けている.
     */
    public function findTemplate(Page $Page): ?string
    {
        $filePath = $this->getFilePath($Page);
        if (is_file($filePath)) {
            return (string) file_get_contents($filePath);
        }

        // ユーザーが作成したページは, twig の @user_data 名前空間が書き込み先と同じ
        // ディレクトリを指すため, ローダへ問い合わせても上の is_file() 以上には見つからない.
        // 見つからなかった問い合わせは FilesystemLoader::$errorCache に残り, 同じプロセスで
        // 書き出した直後のテンプレートを読めなくするので, 問い合わせ自体を行わない.
        if ($this->isUserDataPage($Page)) {
            return null;
        }

        try {
            return $this->twig->getLoader()
                ->getSourceContext($Page->getFileName().'.twig')
                ->getCode();
        } catch (LoaderError) {
            return null;
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
        // 新規登録時は, 配置先に既にあるテンプレートを本文の初期値にする
        $beforeBody = self::normalizeTemplateBody($isNew
            ? ($this->readExistingTemplate($this->getTemplateDir($Page), (string) ($payload['file_name'] ?? $payload['route'])) ?? '')
            : $this->readTemplate($Page));

        $form = $this->formFactory->create(MainEditType::class, $Page, ['csrf_protection' => false]);
        $form->submit($this->toFormData($payload, $Page, $isNew, $beforeBody), false);

        if (!$form->isValid()) {
            throw ContentValidationException::fromForm($form);
        }

        $body = self::normalizeTemplateBody((string) $form->get('tpl_data')->getData());
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
            // 保存を省いても, 再構築が済んでいなければ伝える. 同じ内容での再実行で案内を受け取れるようにする
            return new ContentResult(
                ContentStatus::Unchanged,
                $Page->getId(),
                $route,
                containerRebuildRequired: $this->requiresContainerRebuild($Page)
            );
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
     * 本文が現在の内容と同じ場合はファイルを書き出さない (ContentResult::$writtenPaths も空になる).
     *
     * 名前空間付きのページで上書き用ディレクトリ (app/template/plugin/[code]) が twig にまだ登録されていない場合は,
     * ContentResult::$containerRebuildRequired を true にする (requiresContainerRebuild() 参照).
     * 呼び出し側でコンテナを作り直さないと, 上書きしたテンプレートがフロントへ反映されない.
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
            $filePath = $this->buildFilePath($Page, $templateDir, (string) $Page->getFileName());

            // 本文が現在の内容と同じなら書き出さない (shouldWriteTemplate() 参照).
            // 比較相手の findTemplate() はファイルが無ければコアのテンプレートへフォールバックするので,
            // 上書きしていないページのメタ情報だけを更新しても app/template にファイルが増えない.
            $writtenPaths = [];
            if (self::shouldWriteTemplate($this->findTemplate($Page), $body)) {
                try {
                    $this->filesystem->dumpFile($filePath, StringUtil::convertLineFeed($body));
                } catch (IOException $e) {
                    throw ContentWriteException::forWrite($filePath, $e);
                }
                $writtenPaths[] = $filePath;
            }

            $removedPaths = [];
            // 更新でファイル名を変更した場合, 以前のファイルを削除する
            if (null !== $previousFileName && $Page->getFileName() !== $previousFileName) {
                $oldFilePath = $this->buildFilePath(
                    $Page,
                    $this->resolveTemplateDir($Page, $previousFileName),
                    $previousFileName
                );
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

            // 書き出しの有無に関わらず判定する. 以前の保存で案内を見落とした場合も, 再保存で検出できる
            $containerRebuildRequired = $this->requiresContainerRebuild($Page);

            return new ContentResult(
                $isNew ? ContentStatus::Created : ContentStatus::Updated,
                $Page->getId(),
                (string) $Page->getUrl(),
                $writtenPaths,
                $removedPaths,
                containerRebuildRequired: $containerRebuildRequired
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
