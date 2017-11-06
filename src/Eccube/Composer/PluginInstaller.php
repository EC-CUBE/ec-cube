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
namespace Eccube\Composer;

use Composer\Composer;
use Composer\Installer\BinaryInstaller;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use Eccube\Entity\Plugin;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;
use Eccube\Common\Constant;

/**
 * Class PluginInstaller
 * Override composer installer
 *
 * @package Eccube\Composer
 */
class PluginInstaller extends LibraryInstaller
{
    /**
     * Initializes library installer.
     *
     * @param IOInterface     $io
     * @param Composer        $composer
     * @param string          $type
     * @param Filesystem      $filesystem
     * @param BinaryInstaller $binaryInstaller
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'eccube-plugin', Filesystem $filesystem = null, BinaryInstaller $binaryInstaller = null)
    {
        $type = 'eccube-plugin';
        parent::__construct($io, $composer, $type, $filesystem, $binaryInstaller);
    }

    /**
     * Get install path (plugin path)
     *
     * @param PackageInterface $package
     * @return string
     */
    public function getInstallPath(PackageInterface $package)
    {
        $extra = $package->getExtra();
        if (!isset($extra['code'])) {
            throw new \RuntimeException('`extra.code` not found in '.$package->getName().'/composer.json');
        }

        return "app/Plugin/".$extra['code'];
    }

    /**
     * Override command composer install/require
     *
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface             $package
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);

        // Install to database
        $extra = $package->getExtra();
        $app = $this->getApplication();
        $code = $extra['code'];
        $configYml = Yaml::parse(file_get_contents($app['config']['plugin_realdir'].'/'.$code.'/config.yml'));
        $eventYml = Yaml::parse(file_get_contents($app['config']['plugin_realdir'].'/'.$code.'/event.yml'));

        $app['eccube.service.plugin']->preInstall();
        $app['eccube.service.plugin']->postInstall($configYml, $eventYml, @$extra['id']);
    }

    /**
     * Override command composer remove
     *
     * @param InstalledRepositoryInterface $repo
     * @param PackageInterface             $package
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $extra = $package->getExtra();
        $code = $extra['code'];

        $app = $this->getApplication();
        $dependents = $app['eccube.service.plugin']->findDependentPluginNeedDisable($code);
        if (!empty($dependents)) {
            throw new \RuntimeException('このプラグインに依存しているプラグインがあるため削除できません。'.$dependents[0]);
        }

        if (isset($extra['id']) && $extra['id']) {
            $id = $extra['id'];
            /** @var Plugin $Plugin */
            $Plugin = $app['eccube.repository.plugin']->findOneBy(array('source' => $id));
            if ($Plugin->getEnable() !== Constant::DISABLED) {
                throw new RuntimeException('プラグインを無効化してください。'.$code);
            }
            if ($Plugin) {
                $app['eccube.service.plugin']->uninstall($Plugin);
            }
        }

        parent::uninstall($repo, $package);
    }

    /**
     * New application in the composer process
     *
     * @return \Eccube\Application
     */
    private function getApplication()
    {
        $loader = require_once __DIR__.'/../../../autoload.php';

        $app = \Eccube\Application::getInstance(['eccube.autoloader' => $loader]);
        if (!$app->isBooted()) {
            $app->initialize();
            $app->boot();
        }

        return $app;
    }
}
