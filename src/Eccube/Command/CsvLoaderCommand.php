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

namespace Eccube\Command;

use Eccube\Doctrine\Common\CsvDataFixtures\CsvFixture;
use Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor;
use Eccube\Doctrine\Common\CsvDataFixtures\Loader;
use Symfony\Component\Console\Command\Command;
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
            $output->writeln('<error>CSV File Name or Directory Name not set.</error>');

            return;
        }

        $em = $app['orm.em'];

        if ($file) {
            // ファイル名が指定された場合
            if (file_exists($file)) {
                $file = new \SplFileObject($file);

                if ($file->isFile()) {
                    $output->writeln(sprintf('<comment>CSV Load %s</comment>', $file->getFilename()));

                    $loader = new Loader();
                    $CsvFixture = new CsvFixture($file->openFile());
                    $loader->addFixture($CsvFixture);

                    $Executor = new DbalExecutor($em);
                    $fixtures = $loader->getFixtures();
                    $Executor->execute($fixtures);

                    $output->writeln('<info>CSV Loader complete.</info>');
                } else {
                    $output->writeln('<error>CSV File Name not set.</error>');
                }
            } else {
                $output->writeln('<error>CSV File Name not set.</error>');
            }
        }

        if ($directory) {
            // ディレクトリ名が指定された場合
            if (is_dir($directory)) {
                $output->writeln(sprintf('<comment>CSV Load %s</comment>', $directory));
                $loader = new Loader();
                $loader->loadFromDirectory($directory);
                $Executor = new DbalExecutor($em);
                $fixtures = $loader->getFixtures();
                $Executor->execute($fixtures);

                $output->writeln('<info>CSV Loader complete.</info>');
            } else {
                $output->writeln('<error>CSV Directory Name not set.</error>');
            }
        }
    }
}
