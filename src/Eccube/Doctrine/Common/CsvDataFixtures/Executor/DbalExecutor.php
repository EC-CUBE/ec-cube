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

namespace Eccube\Doctrine\Common\CsvDataFixtures\Executor;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Doctrine Dbal を使用した Executor.
 *
 * @see https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/Executor/AbstractExecutor.php
 * @see https://gist.github.com/gskema/a182aaf7cc04001aebba9c1aad86b40b
 */
class DbalExecutor extends AbstractExecutor
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * DbalExecutor constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $fixtures, $append = false)
    {
        if ($append) {
            trigger_error('$append parameter is not supported.', E_USER_WARNING);
        }
        foreach ($fixtures as $CsvFixture) {
            $CsvFixture->load($this->entityManager);
        }
    }
}
