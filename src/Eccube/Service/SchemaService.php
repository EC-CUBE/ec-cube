<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Service;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Eccube\Doctrine\ORM\Mapping\Driver\ReloadSafeAnnotationDriver;
use Eccube\Util\Str;

/**
 * @Service
 */
class SchemaService
{

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    public function updateSchema($generatedFiles, $proxiesDirectory)
    {
        $outputDir = sys_get_temp_dir() . '/proxy_' . Str::random(12);
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
}