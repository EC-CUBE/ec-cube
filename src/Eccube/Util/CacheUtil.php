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

namespace Eccube\Util;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheClearer\Psr6CacheClearer;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * キャッシュ関連のユーティリティクラス.
 */
class CacheUtil implements EventSubscriberInterface
{
    public const DOCTRINE_APP_CACHE_KEY = 'doctrine.app_cache_pool';

    private $clearCacheAfterResponse = false;

    /**
     * @var KernelInterface
     */
    protected $kernel;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * CacheUtil constructor.
     *
     * @param KernelInterface $kernel
     * @param ContainerInterface $container
     */
    public function __construct(KernelInterface $kernel, ContainerInterface $container)
    {
        $this->kernel = $kernel;
        $this->container = $container;
    }

    /**
     * @param string $env
     */
    public function clearCache($env = null)
    {
        $this->clearCacheAfterResponse = $env;
    }

    public function forceClearCache(TerminateEvent $event)
    {
        if ($this->clearCacheAfterResponse === false) {
            return;
        }

        // Symfony 7 の cache:clear --no-warmup はカーネルをリブートするため,
        // プラグインインストール直後などオートローダーが古い状態で Fatal Error が発生する.
        // cache:clear コマンドの代わりにキャッシュディレクトリを直接削除する.
        // 次のリクエストでコンテナが自動的に再コンパイルされる.
        //
        // PHP ビルトインサーバ (シングルスレッド) では kernel.terminate 中は
        // レスポンスがクライアントに送信されない. rename は Filesystem::remove より
        // 大幅に高速 (ほぼ瞬時) なため, レスポンス送信の遅延を最小化する.
        $cacheDir = $this->kernel->getCacheDir();
        $tmpDir = $cacheDir.'_old_'.uniqid();
        if (!@rename($cacheDir, $tmpDir)) {
            // rename 失敗時はフォールバックで直接削除
            $fs = new Filesystem();
            $fs->remove($cacheDir);
        }
        // rename 成功時は古いディレクトリの削除をスキップ.
        // PHP ビルトインサーバでのレスポンス遅延を 1-2 秒削減する.
        // 古いキャッシュは次回の cache:clear で自動的にクリーンアップされる.

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (function_exists('apc_clear_cache')) {
            apc_clear_cache('user');
            apc_clear_cache();
        }

        if (function_exists('wincache_ucache_clear')) {
            wincache_ucache_clear();
        }

        return null;
    }

    /**
     * Doctrineのキャッシュを削除します.
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function clearDoctrineCache()
    {
        /** @var Psr6CacheClearer $poolClearer */
        $poolClearer = $this->container->get('cache.global_clearer');
        if (!$poolClearer->hasPool(self::DOCTRINE_APP_CACHE_KEY)) {
            return null;
        }

        $console = new Application($this->kernel);
        $console->setAutoExit(false);

        $command = [
            'command' => 'cache:pool:clear',
            'pools' => [self::DOCTRINE_APP_CACHE_KEY],
            '--no-ansi' => true,
        ];

        $input = new ArrayInput($command);

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        return $output->fetch();
    }

    /**
     * Twigキャッシュを削除します.
     */
    public function clearTwigCache()
    {
        $cacheDir = $this->kernel->getCacheDir().'/twig';
        $fs = new Filesystem();
        $fs->remove($cacheDir);
    }

    /**
     * キャッシュを削除する.
     *
     * doctrine, profiler, twig によって生成されたキャッシュディレクトリを削除する.
     * キャッシュは $app['config']['root_dir'].'/app/cache' に生成されます.
     *
     * @param Application $app
     * @param bool $isAll .gitkeep を残してすべてのファイル・ディレクトリを削除する場合 true, 各ディレクトリのみを削除する場合 false
     * @param bool $isTwig Twigキャッシュファイルのみ削除する場合 true
     *
     * @return bool 削除に成功した場合 true
     *
     * @deprecated CacheUtil::clearCacheを利用すること
     */
    public static function clear($app, $isAll, $isTwig = false)
    {
        $cacheDir = $app['config']['root_dir'].'/app/cache';

        $filesystem = new Filesystem();
        $finder = Finder::create()->notName('.gitkeep')->files();
        if ($isAll) {
            $finder = $finder->in($cacheDir);
            $filesystem->remove($finder);
        } elseif ($isTwig) {
            if (is_dir($cacheDir.'/twig')) {
                $finder = $finder->in($cacheDir.'/twig');
                $filesystem->remove($finder);
            }
        } else {
            if (is_dir($cacheDir.'/doctrine')) {
                $finder = $finder->in($cacheDir.'/doctrine');
                $filesystem->remove($finder);
            }
            if (is_dir($cacheDir.'/profiler')) {
                $finder = $finder->in($cacheDir.'/profiler');
                $filesystem->remove($finder);
            }
            if (is_dir($cacheDir.'/twig')) {
                $finder = $finder->in($cacheDir.'/twig');
                $filesystem->remove($finder);
            }
            if (is_dir($cacheDir.'/translator')) {
                $finder = $finder->in($cacheDir.'/translator');
                $filesystem->remove($finder);
            }
        }

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (function_exists('apc_clear_cache')) {
            apc_clear_cache('user');
            apc_clear_cache();
        }

        if (function_exists('wincache_ucache_clear')) {
            wincache_ucache_clear();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::TERMINATE => 'forceClearCache'];
    }
}
