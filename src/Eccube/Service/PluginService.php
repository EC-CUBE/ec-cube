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

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginException;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerServiceInterface;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PluginService
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EntityProxyService
     */
    protected $entityProxyService;

    /**
     * @var SchemaService
     */
    protected $schemaService;

    /**
     * @var ComposerServiceInterface
     */
    protected $composerService;

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
     * @var string %kernel.project_dir%
     */
    private $projectRoot;

    /**
     * @var string %kernel.environment%
     */
    private $environment;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /** @var CacheUtil */
    protected $cacheUtil;

    /**
     * PluginService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PluginRepository $pluginRepository
     * @param EntityProxyService $entityProxyService
     * @param SchemaService $schemaService
     * @param EccubeConfig $eccubeConfig
     * @param ContainerInterface $container
     * @param CacheUtil $cacheUtil
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PluginRepository $pluginRepository,
        EntityProxyService $entityProxyService,
        SchemaService $schemaService,
        EccubeConfig $eccubeConfig,
        ContainerInterface $container,
        CacheUtil $cacheUtil
    ) {
        $this->entityManager = $entityManager;
        $this->pluginRepository = $pluginRepository;
        $this->entityProxyService = $entityProxyService;
        $this->schemaService = $schemaService;
        $this->eccubeConfig = $eccubeConfig;
        $this->projectRoot = $eccubeConfig->get('kernel.project_dir');
        $this->environment = $eccubeConfig->get('kernel.environment');
        $this->container = $container;
        $this->cacheUtil = $cacheUtil;
    }

    /**
     * ファイル指定してのプラグインインストール
     *
     * @param string $path   path to tar.gz/zip plugin file
     * @param int    $source
     *
     * @return boolean
     *
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

            $config = $this->readConfig($tmp);
            // テンポラリのファイルを削除
            $this->deleteFile($tmp);

            // 重複していないかチェック
            $this->checkSamePlugin($config['code']);

            $pluginBaseDir = $this->calcPluginDir($config['code']);
            // 本来の置き場所を作成
            $this->createPluginDir($pluginBaseDir);

            // 問題なければ本当のplugindirへ
            $this->unpackPluginArchive($path, $pluginBaseDir);

            // Check dependent plugin
            // Don't install ec-cube library
//            $dependents = $this->getDependentByCode($config['code'], self::OTHER_LIBRARY);
//            if (!empty($dependents)) {
//                $package = $this->parseToComposerCommand($dependents);
            //FIXME: how to working with ComposerProcessService or ComposerApiService ?
//                $this->composerService->execRequire($package);
//            }

            // プラグイン配置後に実施する処理
            $this->postInstall($config, $source);
            // リソースファイルをコピー
            $this->copyAssets($pluginBaseDir, $config['code']);
        } catch (PluginException $e) {
            $this->deleteDirs([$tmp, $pluginBaseDir]);
            throw $e;
        } catch (\Exception $e) {
            // インストーラがどんなExceptionを上げるかわからないので
            $this->deleteDirs([$tmp, $pluginBaseDir]);
            throw $e;
        }

        return true;
    }

    // インストール事前処理
    public function preInstall()
    {
        // キャッシュの削除
        // FIXME: Please fix clearCache function (because it's clear all cache and this file just upload)
//        $this->cacheUtil->clearCache();
    }

    // インストール事後処理
    public function postInstall($config, $source)
    {
        // Proxyのクラスをロードせずにスキーマを更新するために、
        // インストール時には一時的なディレクトリにProxyを生成する
        $tmpProxyOutputDir = sys_get_temp_dir().'/proxy_'.StringUtil::random(12);
        @mkdir($tmpProxyOutputDir);

        try {
            // dbにプラグイン登録
            $plugin = $this->registerPlugin($config, $source);

            // プラグインmetadata定義を追加
            $entityDir = $this->eccubeConfig['plugin_realdir'].'/'.$plugin->getCode().'/Entity';
            if (file_exists($entityDir)) {
                $ormConfig = $this->entityManager->getConfiguration();
                $chain = $ormConfig->getMetadataDriverImpl();
                $driver = $ormConfig->newDefaultAnnotationDriver([$entityDir], false);
                $namespace = 'Plugin\\'.$config['code'].'\\Entity';
                $chain->addDriver($driver, $namespace);
                $ormConfig->addEntityNamespace($plugin->getCode(), $namespace);
            }

            // インストール時には一時的に利用するProxyを生成してからスキーマを更新する
            $generatedFiles = $this->regenerateProxy($plugin, true, $tmpProxyOutputDir);
            $this->schemaService->updateSchema($generatedFiles, $tmpProxyOutputDir);
        } finally {
            foreach (glob("${tmpProxyOutputDir}/*") as  $f) {
                unlink($f);
            }
            rmdir($tmpProxyOutputDir);
        }
    }

    public function createTempDir()
    {
        $tempDir = $this->projectRoot.'/var/cache/'.$this->environment.'/Plugin';
        @mkdir($tempDir);
        $d = ($tempDir.'/'.sha1(StringUtil::random(16)));

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

    /**
     * @param string $archive
     * @param string $dir
     *
     * @throws PluginException
     */
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
            throw new PluginException(trans('pluginservice.text.error.upload_failure'));
        }
    }

    /**
     * @param $dir
     * @param array $config_cache
     *
     * @throws PluginException
     */
    public function checkPluginArchiveContent($dir, array $config_cache = [])
    {
        try {
            if (!empty($config_cache)) {
                $meta = $config_cache;
            } else {
                $meta = $this->readConfig($dir);
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

    /**
     * @param $pluginDir
     *
     * @return array
     *
     * @throws PluginException
     */
    public function readConfig($pluginDir)
    {
        $composerJsonPath = $pluginDir.DIRECTORY_SEPARATOR.'composer.json';
        if (file_exists($composerJsonPath) === false) {
            throw new PluginException("${composerJsonPath} not found.");
        }

        $json = json_decode(file_get_contents($composerJsonPath), true);
        if ($json === null) {
            throw new PluginException("Invalid json format. [${composerJsonPath}]");
        }

        if (!isset($json['version'])) {
            throw new PluginException("`version` is not defined in ${composerJsonPath}");
        }

        if (!isset($json['extra']['code'])) {
            throw new PluginException("`extra.code` is not defined in ${composerJsonPath}");
        }

        return [
            'code' => $json['extra']['code'],
            'name' => isset($json['description']) ? $json['description'] : $json['extra']['code'],
            'version' => $json['version'],
        ];
    }

    public function checkSymbolName($string)
    {
        return strlen($string) < 256 && preg_match('/^\w+$/', $string);
        // plugin_nameやplugin_codeに使える文字のチェック
        // a-z A-Z 0-9 _
        // ディレクトリ名などに使われれるので厳しめ
    }

    /**
     * @param string $path
     */
    public function deleteFile($path)
    {
        $f = new Filesystem();
        $f->remove($path);
    }

    public function checkSamePlugin($code)
    {
        $repo = $this->pluginRepository->findOneBy(['code' => $code]);
        if ($repo) {
            throw new PluginException('plugin already installed.');
        }
    }

    public function calcPluginDir($name)
    {
        return $this->projectRoot.'/app/Plugin/'.$name;
    }

    /**
     * @param string $d
     *
     * @throws PluginException
     */
    public function createPluginDir($d)
    {
        $b = @mkdir($d);
        if (!$b) {
            throw new PluginException($php_errormsg);
        }
    }

    /**
     * @param $meta
     * @param int $source
     *
     * @return Plugin
     *
     * @throws PluginException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function registerPlugin($meta, $source = 0)
    {
        $em = $this->entityManager;
        $em->getConnection()->beginTransaction();
        try {
            $p = new Plugin();
            // インストール直後はプラグインは有効にしない
            $p->setName($meta['name'])
                ->setEnabled(false)
                ->setVersion($meta['version'])
                ->setSource($source)
                ->setCode($meta['code']);

            $em->persist($p);
            $em->flush();

            $this->callPluginManagerMethod($meta, 'install');

            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw new PluginException($e->getMessage(), $e->getCode(), $e);
        }

        return $p;
    }

    /**
     * @param $meta
     * @param string $method
     */
    public function callPluginManagerMethod($meta, $method)
    {
        $class = '\\Plugin'.'\\'.$meta['code'].'\\'.'PluginManager';
        if (class_exists($class)) {
            $installer = new $class(); // マネージャクラスに所定のメソッドがある場合だけ実行する
            if (method_exists($installer, $method)) {
                // FIXME appを削除.
                $installer->$method($meta, $this->app, $this->container);
            }
        }
    }

    /**
     * @param Plugin $plugin
     * @param bool $force
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function uninstall(Plugin $plugin, $force = true)
    {
        $pluginDir = $this->calcPluginDir($plugin->getCode());
        $this->cacheUtil->clearCache();
        $config = $this->readConfig($pluginDir);
        $this->callPluginManagerMethod($config, 'disable');
        $this->callPluginManagerMethod($config, 'uninstall');
        $this->disable($plugin);
        $this->unregisterPlugin($plugin);

        // スキーマを更新する
        //FIXME: Update schema before no affect
        $this->schemaService->updateSchema([], $this->projectRoot.'/app/proxy/entity');

        // プラグインのネームスペースに含まれるEntityのテーブルを削除する
        $namespace = 'Plugin\\'.$plugin->getCode().'\\Entity';
        $this->schemaService->dropTable($namespace);

        if ($force) {
            $this->deleteFile($pluginDir);
            $this->removeAssets($plugin->getCode());
        }

        return true;
    }

    public function unregisterPlugin(Plugin $p)
    {
        try {
            $em = $this->entityManager;
            $em->remove($p);
            $em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function disable(Plugin $plugin)
    {
        return $this->enable($plugin, false);
    }

    /**
     * Proxyを再生成します.
     *
     * @param Plugin $plugin プラグイン
     * @param boolean $temporary プラグインが無効状態でも一時的に生成するかどうか
     * @param string|null $outputDir 出力先
     *
     * @return array 生成されたファイルのパス
     */
    private function regenerateProxy(Plugin $plugin, $temporary, $outputDir = null)
    {
        if (is_null($outputDir)) {
            $outputDir = $this->projectRoot.'/app/proxy/entity';
        }
        @mkdir($outputDir);

        $enabledPluginCodes = array_map(
            function ($p) { return $p->getCode(); },
            $this->pluginRepository->findAllEnabled()
        );

        $excludes = [];
        if ($temporary || $plugin->isEnabled()) {
            $enabledPluginCodes[] = $plugin->getCode();
        } else {
            $index = array_search($plugin->getCode(), $enabledPluginCodes);
            if ($index >= 0) {
                array_splice($enabledPluginCodes, $index, 1);
                $excludes = [$this->projectRoot.'/app/Plugin/'.$plugin->getCode().'/Entity'];
            }
        }

        $enabledPluginEntityDirs = array_map(function ($code) {
            return $this->projectRoot."/app/Plugin/${code}/Entity";
        }, $enabledPluginCodes);

        return $this->entityProxyService->generate(
            array_merge([$this->projectRoot.'/app/Customize/Entity'], $enabledPluginEntityDirs),
            $excludes,
            $outputDir
        );
    }

    public function enable(Plugin $plugin, $enable = true)
    {
        $em = $this->entityManager;
        try {
            $pluginDir = $this->calcPluginDir($plugin->getCode());
            $config = $this->readConfig($pluginDir);
            $em->getConnection()->beginTransaction();
            $plugin->setEnabled($enable ? true : false);
            $em->persist($plugin);

            $this->callPluginManagerMethod($config, $enable ? 'enable' : 'disable');

            // Proxyだけ再生成してスキーマは更新しない
            $this->regenerateProxy($plugin, false);

            $em->flush();
            $em->getConnection()->commit();
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
     *
     * @return bool
     *
     * @throws PluginException
     * @throws \Exception
     */
    public function update(Plugin $plugin, $path)
    {
        $pluginBaseDir = null;
        $tmp = null;
        try {
            $this->cacheUtil->clearCache();
            $tmp = $this->createTempDir();

            $this->unpackPluginArchive($path, $tmp); //一旦テンポラリに展開
            $this->checkPluginArchiveContent($tmp);

            $config = $this->readConfig($tmp);

            if ($plugin->getCode() != $config['code']) {
                throw new PluginException('new/old plugin code is different.');
            }

            $pluginBaseDir = $this->calcPluginDir($config['code']);
            $this->deleteFile($tmp); // テンポラリのファイルを削除

            $this->unpackPluginArchive($path, $pluginBaseDir); // 問題なければ本当のplugindirへ

            // Check dependent plugin
            // Don't install ec-cube library
            $dependents = $this->getDependentByCode($config['code'], self::OTHER_LIBRARY);
            if (!empty($dependents)) {
                $package = $this->parseToComposerCommand($dependents);
                $this->composerService->execRequire($package);
            }

            $this->updatePlugin($plugin, $config); // dbにプラグイン登録
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
     *
     * @throws \Exception
     */
    public function updatePlugin(Plugin $plugin, $meta)
    {
        $em = $this->entityManager;
        try {
            $em->getConnection()->beginTransaction();
            $plugin->setVersion($meta['version'])
                ->setName($meta['name']);

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
     * Get array require by plugin
     * Todo: need define dependency plugin mechanism
     *
     * @param array $plugin     format as plugin from api
     *
     * @return array|mixed
     */
    public function getPluginRequired($plugin)
    {
        // Check require
        if (!isset($plugin['require']) || empty($plugin['require'])) {
            return [];
        }
//        $require = $plugin['require'];

        $requirePlugins = [];
        // Check require
//        foreach ($require as $pluginName => $version) {
//            $pluginCode = str_replace(self::VENDOR_NAME . '/', '', $pluginName);
//            $pluginCode = Container::camelize($pluginCode);
//            $dependPlugin = $this->buildInfo($plugins, $pluginName);
//            // Prevent call self
//            if (!$dependPlugin || $dependPlugin['product_code'] == $plugin['product_code']) {
//                continue;
//            }
//
//            // Check duplicate in dependency
//            $index = array_search($dependPlugin['product_code'], array_column($dependents, 'product_code'));
//            if ($index === false) {
//                // Update require version
//                $dependPlugin['version'] = $version;
//                $dependents[] = $dependPlugin;
//                // Check child dependency
//                $dependents = $this->getPluginRequired($plugins, $dependPlugin, $dependents);
//            }
//        }

        return $requirePlugins;
    }

    /**
     * Get plugin information
     *
     * @param array $plugin
     *
     * @return array|null
     */
    public function buildInfo($plugin)
    {
        $this->supportedVersion($plugin);
        $plugin['require'] = $this->getRequireOfPlugin($plugin);

        return $plugin;
    }

    /**
     * Check support version
     *
     * @param $plugin
     */
    public function supportedVersion(&$plugin)
    {
        // Check the eccube version that the plugin supports.
        $plugin['version_check'] = false;
        if (in_array(Constant::VERSION, $plugin['supported_versions'])) {
            // Match version
            $plugin['version_check'] = true;
        }
    }

    /**
     * Get require plugin
     *
     * @param array $plugin  target plugin from api
     *
     * @return mixed format [0 => ['name' => pluginName1, 'version' => pluginVersion1], 1 => ['name' => pluginName2, 'version' => pluginVersion2]]
     */
    public function getRequireOfPlugin($plugin)
    {
//        $pluginCode = $plugin['code'];
        // Need dependency Mechanism
        /**
        $pluginDir = $this->calcPluginDir($pluginCode);
        $composerPath = $pluginDir.'/composer.json';
        // read composer.json
        if (!file_exists($composerPath)) {
            return [];
        }
        $content = file_get_contents($composerPath);
        $content = json_decode($content,true);

        $require = [];
        if (isset($content['require']) && !empty($content['require'])) {
            foreach ($content['require'] as $name => $version) {
                $require[] = [
                    'name' => $name,
                    'version' => $version,
                ];
            }
        }
         */

        return [];
    }

    /**
     * Check require plugin in enable
     *
     * @param string $pluginCode
     *
     * @return array plugin code
     */
    public function findRequirePluginNeedEnable($pluginCode)
    {
        $dir = $this->eccubeConfig['plugin_realdir'].'/'.$pluginCode;
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
     *
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
     *
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
         * @var Plugin[]
         */
        $plugins = $this->pluginRepository->matching($criteria);
        $dependents = [];
        foreach ($plugins as $plugin) {
            $dir = $this->eccubeConfig['plugin_realdir'].'/'.$plugin->getCode();
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
     *
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
     *
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
     * @param $pluginCode
     */
    public function copyAssets($pluginBaseDir, $pluginCode)
    {
        $assetsDir = $pluginBaseDir.'/Resource/assets';

        // プラグインにリソースファイルがあれば所定の位置へコピー
        if (file_exists($assetsDir)) {
            $file = new Filesystem();
            $file->mirror($assetsDir, $this->eccubeConfig['plugin_html_realdir'].$pluginCode.'/assets');
        }
    }

    /**
     * コピーしたリソースファイル等を削除
     *
     * @param string $pluginCode
     */
    public function removeAssets($pluginCode)
    {
        $assetsDir = $this->projectRoot.'/app/Plugin/'.$pluginCode;

        // コピーされているリソースファイルがあれば削除
        if (file_exists($assetsDir)) {
            $file = new Filesystem();
            $file->remove($assetsDir);
        }
    }

    /**
     * Is update
     *
     * @param string $pluginVersion
     * @param string $remoteVersion
     *
     * @return boolean
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
     *
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
     * @param string $code
     *
     * @return bool
     */
    private function isEnable($code)
    {
        $Plugin = $this->pluginRepository->findOneBy([
            'enabled' => Constant::ENABLED,
            'code' => $code,
        ]);
        if ($Plugin) {
            return true;
        }

        return false;
    }
}
