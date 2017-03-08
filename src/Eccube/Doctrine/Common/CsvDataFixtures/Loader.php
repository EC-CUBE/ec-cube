<?php

namespace Eccube\Doctrine\Common\CsvDataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\Finder\Finder;

class Loader
{
    protected $fixtures;

    /**
     * Load fixtures from directory.
     *
     * @param string $dir
     * @return array fixtures.
     */
    public function loadFromDirectory($dir)
    {
        if (!dir($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist', $dir));
        }
        $finder = Finder::create()
            ->in($dir)
            ->name('*.csv')
            ->sortByName()
            ->files();
        return $this->loadFromIterator($finder->getIterator());
    }

    /**
     * Load fixtures from Iterator.
     *
     * @param \Iterator $Iterator Iterator of \SplFileInfo 
     * @return array fixtures.
     */
    public function loadFromIterator(\Iterator $Iterator)
    {
        $fixtures = [];
        foreach ($Iterator as $fixture) {
            // TODO $fixture が \SplFileInfo ではない場合の対応
            $this->addFixture(new CsvFixture($fixture->openFile()));
            $fixtures[] = $fixture;
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
