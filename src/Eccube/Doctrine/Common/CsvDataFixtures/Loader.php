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

namespace Eccube\Doctrine\Common\CsvDataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * CSVファイルのローダー.
 *
 * @see https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/Loader.php
 */
class Loader
{
    /**
     * @var CsvFixture[]
     */
    protected $fixtures;

    /**
     * Load fixtures from directory.
     *
     * 同一階層に, Fixture のロード順を定義した definition.yml が必要.
     *
     * @param string $dir
     *
     * @return array fixtures.
     */
    public function loadFromDirectory($dir)
    {
        if (!dir($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist', $dir));
        }

        // import順序の定義ファイルを取得.
        $file = $dir.'/definition.yml';
        if (!file_exists($file)) {
            // 定義ファイルが存在しなければ取得した順序で処理
            $finder = Finder::create()
                ->in($dir)
                ->name('*.csv');
        }
        $definition = Yaml::parse(file_get_contents($file));
        $definition = array_flip($definition);

        $finder = Finder::create()
            ->in($dir)
            ->name('*.csv')
            ->sort(
                // 定義ファイルに記載の順にソート.
                function (\SplFileInfo $a, \SplFileInfo $b) use ($definition) {
                    if (!isset($definition[$a->getFilename()])) {
                        throw new \Exception(sprintf('"%s" is undefined in definition.yml', $a->getFilename()));
                    }
                    if (!isset($definition[$b->getFilename()])) {
                        throw new \Exception(sprintf('"%s" is undefined in definition.yml', $b->getFilename()));
                    }

                    $a_sortNo = $definition[$a->getFilename()];
                    $b_sortNo = $definition[$b->getFilename()];

                    if ($a_sortNo < $b_sortNo) {
                        return -1;
                    } elseif ($a_sortNo > $b_sortNo) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            )
            ->files();

        return $this->loadFromIterator($finder->getIterator());
    }

    /**
     * Load fixtures from Iterator.
     *
     * @param \Iterator $Iterator Iterator of \SplFileInfo
     *
     * @return array fixtures.
     */
    public function loadFromIterator(\Iterator $Iterator)
    {
        $fixtures = [];
        foreach ($Iterator as $fixture) {
            // TODO $fixture が \SplFileInfo ではない場合の対応
            $CsvFixture = new CsvFixture($fixture->openFile());
            $this->addFixture($CsvFixture);
            $fixtures[] = $CsvFixture;
        }

        return $fixtures;
    }

    public function getFixtures()
    {
        return $this->fixtures;
    }

    public function addFixture(FixtureInterface $fixture)
    {
        $this->fixtures[] = $fixture;
    }
}
