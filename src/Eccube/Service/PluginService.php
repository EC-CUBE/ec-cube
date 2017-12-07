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

namespace Eccube\Service;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Plugin;
use Eccube\Entity\PluginEventHandler;
use Eccube\Exception\PluginException;
use Eccube\Plugin\ConfigManager as PluginConfigManager;
use Eccube\Repository\PluginEventHandlerRepository;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerServiceInterface;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @Service
 */
class PluginService
{
    /**
     * @Inject(PluginEventHandlerRepository::class)
     * @var PluginEventHandlerRepository
     */
    protected $pluginEventHandlerRepository;

    /**
     * @Inject("orm.em")
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @Inject(PluginRepository::class)
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(Application::class)
     * @var Application
     */
    protected $app;

    /**
     * @var EntityProxyService
     * @Inject(EntityProxyService::class)
     */
    protected $entityProxyService;

    /**
     * @Inject(SchemaService::class)
     * @var SchemaService
     */
    protected $schemaService;

    /**
     * @Inject("eccube.service.composer")
     * @var ComposerServiceInterface
     */
    protected $composerService;

    const CONFIG_YML = 'config.yml';
    const EVENT_YML = 'event.yml';
    const VENDOR_NAME = 'ec-cube';

    /**
     * Plugin type/library of ec-cube
     */
    const ECCUBE_LIBRARY = 1;

    /**
     * Plugin type/library of other (except ec-cube)
     */
    const OTHER_LIBRARY = 2;

    /**
     * ファイル指定してのプラグインインストール
     *
     * @param string $path   path to tar.gz/zip plugin file
     * @param int    $source
     * @return mixed
     * @throws PluginException
     * @throws \Exception
     */
    public function install($path, $source = 0)
    {
        $pluginBaseDir = null;
        $tmp = null;
        try {
            // プラグイン配置前に実施する処理
            $this->preInstall();
            $tmp = $this->createTempDir();

            // 一旦テンポラリに展開
            $this->unpackPluginArchive($path, $tmp);
            $this->checkPluginArchiveContent($tmp);

            $config = $this->readYml($tmp.'/'.self::CONFIG_YML);
            $event = $this->readYml($tmp.'/'.self::EVENT_YML);
            // テンポラリのファイルを削除
            $this->deleteDirs([$tmp]);

            // 重複していないかチェック
            $this->checkSamePlugin($config['code']);

            $pluginBaseDir = $this->calcPluginDir($config['code']);
            // 本来の置き場所を作成
            $this->createPluginDir($pluginBaseDir);

            // 問題なければ本当のplugindirへ
            $this->unpackPluginArchive($path, $pluginBaseDir);

            // Check dependent plugin
            // Don't install ec-cube library
            $dependents = $this->getDependentByCode($config['code'], self::OTHER_LIBRARY);
            if (!empty($dependents)) {
                $package = $this->parseToComposerCommand($dependents);
                $this->composerService->execRequire($package);
            }

            // プラグイン配置後に実施する処理
            $this->postInstall($config, $event, $source);
            // リソースファイルをコピー
            $this->copyAssets($pluginBaseDir, $config['code']);
        } catch (PluginException $e) {
            $this->deleteDirs(array($tmp, $pluginBaseDir));
            throw $e;
        } catch (\Exception $e) {
            // インストーラがどんなExceptionを上げるかわからないので
            $this->deleteDirs(array($tmp, $pluginBaseDir));
            throw $e;
        }

        return true;
    }

    // インストール事前処理
    public function preInstall()
    {
        // キャッシュの削除
        PluginConfigManager::removePluginConfigCache();
        CacheUtil::clear($this->app, false);
    }

    // インストール事後処理
    public function postInstall($config, $event, $source)
    {
        // Proxyのクラスをロードせずにスキーマを更新するために、
        // インストール時には一時的なディレクトリにProxyを生成する
        $tmpProxyOutputDir = sys_get_temp_dir() . '/proxy_' . StringUtil::random(12);
        @mkdir($tmpProxyOutputDir);

        try {
            // dbにプラグイン登録
            $plugin = $this->registerPlugin($config, $event, $source);

            // インストール時には一時的に利用するProxyを生成してからスキーマを更新する
            $generatedFiles = $this->regenerateProxy($plugin, true, $tmpProxyOutputDir);
            $this->schemaService->updateSchema($generatedFiles, $tmpProxyOutputDir);

            PluginConfigManager::writePluginConfigCache();
        } finally {
            foreach (glob("${tmpProxyOutputDir}/*") as  $f) {
                unlink($f);
            }
            rmdir($tmpProxyOutputDir);
        }
    }

