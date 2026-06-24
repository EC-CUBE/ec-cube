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

namespace Eccube\Service;

use Doctrine\Bundle\DoctrineBundle\Mapping\MappingDriver;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Persistence\Mapping\MappingException as PersistenceMappingException;
use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Doctrine\ORM\Mapping\Driver\TraitProxyAttributeDriver;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginException;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerServiceInterface;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PluginService
{
    public const VENDOR_NAME = 'ec-cube';

    /**
     * Plugin type/library of ec-cube
     */
    public const ECCUBE_LIBRARY = 1;

    /**
     * Plugin type/library of other (except ec-cube)
     */
    public const OTHER_LIBRARY = 2;

    /**
     * @var string %kernel.project_dir%
     */
    private readonly string $projectRoot;

    /**
     * @var string %kernel.environment%
     */
    private readonly string $environment;

    /**
     * PluginService constructor.
     */
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected PluginRepository $pluginRepository,
        protected EntityProxyService $entityProxyService,
        protected SchemaService $schemaService,
        protected EccubeConfig $eccubeConfig,
        protected ContainerInterface $container,
        protected CacheUtil $cacheUtil,
        protected ComposerServiceInterface $composerService,
        private readonly PluginApiService $pluginApiService,
        private readonly PluginContext $pluginContext,
    ) {
        $this->projectRoot = $this->eccubeConfig->get('kernel.project_dir');
        $this->environment = $this->eccubeConfig->get('kernel.environment');
    }

    /**
     * ファイル指定してのプラグインインストール
     *
     * @param string $path path to tar.gz/zip plugin file
     *
     * @throws PluginException
     * @throws \Exception
     */
    public function install(string $path, int $source = 0, bool $notExists = false): bool
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

            // リソースファイルをコピー
            $this->copyAssets($config['code']);
            // プラグイン配置後に実施する処理
            $this->postInstall($config, $source);
        } catch (PluginException $e) {
            $this->deleteDirs([$tmp, $pluginBaseDir]);
            if ($e->getMessage() === 'plugin already installed.' && $notExists) {
                return true;
            }

            throw $e;
        } catch (\Exception $e) {
            // インストーラがどんなExceptionを上げるかわからないので
            $this->deleteDirs([$tmp, $pluginBaseDir]);
            throw $e;
        }

        return true;
    }

    /**
     * @param string $code プラグインコード
     *
     * @throws ConnectionException
     * @throws Exception
     * @throws PluginException
     */
    public function installWithCode(string $code, mixed $notExists = false): bool
    {
        $this->pluginContext->setCode($code);
        $this->pluginContext->setInstall();

        $pluginDir = $this->calcPluginDir($code);
        $this->checkPluginArchiveContent($pluginDir);
        $config = $this->readConfig($pluginDir);

        if (isset($config['source']) && $config['source']) {
            // 依存プラグインが有効になっていない場合はエラー
            $requires = $this->getPluginRequired($config);
            $notInstalledOrDisabled = array_filter($requires, function ($req) {
                $code = preg_replace('/^ec-cube\//i', '', (string) $req['name']);
                /** @var Plugin|null $DependPlugin */
                $DependPlugin = $this->pluginRepository->findByCode($code);

                return $DependPlugin ? $DependPlugin->isEnabled() == false : true;
            });

            if (!empty($notInstalledOrDisabled)) {
                $names = array_map(fn ($p) => $p['name'], $notInstalledOrDisabled);
                throw new PluginException(implode(', ', $names).'を有効化してください。');
            }
        }

        try {
            $this->checkSamePlugin($config['code']);
            $this->copyAssets($config['code']);
            $this->postInstall($config, $config['source']);
        } catch (PluginException $e) {
            if ($e->getMessage() === 'plugin already installed.' && $notExists) {
                return true;
            }

            throw $e;
        }

        return true;
    }

    // インストール事前処理
    public function preInstall(): void
    {
        // キャッシュの削除
        // FIXME: Please fix clearCache function (because it's clear all cache and this file just upload)
        //        $this->cacheUtil->clearCache();
    }

    /**
     * @param array<string, string|int> $config
     *
     * @throws PluginException
     * @throws ConnectionException
     * @throws Exception
     */
    public function postInstall(array $config, string|int $source): void
    {
        try {
            /** @var Plugin|null $Plugin */
            $Plugin = $this->pluginRepository->findByCode($config['code']);

            if (!$Plugin) {
                $Plugin = new Plugin();
                // インストール直後はプラグインは有効にしない
                $Plugin->setName($config['name'])
                    ->setEnabled(false)
                    ->setVersion($config['version'])
                    ->setSource($source)
                    ->setCode($config['code']);
                $this->entityManager->persist($Plugin);
                $this->entityManager->flush();
            }

            $this->generateProxyAndUpdateSchema($Plugin, $config);

            $this->callPluginManagerMethod($config, 'install');

            $Plugin->setInitialized(true);
            $this->entityManager->persist($Plugin);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new PluginException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * プラグインの Proxy ファイルを生成して UpdateSchema を実行する.
     *
     * @param Plugin $plugin プラグインオブジェクト
     * @param array<string, string|int> $config プラグインの composer.json の配列
     * @param bool $uninstall アンインストールする場合は true
     * @param bool $saveMode SQL を即時実行する場合は true
     */
    public function generateProxyAndUpdateSchema(Plugin $plugin, array $config, bool $uninstall = false, bool $saveMode = true): void
    {
        $conn = $this->entityManager->getConnection();
        $this->generateProxyAndCallback(function ($generatedFiles, $proxiesDirectory) use ($saveMode, $conn): void {
            $this->executeDdlWithMySqlWorkaround($conn, function () use ($generatedFiles, $proxiesDirectory, $saveMode): void {
                $this->schemaService->updateSchema($generatedFiles, $proxiesDirectory, $saveMode);
            });
        }, $plugin, $config, $uninstall);
    }

    /**
     * MySQL では DDL が暗黙的に COMMIT を発行し SAVEPOINT を破壊するため,
     * DDL 実行前後でトランザクションのネストレベルを退避・復元する.
     */
    private function executeDdlWithMySqlWorkaround(Connection $conn, callable $ddlCallback): void
    {
        if (!$conn->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            $ddlCallback();

            return;
        }

        $autoCommit = $conn->isAutoCommit();
        $nestingLevel = $conn->getTransactionNestingLevel();

        // MySQL の DDL は暗黙的に COMMIT を発行するため, 既存のトランザクションを
        // すべて COMMIT してからDDLを実行する.
        for ($i = 0; $i < $nestingLevel; $i++) {
            $conn->commit();
        }

        // autoCommit=false のままだと DDL 実行時に DBAL が遅延でトランザクションを
        // 開始し, MySQL の暗黙 COMMIT とずれてしまうため, 一時的に autoCommit=true にする.
        $conn->setAutoCommit(true);
        try {
            $ddlCallback();
        } finally {
            $conn->setAutoCommit($autoCommit);
            // DDL 実行前のネストレベルまでトランザクションを開始し直す.
            for ($i = 0; $i < $nestingLevel; $i++) {
                $conn->beginTransaction();
            }
        }
    }

    /**
     * プラグインの Proxy ファイルを生成してコールバック関数を実行する.
     *
     * コールバック関数は主に SchemaTool が利用されます.
     * Proxy ファイルを出力する一時ディレクトリを指定しない場合は内部で生成し, コールバック関数実行後に削除されます.
     *
     * @param callable $callback Proxy ファイルを生成した後に実行されるコールバック関数
     * @param Plugin $plugin プラグインオブジェクト
     * @param array<string, int|string> $config プラグインの composer.json の配列
     * @param bool $uninstall アンインストールする場合は true
     * @param string $tmpProxyOutputDir Proxy ファイルを出力する一時ディレクトリ
     */
    public function generateProxyAndCallback(callable $callback, Plugin $plugin, array $config, bool $uninstall = false, ?string $tmpProxyOutputDir = null): void
    {
        if ($plugin->isEnabled()) {
            $generatedFiles = $this->regenerateProxy($plugin, false, $tmpProxyOutputDir ?: $this->projectRoot.'/app/proxy/entity');

            call_user_func($callback, $generatedFiles, $tmpProxyOutputDir ?: $this->projectRoot.'/app/proxy/entity');
        } else {
            // Proxyのクラスをロードせずにスキーマを更新するために、
            // インストール時には一時的なディレクトリにProxyを生成する
            $createOutputDir = false;
            if (is_null($tmpProxyOutputDir)) {
                $tmpProxyOutputDir = sys_get_temp_dir().'/proxy_'.StringUtil::random(12);
                @mkdir($tmpProxyOutputDir);
                $createOutputDir = true;
            }

            try {
                if (!$uninstall) {
                    // プラグインmetadata定義を追加
                    $entityDir = $this->eccubeConfig['plugin_realdir'].'/'.$plugin->getCode().'/Entity';
                    if (file_exists($entityDir)) {
                        $ormConfig = $this->entityManager->getConfiguration();
                        $driver = $ormConfig->getMetadataDriverImpl();

                        // DoctrineBundleのMappingDriverラッパーをアンラップ
                        if ($driver instanceof MappingDriver) {
                            $driver = $driver->getDriver();
                        }

                        if ($driver instanceof MappingDriverChain) {
                            $namespace = 'Plugin\\'.$config['code'].'\\Entity';
                            // 既存のドライバーを取得または新しく作成
                            $drivers = $driver->getDrivers();
                            if (!isset($drivers[$namespace])) {
                                $attributeDriver = new TraitProxyAttributeDriver([$entityDir]);
                                $attributeDriver->setTraitProxiesDirectory($this->projectRoot.'/app/proxy/entity');
                                $driver->addDriver($attributeDriver, $namespace);
                            }
                        }
                    }
                }

                // 一時的に利用するProxyを生成してからスキーマを更新する
                $generatedFiles = $this->regenerateProxy($plugin, true, $tmpProxyOutputDir, $uninstall);

                call_user_func($callback, $generatedFiles, $tmpProxyOutputDir);
            } finally {
                if ($createOutputDir) {
                    $files = Finder::create()
                        ->in($tmpProxyOutputDir)
                        ->files();
                    $f = new Filesystem();
                    $f->remove($files);
                }
            }
        }
    }

    /**
     * @throws PluginException
     */
    public function createTempDir(): string
    {
        $tempDir = $this->projectRoot.'/var/cache/'.$this->environment.'/Plugin';
        @mkdir($tempDir);
        $d = ($tempDir.'/'.sha1(StringUtil::random(16)));

        if (!mkdir($d, 0777)) {
            throw new PluginException(trans('admin.store.plugin.mkdir.error', ['%dir_name%' => $d]));
        }

        return $d;
    }

    /**
     * @param array<int, string> $arr
     */
    public function deleteDirs(array $arr): void
    {
        foreach ($arr as $dir) {
            if (file_exists($dir)) {
                $fs = new Filesystem();
                $fs->remove($dir);
            }
        }
    }

    /**
     * @throws PluginException
     */
    public function unpackPluginArchive(string $archive, string $dir): void
    {
        $extension = pathinfo($archive, PATHINFO_EXTENSION);
        try {
            if ($extension == 'zip') {
                $zip = new \ZipArchive();
                $result = $zip->open($archive);
                if ($result !== true) {
                    throw new PluginException(trans('pluginservice.text.error.upload_failure'));
                }

                // ZIP Slip 対策: 展開先パスの検証
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    if (str_contains($entryName, '..')
                        || str_contains($entryName, ':')
                        || str_starts_with($entryName, '/')
                        || str_starts_with($entryName, '\\')
                    ) {
                        $zip->close();
                        throw new PluginException(trans('pluginservice.text.error.upload_failure'));
                    }
                }

                $zip->extractTo($dir);
                $zip->close();
            } else {
                $phar = new \PharData($archive);
                $phar->extractTo($dir, null, true);
            }
        } catch (PluginException $e) {
            throw $e;
        } catch (\Exception) {
            throw new PluginException(trans('pluginservice.text.error.upload_failure'));
        }
    }

    /**
     * @param array<string, string|int> $config_cache
     *
     * @throws PluginException
     */
    public function checkPluginArchiveContent(string $dir, array $config_cache = []): void
    {
        if (!empty($config_cache)) {
            $meta = $config_cache;
        } else {
            $meta = $this->readConfig($dir);
        }

        if (!isset($meta['code']) || !$this->checkSymbolName($meta['code'])) {
            throw new PluginException('composer.json code empty or invalid_character(\W)');
        }
        if (!isset($meta['name'])) {
            // nameは直接クラス名やPATHに使われるわけではないため文字のチェックはなし
            throw new PluginException('composer.json name empty');
        }
        if (!isset($meta['version'])) {
            // versionは直接クラス名やPATHに使われるわけではないため文字のチェックはなし
            throw new PluginException('composer.json version invalid_character(\W) ');
        }
    }

    /**
     * @return array<string, string|int>
     *
     * @throws PluginException
     */
    public function readConfig(string $pluginDir): array
    {
        $composerJsonPath = $pluginDir.DIRECTORY_SEPARATOR.'composer.json';
        if (file_exists($composerJsonPath) === false) {
            throw new PluginException("{$composerJsonPath} not found.");
        }

        $json = json_decode(file_get_contents($composerJsonPath), true);
        if ($json === null) {
            throw new PluginException("Invalid json format. [{$composerJsonPath}]");
        }

        if (!isset($json['version'])) {
            throw new PluginException("`version` is not defined in {$composerJsonPath}");
        }

        if (!isset($json['extra']['code'])) {
            throw new PluginException("`extra.code` is not defined in {$composerJsonPath}");
        }

        return [
            'code' => $json['extra']['code'],
            'name' => $json['description'] ?? $json['extra']['code'],
            'version' => $json['version'],
            'source' => $json['extra']['id'] ?? 0,
        ];
    }

    public function checkSymbolName(string $string): bool
    {
        return strlen($string) < 256 && preg_match('/^\w+$/', $string);
        // plugin_nameやplugin_codeに使える文字のチェック
        // a-z A-Z 0-9 _
        // ディレクトリ名などに使われれるので厳しめ
    }

    public function deleteFile(string $path): void
    {
        $f = new Filesystem();
        $f->remove($path);
    }

    /**
     * @throws PluginException
     */
    public function checkSamePlugin(string $code): void
    {
        /** @var Plugin|null $Plugin */
        $Plugin = $this->pluginRepository->findOneBy(['code' => $code]);
        if ($Plugin && $Plugin->isInitialized()) {
            throw new PluginException('plugin already installed.');
        }
    }

    public function calcPluginDir(string $code): string
    {
        return $this->projectRoot.'/app/Plugin/'.$code;
    }

    /**
     * @throws PluginException
     */
    public function createPluginDir(string $d): void
    {
        $b = @mkdir($d);
        if (!$b) {
            throw new PluginException(trans('admin.store.plugin.mkdir.error', ['%dir_name%' => $d]));
        }
    }

    /**
     * @param array<string, string|int> $meta
     *
     * @throws PluginException
     */
    public function registerPlugin(array $meta, int $source = 0): Plugin
    {
        try {
            $p = new Plugin();
            // インストール直後はプラグインは有効にしない
            $p->setName($meta['name'])
                ->setEnabled(false)
                ->setVersion($meta['version'])
                ->setSource((string) $source)
                ->setCode($meta['code']);

            $this->entityManager->persist($p);
            $this->entityManager->flush();

            $this->pluginApiService->pluginInstalled($p);
        } catch (\Exception $e) {
            throw new PluginException($e->getMessage(), $e->getCode(), $e);
        }

        return $p;
    }

    /**
     * @param array<string, string|int> $meta
     */
    public function callPluginManagerMethod(array $meta, string $method): void
    {
        $class = '\\Plugin\\'.$meta['code'].'\\PluginManager';
        if (class_exists($class)) {
            $installer = new $class(); // マネージャクラスに所定のメソッドがある場合だけ実行する
            if (method_exists($installer, $method)) {
                $installer->$method($meta, $this->container);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function uninstall(Plugin $plugin, bool $force = true): bool
    {
        $pluginDir = $this->calcPluginDir($plugin->getCode());
        $this->cacheUtil->clearCache();
        $config = $this->readConfig($pluginDir);

        if ($plugin->isEnabled()) {
            $this->disable($plugin);
        }

        // 初期化されていない場合はPluginManager#uninstall()は実行しない
        if ($plugin->isInitialized()) {
            $this->callPluginManagerMethod($config, 'uninstall');
        }
        $this->unregisterPlugin($plugin);

        try {
            $conn = $this->entityManager->getConnection();
            $this->executeDdlWithMySqlWorkaround($conn, function () use ($plugin, $config): void {
                // スキーマを更新する
                $this->generateProxyAndUpdateSchema($plugin, $config, true);

                // プラグインのネームスペースに含まれるEntityのテーブルを削除する
                $namespace = 'Plugin\\'.$plugin->getCode().'\\Entity';
                $this->schemaService->dropTable($namespace);
            });
        } catch (PersistenceMappingException) {
            // XXX 削除された Bundle が MappingException をスローする場合があるが実害は無いので無視して進める
        }

        if ($force) {
            $this->deleteFile($pluginDir);
            $this->removeAssets($plugin->getCode());
        }
        $this->pluginApiService->pluginUninstalled($plugin);

        return true;
    }

    /**
     * @throws \Exception
     */
    public function unregisterPlugin(Plugin $p): void
    {
        $em = $this->entityManager;
        $em->remove($p);
        $em->flush();
    }

    /**
     * @throws \Exception
     */
    public function disable(Plugin $plugin): true
    {
        return $this->enable($plugin, false);
    }

    /**
     * Proxyを再生成します.
     *
     * @param Plugin $plugin プラグイン
     * @param bool $temporary プラグインが無効状態でも一時的に生成するかどうか
     * @param string|null $outputDir 出力先
     * @param bool $uninstall プラグイン削除の場合はtrue
     *
     * @return array<int, string> 生成されたファイルのパス
     */
    private function regenerateProxy(Plugin $plugin, bool $temporary, ?string $outputDir = null, bool $uninstall = false): array
    {
        if (is_null($outputDir)) {
            $outputDir = $this->projectRoot.'/app/proxy/entity';
        }
        @mkdir($outputDir);

        if ($temporary) {
            $Plugins = $this->pluginRepository->findAll();
        } else {
            $Plugins = $this->pluginRepository->findAllEnabled();
        }
        /** @var Plugin[] $Plugins */
        $enabledPluginCodes = array_map(fn ($p) => $p->getCode(),
            $Plugins
        );

        $excludes = [];
        if (!$uninstall && ($temporary || $plugin->isEnabled())) {
            $enabledPluginCodes[] = $plugin->getCode();
        } else {
            $index = array_search($plugin->getCode(), $enabledPluginCodes);
            if ($index !== false && $index >= 0) {
                array_splice($enabledPluginCodes, $index, 1);
                $excludes = [$this->projectRoot.'/app/Plugin/'.$plugin->getCode().'/Entity'];
            }
        }

        $enabledPluginEntityDirs = array_map(fn ($code) => $this->projectRoot."/app/Plugin/{$code}/Entity", $enabledPluginCodes);

        return $this->entityProxyService->generate(
            array_merge([$this->projectRoot.'/app/Customize/Entity'], $enabledPluginEntityDirs),
            $excludes,
            $outputDir
        );
    }

    /**
     * @throws Exception
     * @throws PluginException
     */
    public function enable(Plugin $plugin, bool $enable = true): true
    {
        $em = $this->entityManager;
        try {
            $pluginDir = $this->calcPluginDir($plugin->getCode());
            $config = $this->readConfig($pluginDir);
            $em->getConnection()->beginTransaction();

            $this->callPluginManagerMethod($config, $enable ? 'enable' : 'disable');

            $plugin->setEnabled($enable ? true : false);
            $em->persist($plugin);

            // Proxyだけ再生成してスキーマは更新しない
            $this->regenerateProxy($plugin, false);

            $em->flush();
            $em->getConnection()->commit();

            if ($enable) {
                $this->pluginApiService->pluginEnabled($plugin);
            } else {
                $this->pluginApiService->pluginDisabled($plugin);
            }
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * Update plugin
     *
     * @throws PluginException
     * @throws \Exception
     */
    public function update(Plugin $plugin, string $path): bool
    {
        $tmp = null;
        try {
            $this->cacheUtil->clearCache();
            $tmp = $this->createTempDir();

            $this->unpackPluginArchive($path, $tmp); // 一旦テンポラリに展開
            $this->checkPluginArchiveContent($tmp);

            $config = $this->readConfig($tmp);

            if ($plugin->getCode() != $config['code']) {
                throw new PluginException('new/old plugin code is different.');
            }

            $pluginBaseDir = $this->calcPluginDir($config['code']);
            $this->deleteFile($tmp); // テンポラリのファイルを削除
            $this->unpackPluginArchive($path, $pluginBaseDir); // 問題なければ本当のplugindirへ

            $this->copyAssets($plugin->getCode());
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
     * @param array<string, string|int>  $meta     Config data
     *
     * @throws \Exception
     */
    public function updatePlugin(Plugin $plugin, array $meta): void
    {
        $em = $this->entityManager;
        try {
            $em->getConnection()->beginTransaction();
            $plugin->setVersion($meta['version'])
                ->setName($meta['name']);

            $em->persist($plugin);

            $conn = $em->getConnection();
            $this->executeDdlWithMySqlWorkaround($conn, function () use ($plugin, $meta): void {
                $this->generateProxyAndUpdateSchema($plugin, $meta);
            });

            if ($plugin->isInitialized()) {
                $this->callPluginManagerMethod($meta, 'update');
            }
            $this->copyAssets($plugin->getCode());
            $em->flush();

            /** @var \PDO $nativeConnection */
            $nativeConnection = $em->getConnection()->getNativeConnection();
            if ($nativeConnection->inTransaction()) {
                $em->getConnection()->commit();
            }
        } catch (\Exception $e) {
            /** @var \PDO $nativeConnection */
            $nativeConnection = $em->getConnection()->getNativeConnection();
            if ($nativeConnection->inTransaction()) {
                if ($em->getConnection()->isRollbackOnly()) {
                    $em->getConnection()->rollback();
                }
            }
            throw $e;
        }
    }

    /**
     * Get array require by plugin
     * Todo: need define dependency plugin mechanism
     *
     * @param array<string, string|int>|Plugin $plugin format as plugin from api
     *
     * @return array<int, string>|array<mixed>
     *
     * @throws PluginException
     */
    public function getPluginRequired(array|Plugin $plugin): array
    {
        $pluginCode = $plugin instanceof Plugin ? $plugin->getCode() : $plugin['code'];
        $pluginVersion = $plugin instanceof Plugin ? $plugin->getVersion() : $plugin['version'];

        $results = [];

        $this->composerService->foreachRequires('ec-cube/'.strtolower((string) $pluginCode), $pluginVersion, function ($package) use (&$results): void {
            $results[] = $package;
        }, 'eccube-plugin');

        return $results;
    }

    /**
     * Find the dependent plugins that need to be disabled
     *
     * @return array<int, string> plugin code
     */
    public function findDependentPluginNeedDisable(string $pluginCode): array
    {
        return $this->findDependentPlugin($pluginCode, true);
    }

    /**
     * Find the other plugin that has requires on it.
     * Check in both dtb_plugin table and <PluginCode>/composer.json
     *
     * @return array<int, string> plugin code
     */
    public function findDependentPlugin(string $pluginCode, bool $enableOnly = false): array
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('code', $pluginCode));
        if ($enableOnly) {
            $criteria->andWhere(Criteria::expr()->eq('enabled', Constant::ENABLED));
        }

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
                if (array_key_exists(self::VENDOR_NAME.'/'.$pluginCode, $json['require']) // 前方互換用
                    || array_key_exists(self::VENDOR_NAME.'/'.strtolower($pluginCode), $json['require'])) {
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
     * @param int|null $libraryType
     *                      self::ECCUBE_LIBRARY only return library/plugin of eccube
     *                      self::OTHER_LIBRARY only return library/plugin of 3rd part ex: symfony, composer, ...
     *                      default : return all library/plugin
     *
     * @return array<string, string> format [packageName1 => version1, packageName2 => version2]
     */
    public function getDependentByCode(string $pluginCode, ?int $libraryType = null): array
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
            $dependents = match ($libraryType) {
                self::ECCUBE_LIBRARY => array_intersect_key($require, array_flip(preg_grep('/^'.self::VENDOR_NAME.'\//i', array_keys($require)))),
                self::OTHER_LIBRARY => array_intersect_key($require, array_flip(preg_grep('/^'.self::VENDOR_NAME.'\//i', array_keys($require), PREG_GREP_INVERT))),
                default => $json['require'],
            };
        }

        return $dependents;
    }

    /**
     * Format array dependent plugin to string
     * It is used for commands.
     *
     * @param array<string, string> $packages   format [packageName1 => version1, packageName2 => version2]
     *
     * @return string format if version=true: "packageName1:version1 packageName2:version2", if version=false: "packageName1 packageName2"
     */
    public function parseToComposerCommand(array $packages, bool $getVersion = true): string
    {
        $result = array_keys($packages);
        if ($getVersion) {
            $result = array_map(fn ($package, $version) => $package.':'.$version, array_keys($packages), array_values($packages));
        }

        return implode(' ', $result);
    }

    /**
     * リソースファイル等をコピー
     * コピー元となるファイルの置き場所は固定であり、
     * [プラグインコード]/Resource/assets
     * 配下に置かれているファイルが所定の位置へコピーされる
     */
    public function copyAssets(string $pluginCode): void
    {
        $assetsDir = $this->calcPluginDir($pluginCode).'/Resource/assets';

        // プラグインにリソースファイルがあれば所定の位置へコピー
        if (file_exists($assetsDir)) {
            $file = new Filesystem();
            $file->mirror($assetsDir, $this->eccubeConfig['plugin_html_realdir'].$pluginCode.'/assets');
        }
    }

    /**
     * コピーしたリソースファイル等を削除
     */
    public function removeAssets(string $pluginCode): void
    {
        $assetsDir = $this->eccubeConfig['plugin_html_realdir'].$pluginCode;

        // コピーされているリソースファイルがあれば削除
        if (file_exists($assetsDir)) {
            $file = new Filesystem();
            $file->remove($assetsDir);
        }
    }

    /**
     * Plugin is exist check
     *
     * @param array<int, array<string, mixed>> $plugins get from api（各行に product_code を含む）
     *
     * @return false|int|string
     */
    public function checkPluginExist(array $plugins, string $pluginCode): false|int|string
    {
        if (str_contains($pluginCode, self::VENDOR_NAME.'/')) {
            $pluginCode = str_replace(self::VENDOR_NAME.'/', '', $pluginCode);
        }
        $productCodes = array_column($plugins, 'product_code');
        foreach ($productCodes as $index => $code) {
            if ($code === $pluginCode) {
                return $index;
            }
        }
        $lowerPluginCode = strtolower($pluginCode);
        foreach ($productCodes as $index => $code) {
            if ($code === $lowerPluginCode) {
                return $index;
            }
        }

        return false;
    }
}
