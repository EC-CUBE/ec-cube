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
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Exception\PluginException;
use Eccube\Repository\BaseInfoRepository;

/**
 * Class ComposerProcessService
 *
 * @deprecated Not maintained
 */
class ComposerProcessService implements ComposerServiceInterface
{
    /**
     * @var EccubeConfig config parameter
     */
    protected $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    private $workingDir;

    /**
     * @var ComposerApiService
     */
    private $composerApiService;
    /**
     * @var BaseInfoRepository
     */
    private $baseInfoRepository;

    /**
     * ComposerProcessService constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param EntityManagerInterface $entityManager
     * @param ComposerApiService $composerApiService
     */
    public function __construct(EccubeConfig $eccubeConfig, EntityManagerInterface $entityManager, ComposerApiService $composerApiService, BaseInfoRepository $baseInfoRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->entityManager = $entityManager;
        $this->composerApiService = $composerApiService;
        $this->baseInfoRepository = $baseInfoRepository;
    }

    public function execRequire($packageName, $output = null)
    {
        return $this->runCommand([
            'eccube:composer:require',
            $packageName,
        ], $output);
    }

    public function execRemove($packageName, $output = null)
    {
        return $this->runCommand([
            'eccube:composer:remove',
            $packageName,
        ], $output);
    }

    /**
     * Run command
     *
     * @throws PluginException
     *
     * @param string $command
     */
    public function runCommand($commands, $output = null, $init = true)
    {
        if ($init) {
            $this->init();
        }

        $command = implode(' ', array_merge(['bin/console'], $commands));
        try {
            // Execute command
            $returnValue = -1;
            $output = [];
            exec($command, $output, $returnValue);

            $outputString = implode(PHP_EOL, $output);
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
     */
    private function init($BaseInfo = null)
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

    public function execConfig($key, $value = null)
    {
        return $this->composerApiService->execConfig($key, $value);
    }

    public function configureRepository(BaseInfo $BaseInfo)
    {
        return $this->composerApiService->configureRepository($BaseInfo);
    }

    public function foreachRequires($packageName, $version, $callback, $typeFilter = null, $level = 0)
    {
        return $this->composerApiService->foreachRequires($packageName, $version, $callback, $typeFilter, $level);
    }
}