    public function createTempDir()
    {
        @mkdir($this->appConfig['plugin_temp_realdir']);
        $d = ($this->appConfig['plugin_temp_realdir'].'/'.sha1(StringUtil::random(16)));

        if (!mkdir($d, 0777)) {
            throw new PluginException($php_errormsg.$d);
        }

        return $d;
    }

    /**
     * Deletes files, directories and symlinks
     *
     * @param array|string $dirs
     */
    public function deleteDirs($dirs)
    {
        if (!is_array($dirs)) {
            $dirs = [$dirs];
        }
        $fs = new Filesystem();
        foreach ($dirs as $dir) {
            if (is_dir($dir) || file_exists($dir)) {
                $fs->remove($dir);
            }
        }
    }

    public function unpackPluginArchive($archive, $dir)
    {
        $extension = pathinfo($archive, PATHINFO_EXTENSION);
        try {
            if ($extension == 'zip') {
                $zip = new \ZipArchive();
                $zip->open($archive);
                $zip->extractTo($dir);
                $zip->close();
            } else {
                $phar = new \PharData($archive);
                $phar->extractTo($dir, null, true);
            }
        } catch (\Exception $e) {
            throw new PluginException('アップロードに失敗しました。圧縮ファイルを確認してください。');
        }
    }

    public function checkPluginArchiveContent($dir, array $config_cache = array())
    {
        try {
            if (!empty($config_cache)) {
                $meta = $config_cache;
            } else {
                $meta = $this->readYml($dir . '/config.yml');
            }
        } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
            throw new PluginException($e->getMessage(), $e->getCode(), $e);
        }

