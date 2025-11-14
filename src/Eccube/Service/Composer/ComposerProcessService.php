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

namespace Eccube\Service\Composer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Exception\PluginException;
use Eccube\Repository\BaseInfoRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ComposerProcessService
 *
 * @deprecated Not maintained
 */
class ComposerProcessService implements ComposerServiceInterface
{
    /**
     * ComposerProcessService constructor.
     */
    public function __construct(
        /**
         * @var EccubeConfig config parameter
         */
        protected EccubeConfig $eccubeConfig,
        protected EntityManagerInterface $entityManager,
        private readonly ComposerApiService $composerApiService,
        private readonly BaseInfoRepository $baseInfoRepository,
    ) {
    }

    #[\Override]
    public function execRequire($packageName, ?OutputInterface $output = null, ?string $from = null): string
    {
        return $this->runCommand([
            'eccube:composer:require',
            $packageName,
        ], $output);
    }

    #[\Override]
    public function execRemove($packageName, ?OutputInterface $output = null): string
    {
        return $this->runCommand([
            'eccube:composer:remove',
            $packageName,
        ], $output);
    }

    /**
     * @param string[] $commands
     *
     * @throws PluginException
     */
    public function runCommand(array $commands, ?OutputInterface $output = null, bool $init = true): string
    {
        if ($init) {
            $this->init();
        }

        $command = implode(' ', array_merge(['bin/console'], $commands));
        try {
            // Execute command
            $returnValue = -1;
            $commandOutput = [];
            exec($command, $commandOutput, $returnValue);

            $outputString = implode(PHP_EOL, $commandOutput);
            if ($returnValue) {
                throw new PluginException($outputString);
            }
            log_info(PHP_EOL.$outputString.PHP_EOL);

            return $outputString;
        } catch (\Exception $exception) {
            throw new PluginException($exception->getMessage());
        }
    }

    /**
     * Set init
     *
     * @throws PluginException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function init(?BaseInfo $BaseInfo = null): void
    {
        //        /**
        //         * Mysql lock in transaction
        //         *
        //         * @see https://dev.mysql.com/doc/refman/5.7/en/lock-tables.html
        //         *
        //         * @var EntityManagerInterface
        //         */
        //        $em = $this->entityManager;
        //        if ($em->getConnection()->isTransactionActive()) {
        //            $em->getConnection()->commit();
        //            $em->getConnection()->beginTransaction();
        //        }

        $BaseInfo = $BaseInfo ?: $this->baseInfoRepository->get();
        $this->composerApiService->configureRepository($BaseInfo);
    }

    /**
     * @param string $key
     * @param string[]|null $value
     *
     * @return array<int|string, array<int, string>>|null
     */
    #[\Override]
    public function execConfig($key, $value = null): ?array
    {
        return $this->composerApiService->execConfig($key, $value);
    }

    #[\Override]
    public function configureRepository(BaseInfo $BaseInfo): void
    {
        $this->composerApiService->configureRepository($BaseInfo);
    }

    /**
     * @param string $packageName
     * @param string|null $version
     * @param callable $callback
     * @param string|null $typeFilter
     * @param int $level
     *
     * @throws PluginException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[\Override]
    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0): void
    {
        $this->composerApiService->foreachRequires($packageName, $version, $callback, $typeFilter, $level);
    }
}
