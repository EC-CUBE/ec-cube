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

namespace Eccube\Command;

use Eccube\Common\EccubeConfig;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Service\Attribute\Required;

trait PluginCommandTrait
{
    /**
     * 本処理は完了したが, 手動での操作が必要な状態を表す終了コード.
     *
     * 1 (異常終了) と区別するために独自の値を使う. 2 は Symfony の Command::INVALID が使用済みのため避ける.
     */
    public const EXIT_MANUAL_ACTION_REQUIRED = 3;

    protected PluginService $pluginService;

    protected PluginRepository $pluginRepository;

    protected EccubeConfig $eccubeConfig;

    #[Required]
    public function setPluginService(PluginService $pluginService): void
    {
        $this->pluginService = $pluginService;
    }

    #[Required]
    public function setPluginRepository(PluginRepository $pluginRepository): void
    {
        $this->pluginRepository = $pluginRepository;
    }

    #[Required]
    public function setEccubeConfig(EccubeConfig $eccubeConfig): void
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * キャッシュを削除する.
     *
     * --no-warmup を外したり eccube:cache:build を続けて実行したりして, ここでコンパイル済み
     * コンテナまで作り直すことはできない. コンテナを作り直す子プロセスは, 新しいコンテナの
     * ディレクトリだけを残して build ディレクトリを差し替えるため, 古いコンテナを読み込んだまま
     * 動いているこのプロセスのディレクトリが消える. その後 console.terminate でサービスを
     * 遅延読み込みする際に require が失敗し, プラグインの操作自体は完了しているのに
     * コマンドが異常終了する.
     *
     * 権限を分離した構成では, Web サーバーが build ディレクトリを書けないため
     * eccube:cache:build を別途実行する必要がある.
     *
     * 失敗しても本処理は完了しているため例外にはせず, 手動で実行する手順を案内して false を返す.
     *
     * @return bool 削除できた場合 true
     */
    protected function clearCache(SymfonyStyle $io): bool
    {
        $command = ['bin/console', 'cache:clear', '--no-warmup'];
        try {
            $io->text(sprintf('<info>Run %s</info>...', implode(' ', $command)));
            // cwd を明示しない場合, プロジェクトルート以外から実行すると bin/console を解決できない
            $process = new Process($command, (string) $this->eccubeConfig->get('kernel.project_dir'));
            // Process の既定タイムアウトは 60 秒. 超過すると子プロセスが kill され,
            // 削除が中途半端な状態のまま「削除できませんでした」と案内することになる
            $process->setTimeout(null);
            $process->mustRun();
            $io->text($process->getOutput());

            return true;
        } catch (ProcessExceptionInterface $e) {
            $io->error($e->getMessage());
            $io->warning(sprintf(
                'キャッシュを削除できませんでした. 書き込み権限のあるユーザーで %s を実行してください.',
                implode(' ', $command)
            ));

            return false;
        }
    }
}
