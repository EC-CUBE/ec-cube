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

namespace Eccube\Tests\Doctrine\Common\CsvDataFixtures\Executor;

use Eccube\Doctrine\Common\CsvDataFixtures\CsvFixture;
use Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor;
use Eccube\Repository\Master\JobRepository;
use Eccube\Tests\EccubeTestCase;

class DbalExecutorTest extends EccubeTestCase
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * @var CsvFixture[]
     */
    protected $fixtures;

    /**
     * @var JobRepository
     */
    protected $jobRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->jobRepository = $this->entityManager->getRepository(\Eccube\Entity\Master\Job::class);

        $Jobs = $this->jobRepository->findAll();
        foreach ($Jobs as $Job) {
            $this->entityManager->remove($Job);
        }
        $this->entityManager->flush();

        $this->file = new \SplFileObject(
            __DIR__.'/../../../../../../Fixtures/import_csv/mtb_job.csv'
        );
        $this->fixtures[] = new CsvFixture($this->file);
    }

    public function testExecute()
    {
        $Executor = new DbalExecutor($this->entityManager);
        $Executor->execute($this->fixtures);

        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
        $this->file->rewind();
        $headers = $this->file->current();
        $this->file->next();

        // ファイルのデータ行を取得しておく
        $rows = [];
        while (!$this->file->eof()) {
            $rows[] = $this->file->current();
            $this->file->next();
        }

        $this->file->rewind();
        $Jobs = $this->jobRepository->findAll();

        $this->expected = count($rows);
        $this->actual = count($Jobs);
        $this->verify('行数は一致するか？');
        foreach ($Jobs as $key => $Job) {
            $this->expected = $rows[$key][0].', '.$rows[$key][1].', '.$rows[$key][2];
            $this->actual = $Job->getId().', '.$Job->getName().', '.$Job->getSortNo();
            $this->verify($key.'行目のデータは一致するか？');
        }
    }
}
