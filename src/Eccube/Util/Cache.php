<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Util;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Eccube\Application;

/**
 * キャッシュ関連のユーティリティクラス.
 */
class Cache
{

    /**
     * キャッシュを削除する.
     *
     * doctrine, profiler, twig によって生成されたキャッシュディレクトリを削除する.
     * キャッシュは $app['config']['root_dir'].'/app/cache' に生成されます.
     *
     * @param Application $app
     * @param boolean $isAll .gitkeep を残してすべてのファイル・ディレクトリを削除する場合 true, 各ディレクトリのみを削除する場合 false
     * @param boolean $isTwig Twigキャッシュファイルのみ削除する場合 true
     * @return boolean 削除に成功した場合 true
     */
    public static function clear($app, $isAll, $isTwig = false)
    {
        $cacheDir = $app['config']['root_dir'].'/app/cache';

        $filesystem = new Filesystem();
        if ($isAll) {
            $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');
            $filesystem->remove($finder);
        } elseif ($isTwig) {
            if (is_dir($cacheDir.'/twig')) {
                $finder = Finder::create()->in($cacheDir.'/twig');
                $filesystem->remove($finder);
            }
        } else {
            if (is_dir($cacheDir.'/doctrine')) {
                $finder = Finder::create()->in($cacheDir.'/doctrine');
                $filesystem->remove($finder);
            }
            if (is_dir($cacheDir.'/profiler')) {
                $finder = Finder::create()->in($cacheDir.'/profiler');
                $filesystem->remove($finder);
            }
            if (is_dir($cacheDir.'/twig')) {
                $finder = Finder::create()->in($cacheDir.'/twig');
                $filesystem->remove($finder);
            }
            if (is_dir($cacheDir.'/translator')) {
                $finder = Finder::create()->in($cacheDir.'/translator');
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
