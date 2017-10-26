<?php

namespace Eccube\Tests\Doctrine\Common\CsvDataFixtures;

use Eccube\Doctrine\Common\CsvDataFixtures\CsvFixture;
use Eccube\Tests\EccubeTestCase;

class CsvFixtureTest extends EccubeTestCase
{

    protected $fixture;
    protected $file;

    public function setUp() {
        parent::setUp();
        $Jobs = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Job')->findAll();
        foreach ($Jobs as $Job) {
            $this->app['orm.em']->remove($Job);
        }
        $this->app['orm.em']->flush();

        $this->file = new \SplFileObject(
            __DIR__.'/../../../../../Fixtures/import_csv/mtb_job.csv'
        );
        $this->fixture = new CsvFixture($this->file);
    }

    public function testNewInstance()
    {
        $this->assertInstanceOf('Eccube\Doctrine\Common\CsvDataFixtures\CsvFixture', $this->fixture);
    }

    public function testGetSql()
    {
        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
        $headers = $this->file->current();

        $this->expected = 'INSERT INTO mtb_job (id, name, rank) VALUES (?, ?, ?)';
        $this->actual = $this->fixture->getSql('mtb_job', $headers);
        $this->verify();
    }

    public function testLoad()
    {
        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
        $this->file->rewind();
        $headers = $this->file->current();
        $this->file->next();

        // ファイルのデータ行を取得しておく
        $rows = array();
        while(!$this->file->eof()) {
            $rows[] = $this->file->current();
            $this->file->next();
        }

        $this->file->rewind();
        $this->fixture->load($this->app['orm.em']);
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
