<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Util;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * キャッシュ関連のユーティリティクラス.
 */
class CacheUtil
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * CacheUtil constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function clearCache($env = null)
    {
        $console = new Application($this->kernel);
        $console->setAutoExit(false);

        $command = [
            'command' => 'cache:clear',
            '--no-warmup' => true,
            '--no-ansi' => true,
        ];

        if ($env !== null) {
            $command['--env'] = $env;
        }

        $input = new ArrayInput($command);

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        return $output->fetch();
    }

    /**
     * キャッシュを削除する.
     *
     * doctrine, profiler, twig によって生成されたキャッシュディレクトリを削除する.
     * キャッシュは $app['config']['root_dir'].'/app/cache' に生成されます.
     *
     * @param Application $app
     * @param boolean $isAll .gitkeep を残してすべてのファイル・ディレクトリを削除する場合 true, 各ディレクトリのみを削除する場合 false
     * @param boolean $isTwig Twigキャッシュファイルのみ削除する場合 true
     *
     * @return boolean 削除に成功した場合 true
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
}
