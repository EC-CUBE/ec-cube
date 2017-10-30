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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginException;
use Eccube\Plugin\ConfigManager;
use Eccube\Plugin\ConfigManager as PluginConfigManager;
use Eccube\Repository\PluginEventHandlerRepository;
use Eccube\Repository\PluginRepository;
use Eccube\Util\Cache;
use Eccube\Util\Str;
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
     * @var EntityManager
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

    const CONFIG_YML = 'config.yml';
    const EVENT_YML = 'event.yml';
    const VENDOR_NAME = 'ec-cube';

    // ファイル指定してのプラグインインストール
    public function install($path, $source = 0)
    {
        $pluginBaseDir = null;
        $tmp = null;

        try {
            // プラグイン配置前に実施する処理
            $this->preInstall();

            $tmp = $this->createTempDir();

            $this->unpackPluginArchive($path, $tmp); //一旦テンポラリに展開
            $this->checkPluginArchiveContent($tmp);

            $config = $this->readYml($tmp.'/'.self::CONFIG_YML);
            $event = $this->readYml($tmp.'/'.self::EVENT_YML);
            $this->deleteFile($tmp); // テンポラリのファイルを削除

            $this->checkSamePlugin($config['code']); // 重複していないかチェック

            $pluginBaseDir = $this->calcPluginDir($config['code']);
            $this->createPluginDir($pluginBaseDir); // 本来の置き場所を作成

            $this->unpackPluginArchive($path, $pluginBaseDir); // 問題なければ本当のplugindirへ

            // プラグイン配置後に実施する処理
            $this->postInstall($config, $event, $source);
        } catch (PluginException $e) {
            $this->deleteDirs(array($tmp, $pluginBaseDir));
            throw $e;
        } catch (\Exception $e) { // インストーラがどんなExceptionを上げるかわからないので

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
        Cache::clear($this->app, false);
    }

    // インストール事後処理
    public function postInstall($config, $event, $source)
    {
        // Proxyのクラスをロードせずにスキーマを更新するために、
        // インストール時には一時的なディレクトリにProxyを生成する
        $tmpProxyOutputDir = sys_get_temp_dir() . '/proxy_' . Str::random(12);
        @mkdir($tmpProxyOutputDir);

        try {
            // dbにプラグイン登録
            $plugin = $this->registerPlugin($config, $event, $source);

            // インストール時には一時的に利用するProxyを生成してからスキーマを更新する
            $generatedFiles = $this->regenerateProxy($plugin, true, $tmpProxyOutputDir);
            $this->schemaService->updateSchema($generatedFiles, $tmpProxyOutputDir);

            ConfigManager::writePluginConfigCache();
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
        $d = ($this->appConfig['plugin_temp_realdir'].'/'.sha1(Str::random(16)));

        if (!mkdir($d, 0777)) {
            throw new PluginException($php_errormsg.$d);
        }

        return $d;
    }

    public function deleteDirs($arr)
    {
        foreach ($arr as $dir) {
            if (file_exists($dir)) {
                $fs = new Filesystem();
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

    public function deleteFile($path)
    {
        $f = new Filesystem();
        $f->remove($path);
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
                ->setEnable(Constant::DISABLED)
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
        ConfigManager::removePluginConfigCache();
        Cache::clear($this->app, false);
        $this->callPluginManagerMethod(Yaml::parse(file_get_contents($pluginDir.'/'.self::CONFIG_YML)), 'disable');
        $this->callPluginManagerMethod(Yaml::parse(file_get_contents($pluginDir.'/'.self::CONFIG_YML)), 'uninstall');
        $this->disable($plugin);
        $this->unregisterPlugin($plugin);
        $this->deleteFile($pluginDir);

        // スキーマを更新する
        $this->schemaService->updateSchema([], $this->appConfig['root_dir'].'/app/proxy/entity');

        ConfigManager::writePluginConfigCache();
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
        if ($temporary || $plugin->getEnable() === Constant::ENABLED) {
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
            Cache::clear($this->app, false);
            $pluginDir = $this->calcPluginDir($plugin->getCode());
            $em->getConnection()->beginTransaction();
            $plugin->setEnable($enable ? Constant::ENABLED : Constant::DISABLED);
            $em->persist($plugin);

            $this->callPluginManagerMethod(Yaml::parse(file_get_contents($pluginDir.'/'.self::CONFIG_YML)), $enable ? 'enable' : 'disable');

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

    public function update(\Eccube\Entity\Plugin $plugin, $path)
    {
        $pluginBaseDir = null;
        $tmp = null;
        try {
            PluginConfigManager::removePluginConfigCache();
            Cache::clear($this->app, false);
            $tmp = $this->createTempDir();

            $this->unpackPluginArchive($path, $tmp); //一旦テンポラリに展開
            $this->checkPluginArchiveContent($tmp);

            $config = $this->readYml($tmp.'/'.self::CONFIG_YML);
            $event = $this->readYml($tmp.'/event.yml');

            if ($plugin->getCode() != $config['code']) {
                throw new PluginException('new/old plugin code is different.');
            }

            $pluginBaseDir = $this->calcPluginDir($config['code']);
            $this->deleteFile($tmp); // テンポラリのファイルを削除

            $this->unpackPluginArchive($path, $pluginBaseDir); // 問題なければ本当のplugindirへ
            $this->updatePlugin($plugin, $config, $event); // dbにプラグイン登録

            PluginConfigManager::writePluginConfigCache();
        } catch (PluginException $e) {
            foreach (array($tmp) as $dir) {
                if (file_exists($dir)) {
                    $fs = new Filesystem();
                    $fs->remove($dir);
                }
            }
            throw $e;
        }

        return true;
    }

    public function updatePlugin(\Eccube\Entity\Plugin $plugin, $meta, $event_yml)
    {
        try {
            $em = $this->entityManager;
            $em->getConnection()->beginTransaction();
            $plugin->setVersion($meta['version'])
                ->setName($meta['name']);

            if (isset($meta['event'])) {
                $plugin->setClassName($meta['event']);
            }

            $rep = $this->pluginEventHandlerRepository;

            if (is_array($event_yml)) {
                foreach ($event_yml as $event => $handlers) {
                    foreach ($handlers as $handler) {
                        if (!$this->checkSymbolName($handler[0])) {
                            throw new PluginException('Handler name format error');
                        }
                        // updateで追加されたハンドラかどうか調べる
                        $peh = $rep->findBy(array(
                            'plugin_id' => $plugin->getId(),
                            'event' => $event,
                            'handler' => $handler[0],
                            'handler_type' => $handler[1],));

                        if (!$peh) { // 新規にevent.ymlに定義されたハンドラなのでinsertする
                            $peh = new \Eccube\Entity\PluginEventHandler();
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

                # アップデート後のevent.ymlで削除されたハンドラをdtb_plugin_event_handlerから探して削除
                foreach ($rep->findBy(array('plugin_id' => $plugin->getId())) as $peh) {
                    if (!isset($event_yml[$peh->getEvent()])) {
                        $em->remove($peh);
                        $em->flush();
                    } else {
                        $match = false;
                        foreach ($event_yml[$peh->getEvent()] as $handler) {
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
     * @param array $arrPlugin
     * @param array $plugin
     * @param array $arrDependency
     * @return array|mixed
     */
    public function getDependency($arrPlugin, $plugin, $arrDependency = array())
    {
        // Prevent infinity loop
        if (empty($arrDependency)) {
            $arrDependency[] = $plugin;
        }

        // Check dependency
        if (!isset($plugin['require']) || empty($plugin['require'])) {
            return $arrDependency;
        }

        $require = $plugin['require'];
        // Check dependency
        foreach ($require as $pluginName => $version) {
            $dependPlugin = $this->buildInfo($arrPlugin, $pluginName);
            // Prevent call self
            if (!$dependPlugin || $dependPlugin['product_code'] == $plugin['product_code']) {
                continue;
            }

            // Check duplicate in dependency
            $index = array_search($dependPlugin['product_code'], array_column($arrDependency, 'product_code'));
            if ($index === false) {
                $arrDependency[] = $dependPlugin;
                // Check child dependency
                $arrDependency = $this->getDependency($arrPlugin, $dependPlugin, $arrDependency);
            }
        }

        return $arrDependency;
    }

    /**
     * Get plugin information
     *
     * @param array  $arrPlugin
     * @param string $pluginCode
     * @return array|null
     */
    public function buildInfo($arrPlugin, $pluginCode)
    {
        $plugin = [];
        $index = $this->checkPluginExist($arrPlugin, $pluginCode);
        if ($index === false) {
            return $plugin;
        }
        // Get target plugin in return of api
        $plugin = $arrPlugin[$index];

        // Check the eccube version that the plugin supports.
        $plugin['is_supported_eccube_version'] = 0;
        if (in_array(Constant::VERSION, $plugin['eccube_version'])) {
            // Match version
            $plugin['is_supported_eccube_version'] = 1;
        }

        $plugin['depend'] = $this->getRequirePluginName($arrPlugin, $plugin);

        return $plugin;
    }

    /**
     * Get dependency name and version only
     *
     * @param array $arrPlugin
     * @param array $plugin
     * @return mixed
     */
    public function getRequirePluginName($arrPlugin, $plugin)
    {
        $depend = [];
        if (isset($plugin['require']) && !empty($plugin['require'])) {
            foreach ($plugin['require'] as $name => $version) {
                $ret = $this->checkPluginExist($arrPlugin, $name);
                if ($ret === false) {
                    continue;
                }
                $depend[] = [
                    'name' => $arrPlugin[$ret]['name'],
                    'version' => $version,
                ];
            }
        }

        return $depend;
    }

    /**
     * Find the dependent plugins that need to be disabled
     *
     * @param string $pluginCode
     * @return array
     */
    public function findDependentPluginNeedDisable($pluginCode)
    {
        /**
         * @var Plugin[] $enabledPlugins
         */
        $enabledPlugins = $this->pluginRepository->findAllEnabled();
        $arrDependent = [];
        foreach ($enabledPlugins as $plugin) {
            if ($plugin->getCode() !== $pluginCode) {
                $dir = $this->appConfig['plugin_realdir'].'/'.$plugin->getCode();
                $jsonText = @file_get_contents($dir.'/composer.json');
                if ($jsonText) {
                    $json = json_decode($jsonText, true);
                    if (!isset($json['require'])) {
                        continue;
                    }
                    if (array_key_exists(self::VENDOR_NAME.'/'.$pluginCode, $json['require'])) {
                        $arrDependent[] = $plugin->getName();
                    }
                }
            }
        }

        return $arrDependent;
    }

    /**
     * @param $arrPlugin
     * @param $pluginCode
     * @return false|int|string
     */
    private function checkPluginExist($arrPlugin, $pluginCode)
    {
        if (strpos($pluginCode, self::VENDOR_NAME.'/') !== false) {
            $pluginCode = str_replace(self::VENDOR_NAME.'/', '', $pluginCode);
        }
        // Find plugin in array
        $index = array_search($pluginCode, array_column($arrPlugin, 'product_code'));

        return $index;
    }
}
