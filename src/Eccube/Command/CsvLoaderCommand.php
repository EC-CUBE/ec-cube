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

use Eccube\Doctrine\Common\CsvDataFixtures\CsvFixture;
use Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor;
use Eccube\Doctrine\Common\CsvDataFixtures\Loader;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CsvLoaderCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('csv-loader')
            ->addUsage('--file=csvfilename OR -f csvfilename OR --direcotry=csvdirectoryname OR -d csvdirectoryname')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Csv File Name.')
            ->addOption('directory', 'd', InputOption::VALUE_REQUIRED, 'Csv Directory Name.')
            ->setDescription('CSV Loader')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command CSV loader,
Create the CSV file name with the table name.
Ex) mtb_pref -> mtb_pref.csv

  <info>php %command.full_name%</info>

The command specify the CSV file name.

  <info>php %command.full_name% --file=[/Absolute Path/CSV File Name]</info>
  OR
  <info>php %command.full_name% -f [/Absolute Path/CSV File Name]</info>

The command specify the directory name containing the CSV file.

  <info>php %command.full_name% --directory=[/Absolute Path/CSV Directory Name]</info>
  OR
  <info>php %command.full_name% -d [/Absolute Path/CSV Directory Name]</info>

EOF
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Eccube\Application $app */
        $app = $this->getSilexApplication();

        $file = $input->getOption('file');
        $directory = $input->getOption('directory');

        if (!$file && !$directory) {
            $output->writeln("<error>CSV File Name or Directory Name not set.</error>");

            return;
        }

        $em = $app['orm.em'];

        if ($file) {
            // ファイル名が指定された場合
            if (file_exists($file)) {
                $file = new \SplFileObject($file);

                if ($file->isFile()) {
                    $output->writeln(sprintf("<comment>CSV Load %s</comment>", $file->getFilename()));

                    $loader = new Loader();
                    $CsvFixture = new CsvFixture($file->openFile());
                    $loader->addFixture($CsvFixture);

                    $Executor = new DbalExecutor($em);
                    $fixtures = $loader->getFixtures();
                    $Executor->execute($fixtures);

                    $output->writeln("<info>CSV Loader complete.</info>");
                } else {
                    $output->writeln("<error>CSV File Name not set.</error>");
                }
            } else {
                $output->writeln("<error>CSV File Name not set.</error>");
            }
        }

        if ($directory) {
            // ディレクトリ名が指定された場合
            if (is_dir($directory)) {

                $output->writeln(sprintf("<comment>CSV Load %s</comment>", $directory));
                $loader = new Loader();
                $loader->loadFromDirectory($directory);
                $Executor = new DbalExecutor($em);
                $fixtures = $loader->getFixtures();
                $Executor->execute($fixtures);

                $output->writeln("<info>CSV Loader complete.</info>");
            } else {
                $output->writeln("<error>CSV Directory Name not set.</error>");
            }
        }

    }
}
