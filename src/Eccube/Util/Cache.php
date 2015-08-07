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

class Cache {

    public static function clear($app,$isAll){

        $cacheDir = $app['config']['root_dir'] . '/app/cache';

        $filesystem = new Filesystem();
        if ($isAll) {
            $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');
            $filesystem->remove($finder);
        } else {
            if(is_dir($cacheDir . '/doctrine')){
                $finder = Finder::create()->in($cacheDir . '/doctrine');
                $filesystem->remove($finder);
            }
            if(is_dir($cacheDir . '/profiler')){
                $finder = Finder::create()->in($cacheDir . '/profiler');
                $filesystem->remove($finder);
            }
            if(is_dir($cacheDir . '/twig')){
                $finder = Finder::create()->in($cacheDir . '/twig');
                $filesystem->remove($finder);
            }
        }
        return true;
    }
}