        if (!is_array($meta)) {
            throw new PluginException('config.yml not found or syntax error');
        }
        if (!isset($meta['code']) || !$this->checkSymbolName($meta['code'])) {
            throw new PluginException('config.yml code empty or invalid_character(\W)');
        }
        if (!isset($meta['name'])) {
            // nameは直接クラス名やPATHに使われるわけではないため文字のチェックはなしし
            throw new PluginException('config.yml name empty');
        }
        if (isset($meta['event']) && !$this->checkSymbolName($meta['event'])) { // eventだけは必須ではない
            throw new PluginException('config.yml event empty or invalid_character(\W) ');
        }
        if (!isset($meta['version'])) {
            // versionは直接クラス名やPATHに使われるわけではないため文字のチェックはなしし
            throw new PluginException('config.yml version invalid_character(\W) ');
        }
        if (isset($meta['orm.path'])) {
            if (!is_array($meta['orm.path'])) {
                throw new PluginException('config.yml orm.path invalid_character(\W) ');
            }
        }
        if (isset($meta['service'])) {
            if (!is_array($meta['service'])) {
                throw new PluginException('config.yml service invalid_character(\W) ');
            }
        }
    }

    public function readYml($yml)
    {
        if (file_exists($yml)) {
            return Yaml::parse(file_get_contents($yml));
        }

        return false;
    }

    public function checkSymbolName($string)
    {
        return strlen($string) < 256 && preg_match('/^\w+$/', $string);
        // plugin_nameやplugin_codeに使える文字のチェック
        // a-z A-Z 0-9 _
        // ディレクトリ名などに使われれるので厳しめ
    }

    public function checkSamePlugin($code)
    {
        $repo = $this->pluginRepository->findOneBy(array('code' => $code));
        if ($repo) {
            throw new PluginException('plugin already installed.');
        }
    }

    public function calcPluginDir($name)
    {
        return $this->appConfig['plugin_realdir'].'/'.$name;
    }

    public function createPluginDir($d)
    {
        $b = @mkdir($d);
        if (!$b) {
            throw new PluginException($php_errormsg);
        }
    }

    public function registerPlugin($meta, $event_yml, $source = 0)
    {
        $em = $this->entityManager;
        $em->getConnection()->beginTransaction();
        try {
            $p = new \Eccube\Entity\Plugin();
            // インストール直後はプラグインは有効にしない
            $p->setName($meta['name'])
                ->setEnabled(false)
                ->setClassName(isset($meta['event']) ? $meta['event'] : '')
                ->setVersion($meta['version'])
                ->setSource($source)
                ->setCode($meta['code']);

            $em->persist($p);
            $em->flush();

            if (is_array($event_yml)) {
                foreach ($event_yml as $event => $handlers) {
                    foreach ($handlers as $handler) {
                        if (!$this->checkSymbolName($handler[0])) {
                            throw new PluginException('Handler name format error');
                        }
                        $peh = new \Eccube\Entity\PluginEventHandler();
                        $peh->setPlugin($p)
                            ->setEvent($event)
                            ->setHandler($handler[0])
                            ->setHandlerType($handler[1])
                            ->setPriority($this->pluginEventHandlerRepository->calcNewPriority($event, $handler[1]));
                        $em->persist($peh);
                        $em->flush();
                    }
                }
            }

            $em->persist($p);

            $this->callPluginManagerMethod($meta, 'install');

            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw new PluginException($e->getMessage());
        }

        return $p;
    }

    public function callPluginManagerMethod($meta, $method)
    {
        $class = '\\Plugin'.'\\'.$meta['code'].'\\'.'PluginManager';
        if (class_exists($class)) {
            $installer = new $class(); // マネージャクラスに所定のメソッドがある場合だけ実行する
            if (method_exists($installer, $method)) {
                $installer->$method($meta, $this->app);
            }
        }
    }

    public function uninstall(\Eccube\Entity\Plugin $plugin)
    {
        $pluginDir = $this->calcPluginDir($plugin->getCode());
        PluginConfigManager::removePluginConfigCache();
        CacheUtil::clear($this->app, false);
        $this->callPluginManagerMethod(Yaml::parse(file_get_contents($pluginDir.'/'.self::CONFIG_YML)), 'disable');
        $this->callPluginManagerMethod(Yaml::parse(file_get_contents($pluginDir.'/'.self::CONFIG_YML)), 'uninstall');
        $this->disable($plugin);
        $this->unregisterPlugin($plugin);
        $this->deleteDirs([$pluginDir]);
        $this->removeAssets($plugin->getCode());

        // スキーマを更新する
        $this->schemaService->updateSchema([], $this->appConfig['root_dir'].'/app/proxy/entity');

        PluginConfigManager::writePluginConfigCache();
        return true;
    }

    public function unregisterPlugin(\Eccube\Entity\Plugin $p)
    {
        try {
            $em = $this->entityManager;
            foreach ($p->getPluginEventHandlers()->toArray() as $peh) {
                $em->remove($peh);
            }
            $em->remove($p);
            $em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function disable(\Eccube\Entity\Plugin $plugin)
    {
        return $this->enable($plugin, false);
    }

    /**
     * Proxyを再生成します.
     * @param Plugin $plugin プラグイン
     * @param boolean $temporary プラグインが無効状態でも一時的に生成するかどうか
     * @param string|null $outputDir 出力先
     * @return array 生成されたファイルのパス
     */
    private function regenerateProxy(Plugin $plugin, $temporary, $outputDir = null)
    {
        if (is_null($outputDir)) {
            $outputDir = $this->appConfig['root_dir'].'/app/proxy/entity';
        }
        @mkdir($outputDir);

        $enabledPluginCodes = array_map(
            function($p) { return $p->getCode(); },
            $this->pluginRepository->findAllEnabled()
        );

        $excludes = [];
        if ($temporary || $plugin->isEnabled()) {
            $enabledPluginCodes[] = $plugin->getCode();
        } else {
            $index = array_search($plugin->getCode(), $enabledPluginCodes);
            if ($index >= 0) {
                array_splice($enabledPluginCodes, $index, 1);
                $excludes = [$this->appConfig['root_dir']."/app/Plugin/".$plugin->getCode()."/Entity"];
            }
        }

        $enabledPluginEntityDirs = array_map(function($code) {
            return $this->appConfig['root_dir']."/app/Plugin/${code}/Entity";
        }, $enabledPluginCodes);

        return $this->entityProxyService->generate(
            array_merge([$this->appConfig['root_dir'].'/app/Acme/Entity'], $enabledPluginEntityDirs),
            $excludes,
            $outputDir
        );
    }

    public function enable(\Eccube\Entity\Plugin $plugin, $enable = true)
    {
        $em = $this->entityManager;
        try {
            PluginConfigManager::removePluginConfigCache();
            CacheUtil::clear($this->app, false);
            $pluginDir = $this->calcPluginDir($plugin->getCode());
            $em->getConnection()->beginTransaction();
            $plugin->setEnabled($enable ? true : false);
            $em->persist($plugin);
            $this->callPluginManagerMethod($this->readYml($pluginDir.'/'.self::CONFIG_YML), $enable ? 'enable' : 'disable');
            // Proxyだけ再生成してスキーマは更新しない
            $this->regenerateProxy($plugin, false);

            $em->flush();
            $em->getConnection()->commit();
            PluginConfigManager::writePluginConfigCache();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * Update plugin
     *
     * @param Plugin $plugin
     * @param string $path
     * @return bool
     * @throws PluginException
     * @throws \Exception
     */
    public function update(\Eccube\Entity\Plugin $plugin, $path)
    {
        $pluginBaseDir = null;
        $tmp = null;
        try {
            PluginConfigManager::removePluginConfigCache();
            CacheUtil::clear($this->app, false);
            $tmp = $this->createTempDir();

            $this->unpackPluginArchive($path, $tmp); //一旦テンポラリに展開
            $this->checkPluginArchiveContent($tmp);

            $config = $this->readYml($tmp.'/'.self::CONFIG_YML);
            $event = $this->readYml($tmp.'/'.self::EVENT_YML);

            if ($plugin->getCode() != $config['code']) {
                throw new PluginException('new/old plugin code is different.');
            }

            $pluginBaseDir = $this->calcPluginDir($config['code']);
            $this->deleteDirs([$tmp]); // テンポラリのファイルを削除

            $this->unpackPluginArchive($path, $pluginBaseDir); // 問題なければ本当のplugindirへ

            // Check dependent plugin
            // Don't install ec-cube library
            $dependents = $this->getDependentByCode($config['code'], self::OTHER_LIBRARY);
            if (!empty($dependents)) {
                $package = $this->parseToComposerCommand($dependents);
                $this->composerService->execRequire($package);
            }

            $this->updatePlugin($plugin, $config, $event); // dbにプラグイン登録
            PluginConfigManager::writePluginConfigCache();
        } catch (PluginException $e) {
            $this->deleteDirs([$tmp]);
            throw $e;
        } catch (\Exception $e) {
            // catch exception of composer
            $this->deleteDirs([$tmp]);
            throw $e;
        }

        return true;
    }

    /**
     * Update plugin
     *
     * @param Plugin $plugin
     * @param array  $meta     Config data
     * @param array  $eventYml event data
     * @throws \Exception
     */
    public function updatePlugin(Plugin $plugin, $meta, $eventYml)
    {
        $em = $this->entityManager;
        try {
            $em->getConnection()->beginTransaction();
            $plugin->setVersion($meta['version'])
                ->setName($meta['name']);
            if (isset($meta['event'])) {
                $plugin->setClassName($meta['event']);
            }
            $rep = $this->pluginEventHandlerRepository;
            if (!empty($eventYml) && is_array($eventYml)) {
                foreach ($eventYml as $event => $handlers) {
                    foreach ($handlers as $handler) {
                        if (!$this->checkSymbolName($handler[0])) {
                            throw new PluginException('Handler name format error');
                        }
                        // updateで追加されたハンドラかどうか調べる
                        $peh = $rep->findBy(
                            array(
                            'plugin_id' => $plugin->getId(),
                            'event' => $event,
                            'handler' => $handler[0],
                            'handler_type' => $handler[1],
                                )
                        );

                        // 新規にevent.ymlに定義されたハンドラなのでinsertする
                        if (!$peh) {
                            $peh = new PluginEventHandler();
                            $peh->setPlugin($plugin)
                                ->setEvent($event)
                                ->setHandler($handler[0])
                                ->setHandlerType($handler[1])
                                ->setPriority($rep->calcNewPriority($event, $handler[1]));
                            $em->persist($peh);
                            $em->flush();
                        }
                    }
                }

                // アップデート後のevent.ymlで削除されたハンドラをdtb_plugin_event_handlerから探して削除
                /** @var PluginEventHandler $peh */
                foreach ($rep->findBy(array('plugin_id' => $plugin->getId())) as $peh) {
                    if (!isset($eventYml[$peh->getEvent()])) {
                        $em->remove($peh);
                        $em->flush();
                    } else {
                        $match = false;
                        foreach ($eventYml[$peh->getEvent()] as $handler) {
                            if ($peh->getHandler() == $handler[0] && $peh->getHandlerType() == $handler[1]) {
                                $match = true;
                            }
                        }
                        if (!$match) {
                            $em->remove($peh);
                            $em->flush();
                        }
                    }
                }
            }

            $em->persist($plugin);
            $this->callPluginManagerMethod($meta, 'update');
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Do check dependency plugin
     *
     * @param array $plugins    get from api
     * @param array $plugin     format as plugin from api
     * @param array $dependents template output
     * @return array|mixed
     */
    public function getDependency($plugins, $plugin, $dependents = array())
    {
        // Prevent infinity loop
        if (empty($dependents)) {
            $dependents[] = $plugin;
        }

        // Check dependency
        if (!isset($plugin['require']) || empty($plugin['require'])) {
            return $dependents;
        }

        $require = $plugin['require'];
        // Check dependency
        foreach ($require as $pluginName => $version) {
            $dependPlugin = $this->buildInfo($plugins, $pluginName);
            // Prevent call self
            if (!$dependPlugin || $dependPlugin['product_code'] == $plugin['product_code']) {
                continue;
            }

            // Check duplicate in dependency
            $index = array_search($dependPlugin['product_code'], array_column($dependents, 'product_code'));
            if ($index === false) {
                // Update require version
                $dependPlugin['version'] = $version;
                $dependents[] = $dependPlugin;
                // Check child dependency
                $dependents = $this->getDependency($plugins, $dependPlugin, $dependents);
            }
        }

        return $dependents;
    }

    /**
     * Get plugin information
     *
     * @param array  $plugins    get from api
     * @param string $pluginCode
     * @return array|null
     */
    public function buildInfo($plugins, $pluginCode)
    {
        $plugin = [];
        $index = $this->checkPluginExist($plugins, $pluginCode);
        if ($index === false) {
            return $plugin;
        }
        // Get target plugin in return of api
        $plugin = $plugins[$index];

        // Check the eccube version that the plugin supports.
        $plugin['is_supported_eccube_version'] = 0;
        if (in_array(Constant::VERSION, $plugin['eccube_version'])) {
            // Match version
            $plugin['is_supported_eccube_version'] = 1;
        }

        $plugin['depend'] = $this->getRequirePluginName($plugins, $plugin);

        return $plugin;
    }

    /**
     * Get dependency name and version only
     *
     * @param array $plugins get from api
     * @param array $plugin  target plugin from api
     * @return mixed format [0 => ['name' => pluginName1, 'version' => pluginVersion1], 1 => ['name' => pluginName2, 'version' => pluginVersion2]]
     */
    public function getRequirePluginName($plugins, $plugin)
    {
        $depend = [];
        if (isset($plugin['require']) && !empty($plugin['require'])) {
            foreach ($plugin['require'] as $name => $version) {
                $ret = $this->checkPluginExist($plugins, $name);
                if ($ret === false) {
                    continue;
                }
                $depend[] = [
                    'name' => $plugins[$ret]['name'],
                    'version' => $version,
                ];
            }
        }

        return $depend;
    }

    /**
     * Check require plugin in enable
     *
     * @param string $pluginCode
     * @return array plugin code
     */
    public function findRequirePluginNeedEnable($pluginCode)
    {
        $dir = $this->appConfig['plugin_realdir'].'/'.$pluginCode;
        $composerFile = $dir.'/composer.json';
        if (!file_exists($composerFile)) {
            return [];
        }
        $jsonText = file_get_contents($composerFile);
        $json = json_decode($jsonText, true);
        // Check require
        if (!isset($json['require']) || empty($json['require'])) {
            return [];
        }
        $require = $json['require'];

        // Remove vendor plugin
        if (isset($require[self::VENDOR_NAME.'/plugin-installer'])) {
            unset($require[self::VENDOR_NAME.'/plugin-installer']);
        }
        $requires = [];
        foreach ($require as $name => $version) {
            // Check plugin of ec-cube only
            if (strpos($name, self::VENDOR_NAME.'/') !== false) {
                $requireCode = str_replace(self::VENDOR_NAME.'/', '', $name);
                $ret = $this->isEnable($requireCode);
                if ($ret) {
                    continue;
                }
                $requires[] = $requireCode;
            }
        }

        return $requires;
    }
    /**
     * Find the dependent plugins that need to be disabled
     *
     * @param string $pluginCode
     * @return array plugin code
     */
    public function findDependentPluginNeedDisable($pluginCode)
    {
        return $this->findDependentPlugin($pluginCode, true);
    }

    /**
     * Find the other plugin that has requires on it.
     * Check in both dtb_plugin table and <PluginCode>/composer.json
     *
     * @param string $pluginCode
     * @param bool   $enableOnly
     * @return array plugin code
     */
    public function findDependentPlugin($pluginCode, $enableOnly = false)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('code', $pluginCode));
        if ($enableOnly) {
            $criteria->andWhere(Criteria::expr()->eq('enabled', Constant::ENABLED));
        }
        /**
         * @var Plugin[] $plugins
         */
        $plugins = $this->pluginRepository->matching($criteria);
        $dependents = [];
        foreach ($plugins as $plugin) {
            $dir = $this->appConfig['plugin_realdir'].'/'.$plugin->getCode();
            $fileName = $dir.'/composer.json';
            if (!file_exists($fileName)) {
                continue;
            }
            $jsonText = file_get_contents($fileName);
            if ($jsonText) {
                $json = json_decode($jsonText, true);
                if (!isset($json['require'])) {
                    continue;
                }
                if (array_key_exists(self::VENDOR_NAME.'/'.$pluginCode, $json['require'])) {
                    $dependents[] = $plugin->getCode();
                }
            }
        }

        return $dependents;
    }

    /**
     * Get dependent plugin by code
     * It's base on composer.json
     * Return the plugin code and version in the format of the composer
     *
     * @param string   $pluginCode
     * @param int|null $libraryType
     *                      self::ECCUBE_LIBRARY only return library/plugin of eccube
     *                      self::OTHER_LIBRARY only return library/plugin of 3rd part ex: symfony, composer, ...
     *                      default : return all library/plugin
     * @return array format [packageName1 => version1, packageName2 => version2]
     */
    public function getDependentByCode($pluginCode, $libraryType = null)
    {
        $pluginDir = $this->calcPluginDir($pluginCode);
        $jsonFile = $pluginDir.'/composer.json';
        if (!file_exists($jsonFile)) {
            return [];
        }
        $jsonText = file_get_contents($jsonFile);
        $json = json_decode($jsonText, true);
        $dependents = [];
        if (isset($json['require'])) {
            $require = $json['require'];
            switch ($libraryType) {
                case self::ECCUBE_LIBRARY:
                    $dependents = array_intersect_key($require, array_flip(preg_grep('/^'.self::VENDOR_NAME.'\//i', array_keys($require))));
                    break;

                case self::OTHER_LIBRARY:
                    $dependents = array_intersect_key($require, array_flip(preg_grep('/^'.self::VENDOR_NAME.'\//i', array_keys($require), PREG_GREP_INVERT)));
                    break;

                default:
                    $dependents = $json['require'];
                    break;
            }
        }

        return $dependents;
    }

    /**
     * Format array dependent plugin to string
     * It is used for commands.
     *
     * @param array $packages   format [packageName1 => version1, packageName2 => version2]
     * @param bool  $getVersion
     * @return string format if version=true: "packageName1:version1 packageName2:version2", if version=false: "packageName1 packageName2"
     */
    public function parseToComposerCommand(array $packages, $getVersion = true)
    {
        $result = array_keys($packages);
        if ($getVersion) {
            $result = array_map(function ($package, $version) {
                return $package.':'.$version;
            }, array_keys($packages), array_values($packages));
        }

        return implode(' ', $result);
    }

    /**
     * リソースファイル等をコピー
     * コピー元となるファイルの置き場所は固定であり、
     * [プラグインコード]/Resource/assets
     * 配下に置かれているファイルが所定の位置へコピーされる
     *
     * @param string $pluginBaseDir
     * @param string $pluginCode
     */
    public function copyAssets($pluginBaseDir, $pluginCode)
    {
        $assetsDir = $pluginBaseDir.'/Resource/assets';

        // プラグインにリソースファイルがあれば所定の位置へコピー
        if (file_exists($assetsDir)) {
            $file = new Filesystem();
            $file->mirror($assetsDir, $this->appConfig['plugin_html_realdir'].$pluginCode.'/assets');
        }
    }

    /**
     * コピーしたリソースファイル等を削除
     *
     * @param string $pluginCode
     */
    public function removeAssets($pluginCode)
    {
        $assetsDir = $this->appConfig['plugin_html_realdir'].$pluginCode;
        $this->deleteDirs($assetsDir);
    }

    /**
     * Is update
     *
     * @param string $pluginVersion
     * @param string $remoteVersion
     * @return mixed
     */
    public function isUpdate($pluginVersion, $remoteVersion)
    {
        return version_compare($pluginVersion, $remoteVersion, '<');
    }

    /**
     * Plugin is exist check
     *
     * @param array  $plugins    get from api
     * @param string $pluginCode
     * @return false|int|string
     */
    public function checkPluginExist($plugins, $pluginCode)
    {
        if (strpos($pluginCode, self::VENDOR_NAME.'/') !== false) {
            $pluginCode = str_replace(self::VENDOR_NAME.'/', '', $pluginCode);
        }
        // Find plugin in array
        $index = array_search($pluginCode, array_column($plugins, 'product_code'));

        return $index;
    }

    /**
     * Do rollback when update fail
     *
     * @param array $updateData
     * @throws \Exception
     * @return bool
     */
    public function doUpgradeRollback($updateData)
    {
        if (empty($updateData)) {
            return false;
        }
        $pluginRealDir = $this->appConfig['plugin_realdir'];
        $pluginTempRealDir = $this->appConfig['plugin_temp_realdir'];
        $file = new Filesystem();

        // rollback composer file
        $targetFile = $this->appConfig['root_dir'].'/composer.json';
        $backupComposerFile = $pluginTempRealDir.'/composer.json';
        if (file_exists($backupComposerFile)) {
            $this->deleteDirs($targetFile);
            $file->copy($backupComposerFile, $targetFile);
            $file->remove($backupComposerFile);
        }
        $targetLockFile = $this->appConfig['root_dir'].'/composer.lock';
        $backupLockFile = $pluginTempRealDir.'/composer.lock';
        if (file_exists($backupLockFile)) {
            $this->deleteDirs($targetLockFile);
            $file->copy($backupLockFile, $targetLockFile);
            $file->remove($backupLockFile);
        }

        foreach ($updateData as $value) {
            if (!isset($value['product_code'])) {
                throw new PluginException('The format incorrect!');
            }
            $code = $value['product_code'];
            $pluginDir = $pluginRealDir.'/'.$code;
            $backupDir = $pluginTempRealDir.'/'.$code;
            if (!is_dir($backupDir)) {
                continue;
            }
            $Plugin = $this->pluginRepository->findOneBy(['code' => $code]);
            try {
                $file->mirror($backupDir, $pluginDir, null, ['override' => true, 'delete' => true]);
                $file->remove($backupDir);
                if ($Plugin) {
                    $configYml = $this->readYml($pluginDir.'/'.self::CONFIG_YML);
                    $eventYml = $this->readYml($pluginDir.'/'.self::EVENT_YML);
                    $this->updatePlugin($Plugin, $configYml, $eventYml);
                }
            } catch (\Exception $e) {
                log_error($e->getMessage());
                throw $e;
            }
        }

        return true;
    }

    /**
     * Backup file before upgrade
     *
     * @param array $updateData
     * @throws PluginException
     * @return bool
     */
    public function doUpgradeBackup($updateData)
    {
        if (empty($updateData)) {
            return false;
        }
        $pluginRealDir = $this->appConfig['plugin_realdir'];
        $pluginTempRealDir = $this->appConfig['plugin_temp_realdir'];

        $file = new Filesystem();
        // Backup composer file
        $file->copy($this->appConfig['root_dir'].'/composer.json', $pluginTempRealDir.'/composer.json', true);
        $file->copy($this->appConfig['root_dir'].'/composer.lock', $pluginTempRealDir.'/composer.lock', true);

        foreach ($updateData as $value) {
            if (!isset($value['product_code'])) {
                throw new PluginException('The format incorrect!');
            }
            $pluginDir = $pluginRealDir.'/'.$value['product_code'];
            if (!is_dir($pluginDir)) {
                continue;
            }
            $backupDir = $pluginTempRealDir.'/'.$value['product_code'];
            try {
                $file->remove($backupDir);
                $file->mirror($pluginDir, $backupDir);
            } catch (\Exception $e) {
                log_error($e->getMessage());
                continue;
            }
        }

        return true;
    }

    /**
     * @param string $code
     * @return bool
     */
    private function isEnable($code)
    {
        $Plugin = $this->pluginRepository->findOneBy([
            'enabled' => Constant::ENABLED,
            'code' => $code
        ]);
        if ($Plugin) {
            return true;
        }

        return false;
    }
}
