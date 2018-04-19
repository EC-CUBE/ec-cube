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

namespace Eccube\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;


/**
 * Class DocumentRootCommand
 *
 * @package Eccube\Command
 * @deprecated since 3.0.16, to be removed in 3.1
 */
class DocumentRootCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('documentroot:change')
            ->addArgument('use_webserver', InputArgument::REQUIRED, 'Use web server apache or IIS')
            ->setDescription('DocumentRoot Change')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command DocumentRoot Change,
I will remove html from the URL.

  <info>php %command.full_name% [apache or IIS]</info>

EOF
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $webserver = $input->getArgument('use_webserver');

        if ($webserver != 'apache' && $webserver != 'IIS') {
            $io->error('Please specify apache or IIS as the parameter of use_webserver.');

            return;
        }

        $rootDir = __DIR__.'/../../..';
        $htmlDir = $rootDir.'/html';
        $vendorDir = $rootDir.'/vendor';


        // 対象ファイルの検索
        $finder = Finder::create()->files()->in($htmlDir)->ignoreDotFiles(false)
            ->depth(0)
            ->name('/index.php|index_dev.php|install.php|robots.txt|.htaccess|web.config/');

        // ファイル配置場所の変更
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $f = new File($file->getRealPath());
            $f->move($rootDir);
        }

        // .htaccess / web.config の置き換え
        $filesystem = new Filesystem();
        if ($webserver == 'apache') {
            $filesystem->rename($rootDir.'/.htaccess.sample', $rootDir.'/.htaccess', true);
        } elseif ($webserver == 'IIS') {
            $filesystem->rename($rootDir.'/web.config.sample', $rootDir.'/web.config', true);
        }

        // index.phpの変更
        $content = file_get_contents($rootDir.'/index.php');
        $before = '/require __DIR__.\'\/..\/autoload.php\';/';
        $after = '//require __DIR__.\'/../autoload.php\';';
        $content = preg_replace($before, $after, $content);
        $before = '/\/\/require __DIR__.\'\/autoload.php\';/';
        $after = 'require __DIR__.\'/autoload.php\';';
        $content = preg_replace($before, $after, $content);
        file_put_contents($rootDir.'/index.php', $content);

        // install.phpの変更
        $content = file_get_contents($rootDir.'/install.php');
        $before = '/require __DIR__ . \'\/..\/autoload.php\';/';
        $after = '//require __DIR__ . \'/../autoload.php\';';
        $content = preg_replace($before, $after, $content);
        $before = '/\/\/require __DIR__ . \'\/autoload.php\';/';
        $after = 'require __DIR__ . \'/autoload.php\';';
        $content = preg_replace($before, $after, $content);
        file_put_contents($rootDir.'/install.php', $content);

        // index_dev.phpの変更
        $content = file_get_contents($rootDir.'/index_dev.php');
        $before = '/require_once __DIR__.\'\/..\/autoload.php\';/';
        $after = '//require_once __DIR__.\'/../autoload.php\';';
        $content = preg_replace($before, $after, $content);
        $before = '/\/\/require_once __DIR__.\'\/autoload.php\';/';
        $after = 'require_once __DIR__.\'/autoload.php\';';
        $content = preg_replace($before, $after, $content);
        $before = '/\/..\/app\/cache\/profiler/';
        $after = '/app/cache/profiler';
        $content = preg_replace($before, $after, $content);
        file_put_contents($rootDir.'/index_dev.php', $content);

        // autoload.phpの変更
        $content = file_get_contents($rootDir.'/autoload.php');
        $before = '/define\("RELATIVE_PUBLIC_DIR_PATH", \'\'\);/';
        $after = '//define("RELATIVE_PUBLIC_DIR_PATH", \'\');';
        $content = preg_replace($before, $after, $content);
        $before = '/\/\/define\("RELATIVE_PUBLIC_DIR_PATH", \'\/html\'\);/';
        $after = 'define("RELATIVE_PUBLIC_DIR_PATH", \'/html\');';
        $content = preg_replace($before, $after, $content);
        file_put_contents($rootDir.'/autoload.php', $content);

        // vendorディレクトリ直下に.htaccessの追加
        $content = 'order allow,deny'.PHP_EOL.'deny from all';
        file_put_contents($vendorDir.'/.htaccess', $content);
        $filesystem = new Filesystem();
        $filesystem->chmod($vendorDir.'/.htaccess', 0644);

        $io->success('DocumentRoot change complete.');
    }

}