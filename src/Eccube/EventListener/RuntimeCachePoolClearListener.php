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

use Eccube\Util\CacheUtil;
use Eccube\Util\RuntimeCachePoolClearer;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * cache:clear が実行時 cache pool を削除できなかったことを通知する.
 *
 * 権限を分離した構成では, cache:clear をレーン S の所有者 (CLI ユーザー) で実行すると
 * ビルド生成物は削除できる一方, レーン W のランタイムディレクトリにある cache pool は削除できない.
 * RuntimeCachePoolClearer はここで例外を投げると cache:clear 全体が失敗するため中断しないが,
 * そのままでは "Cache was successfully cleared" と表示され, 消えていないことが利用者に伝わらない.
 *
 * eccube:cache:build や eccube:page:apply とは異なり, 終了コードは変更しない.
 * cache:clear は composer.json の auto-scripts (cache:clear --no-warmup) から実行され,
 * 非ゼロを返すと composer install がスクリプト失敗として中断する (実測で [KO] になる).
 * cache:clear 自体が担うビルド生成物の削除は成功しているため, 残りの操作は警告で案内する.
 */
class RuntimeCachePoolClearListener implements EventSubscriberInterface
{
    public function __construct(private readonly RuntimeCachePoolClearer $runtimeCachePoolClearer)
    {
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

        $unclearedPath = $this->runtimeCachePoolClearer->getUnclearedPath();
        if (null === $unclearedPath) {
            return;
        }

        (new SymfonyStyle($event->getInput(), $event->getOutput()))->warning([
            sprintf('%s を削除できないため, 実行時キャッシュに古い内容が残ります.', $unclearedPath),
            sprintf(
                'Web サーバーのユーザーで bin/console cache:pool:clear --all または bin/console cache:pool:clear %s を実行するか, 管理画面のキャッシュ管理から削除してください.',
                CacheUtil::DOCTRINE_APP_CACHE_KEY
            ),
        ]);
    }
}
