<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Doctrine\ORM\Mapping\Driver\ReloadSafeAnnotationDriver;
use Eccube\Util\StringUtil;

class SchemaService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * SchemaService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateSchema($generatedFiles, $proxiesDirectory)
    {
        $outputDir = sys_get_temp_dir().'/proxy_'.StringUtil::random(12);
        mkdir($outputDir);

        try {
            $chain = $this->entityManager->getConfiguration()->getMetadataDriverImpl();
            $drivers = $chain->getDrivers();
            foreach ($drivers as $namespace => $oldDriver) {
                if ('Eccube\Entity' === $namespace) {
                    $newDriver = new ReloadSafeAnnotationDriver(
                        new AnnotationReader(),
                        $oldDriver->getPaths()
                    );
                    $newDriver->setFileExtension($oldDriver->getFileExtension());
                    $newDriver->addExcludePaths($oldDriver->getExcludePaths());
                    $newDriver->setTraitProxiesDirectory($proxiesDirectory);
                    $newDriver->setNewProxyFiles($generatedFiles);
                    $newDriver->setOutputDir($outputDir);
                    $chain->addDriver($newDriver, $namespace);
                }
            }

            $tool = new SchemaTool($this->entityManager);
            $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
            $tool->updateSchema($metaData, true);
        } finally {
            foreach (glob("${outputDir}/*") as  $f) {
                unlink($f);
            }
            rmdir($outputDir);
        }
    }

    /**
     * ネームスペースに含まれるEntityのテーブルを削除する
     *
     * @param $targetNamespace string 削除対象のネームスペース
     */
    public function dropTable($targetNamespace)
    {
        $chain = $this->entityManager->getConfiguration()->getMetadataDriverImpl();
        $drivers = $chain->getDrivers();

        $dropMetas = [];
        foreach ($drivers as $namespace => $driver) {
            if ($targetNamespace === $namespace) {
                $allClassNames = $driver->getAllClassNames();

                foreach ($allClassNames as $className) {
                    $dropMetas[] = $this->entityManager->getMetadataFactory()->getMetadataFor($className);
                }
            }
        }
        $tool = new SchemaTool($this->entityManager);
        $tool->dropSchema($dropMetas);
    }
}
