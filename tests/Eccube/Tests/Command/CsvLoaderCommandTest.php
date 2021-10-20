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

namespace Eccube\Tests\Command;

use Eccube\Command\CsvLoaderCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CsvLoaderCommandTest extends AbstractCommandTest
{
    /** @var \SplFileObject */
    protected $file;

    public static function setUpBeforeClass()
    {
        self::markTestIncomplete();
    }

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        if ($this->app['config']['database']['driver'] == 'pdo_sqlite') {
            $this->markTestSkipped('Can not support for sqlite3');
        }

        $this->initCommand(new CsvLoaderCommand());

        $Jobs = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Job')->findAll();
        foreach ($Jobs as $Job) {
            $this->app['orm.em']->remove($Job);
        }
        $this->app['orm.em']->flush();

        $this->file = new \SplFileObject(__DIR__.'/../../../Fixtures/import_csv/mtb_job.csv');
    }

    public function testExecute()
    {
        $commandArg = [
            'command' => 'csv-loader',
            '--file' => $this->file->getRealPath(),
        ];

        $command = $this->app['console']->find($this->command->getName());
        $this->expected = $commandArg['command'];
        $this->actual = $command->getName();
        $this->verify();

        $CommandTester = new CommandTester($command);
        $CommandTester->execute($commandArg);

        $output = $CommandTester->getDisplay();
        $this->assertContains('CSV Loader complete.', $output);

        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
        $this->file->rewind();
        $this->file->next();

        // ファイルのデータ行を取得しておく
        $rows = [];
        while (!$this->file->eof()) {
            $rows[] = $this->file->current();
            $this->file->next();
        }

        $this->file->rewind();
        $Jobs = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Job')->findAll();

        $this->expected = count($rows);
        $this->actual = count($Jobs);
        $this->verify('行数は一致するか？');
        foreach ($Jobs as $key => $Job) {
            $this->expected = $rows[$key][0].', '.$rows[$key][1].', '.$rows[$key][2];
            $this->actual = $Job->getId().', '.$Job->getName().', '.$Job->getRank();
            $this->verify($key.'行目のデータは一致するか？');
        }
    }
}
