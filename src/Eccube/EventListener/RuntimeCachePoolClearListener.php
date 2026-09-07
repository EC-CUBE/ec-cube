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

namespace Eccube\EventListener;

use Eccube\Service\Permission\PathOwnership;
use Eccube\Service\Permission\WebServerUserResolver;
use Eccube\Util\CacheUtil;
use Eccube\Util\RuntimeCachePoolClearer;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * cache:clear の後始末が必要なことを通知する.
 *
 * 権限を分離した構成では cache:clear が 2 つの後始末を残す. どちらも cache:clear 自体は
 * 成功として終わるため, 案内しないと気づけない.
 *
 * 1. コンパイル済みコンテナの消失 — cache:clear はビルドディレクトリも削除する
 *    (CacheClearCommand.php の $useBuildDir 分岐). --no-warmup を付けると再生成されないため,
 *    次回起動時に Kernel::buildContainer() がコンテナを作り直そうとし, レーン S (var/cache) へ
 *    書き込めない Web サーバーは "Unable to write in the "cache" directory" で 500 になる.
 *    復旧できるのは CLI ユーザーの eccube:cache:build だけなので最優先で案内する.
 * 2. 実行時 cache pool の残存 — レーン W のランタイムディレクトリは CLI ユーザーから削除できない.
 *    RuntimeCachePoolClearer は例外を投げず (投げると cache:clear 全体が失敗する) 結果だけを残す.
 *
 * eccube:cache:build や eccube:page:apply とは異なり, 終了コードは変更しない.
 * cache:clear は composer.json の auto-scripts (cache:clear --no-warmup) から実行され,
 * 非ゼロを返すと composer install がスクリプト失敗として中断する (実測で [KO] になる).
 * auto-scripts は直後に cache:warmup でコンテナを再生成するため, 1 の状態は残らない.
 */
class RuntimeCachePoolClearListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly RuntimeCachePoolClearer $runtimeCachePoolClearer,
        private readonly WebServerUserResolver $webServerUserResolver,
        private readonly string $buildDir,
        private readonly string $cacheDir,
        private readonly string $containerClass,
    ) {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::TERMINATE => ['onConsoleTerminate'],
        ];
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        if ('cache:clear' !== $event->getCommand()?->getName()) {
            return;
        }

        // 本処理自体が失敗している場合は, そちらのエラーを埋もれさせないため何も表示しない.
        if (0 !== $event->getExitCode()) {
            return;
        }

        $io = new SymfonyStyle($event->getInput(), $event->getOutput());

        // コンテナが無い状態は Web サーバーが起動できないため, pool の案内より先に出す.
        if ($this->needsManualRebuild()) {
            $io->warning([
                sprintf('%s にコンパイル済みコンテナがありません.', $this->buildDir),
                'ビルドディレクトリへ書き込めるユーザーで bin/console eccube:cache:build を実行してください.'
                .' 実行するまで Web サーバーはアプリケーションを起動できず, Web サーバーのユーザーで実行する'
                .' bin/console も同じ理由で失敗します.',
            ]);
        }

        $unclearedPath = $this->runtimeCachePoolClearer->getUnclearedPath();
        if (null === $unclearedPath) {
            return;
        }

        $io->warning([
            sprintf('%s を削除できないため, 実行時キャッシュに古い内容が残ります.', $unclearedPath),
            sprintf(
                'Web サーバーのユーザーで bin/console cache:pool:clear --all または bin/console cache:pool:clear %s を実行するか, 管理画面のキャッシュ管理から削除してください.',
                CacheUtil::DOCTRINE_APP_CACHE_KEY
            ),
        ]);
    }

    /**
     * コンパイル済みコンテナが無く, かつ Web サーバーからは作り直せない状態か.
     *
     * 権限を分離していない構成では Web サーバー自身がコンテナを再生成できるため案内は不要.
     * Web サーバーの実行ユーザーを特定できない場合も, 誤った警告を出さないよう黙る
     * (判定材料が要るときは eccube:doctor:permissions が別途案内する).
     */
    private function needsManualRebuild(): bool
    {
        if (is_file(rtrim($this->buildDir, '/').'/'.$this->containerClass.'.php')) {
            return false;
        }

        $webServerUser = $this->webServerUserResolver->resolve();
        if (null === $webServerUser) {
            return false;
        }

        // Kernel::buildContainer() は cache と build の双方へ書き込めることを要求する.
        foreach ([$this->cacheDir, $this->buildDir] as $dir) {
            $ownership = PathOwnership::of($dir);
            // isWritableBy() が見るのは対象自身のビットだけなので, 祖先の到達可否は別に確かめる.
            if ($ownership->unreachableAncestorFor($webServerUser) instanceof PathOwnership) {
                return true;
            }
            if (!$ownership->isWritableBy($webServerUser)) {
                return true;
            }
        }

        return false;
    }
}
