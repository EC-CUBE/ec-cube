<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube;

use Knp\Provider\ConsoleServiceProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ConsoleApplication extends \Silex\Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $app = $this;

        // load databtase config
        $config = array();
        $file = __DIR__ . '/../../app/config/eccube/database.yml';
        if (file_exists($file)) {
            $config = Yaml::parse($file);
        }

        // Doctrine ORM
        $app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => $config['database']
        ));

        $app->register(new \Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());

        $app->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), array(
            "orm.proxies_dir" => __DIR__ . '/../../app/cache/doctrine',
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'yml',
                        'namespace' => 'Eccube\Entity',
                        'path' => array(
                            __DIR__ . '/Resource/doctrine',
                            __DIR__ . '/Resource/doctrine/master',
                        ),
                    ),
                ),
            ),
        ));

        // Migration
        $app->register(
            new ConsoleServiceProvider(),
            array(
                'console.name' => 'EC-CUBE',
                'console.version' => '3.0.0',
                'console.project_directory' => __DIR__ . "/.."
            )
        );

        $app->register(new \Dbtlr\MigrationProvider\Provider\MigrationServiceProvider(), array(
            'db.migrations.path' => __DIR__ . '/Resource/doctrine/migration',
        ));

        $app->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__ . '/../../app/log/miglation.log',
        ));
    }
}
