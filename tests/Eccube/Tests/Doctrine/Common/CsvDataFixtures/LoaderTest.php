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

namespace Eccube\Tests\Doctrine\Common\CsvDataFixtures;

use Eccube\Doctrine\Common\CsvDataFixtures\CsvFixture;
use Eccube\Doctrine\Common\CsvDataFixtures\Loader;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class LoaderTest extends EccubeTestCase
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->loader = new Loader();
        $this->dir = __DIR__.'/../../../../../Fixtures/import_csv';
    }

    public function testAddFixture()
    {
        $this->loader->addFixture(new CsvFixture(new \SplFileObject($this->dir.'/mtb_job.csv')));
        $this->loader->addFixture(new CsvFixture(new \SplFileObject($this->dir.'/mtb_pref.csv')));

        $fixtures = $this->loader->getFixtures();

        $this->assertInstanceOf(CsvFixture::class, $fixtures[0]);

        $this->expected = 2;
        $this->actual = count($fixtures);
        $this->verify();
    }

    public function testLoadFromIterator()
    {
        $finder = Finder::create()
            ->in($this->dir)
            ->name('*.csv')
            ->files();
        $fixtures = $this->loader->loadFromIterator($finder->getIterator());

        $this->assertTrue(is_array($fixtures));
        $this->assertInstanceOf(CsvFixture::class, $fixtures[0]);

        $this->expected = iterator_count($finder->getIterator());
        $this->actual = count($fixtures);
        $this->verify();
    }

    public function testLoadFromDirectory()
    {
        $finder = Finder::create()
            ->in($this->dir)
            ->name('*.csv')
            ->files();

        $fixtures = $this->loader->loadFromDirectory($this->dir);

        $this->assertTrue(is_array($fixtures));
        $this->assertInstanceOf(CsvFixture::class, $fixtures[0]);

        $this->expected = iterator_count($finder->getIterator());
        $this->actual = count($fixtures);
        $this->verify();

        $definition = Yaml::parse(file_get_contents($this->dir.'/definition.yml'));
        foreach ($definition as $key => $file_name) {
            $this->assertEquals($file_name, $fixtures[$key]->getFile()->getFilename(), 'definition.yml の順にソートされているか');
        }
    }
}
