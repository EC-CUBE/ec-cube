<?php

namespace Eccube\Doctrine\Common\CsvDataFixtures\Executor;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

/**
 * TODO AbstractExecutor を継承するか, ExecutorInterface を作成する
 * @see https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/Executor/AbstractExecutor.php
 * @see https://gist.github.com/gskema/a182aaf7cc04001aebba9c1aad86b40b
 */
class DbalExecutor extends AbstractExecutor
{
    /** @var $em \Doctrine\ORM\EntityManager */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function execute(array $fixtures, $append = false)
    {
        foreach ($fixtures as $CsvFixture) {
            $CsvFixture->load($this->em);
        }
    }
}
