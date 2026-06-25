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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Eccube\Doctrine\ORM\Mapping\Driver\NopAttributeDriver;
use Eccube\Doctrine\ORM\Mapping\Driver\ReloadSafeAttributeDriver;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class SchemaService
{
    /**
     * SchemaService constructor.
     */
    public function __construct(protected EntityManagerInterface $entityManager, private readonly PluginContext $pluginContext)
    {
    }

    /**
     * Doctrine Metadata を生成してコールバック関数を実行する.
     *
     * コールバック関数は主に SchemaTool が利用されます.
     * Metadata を出力する一時ディレクトリを指定しない場合は内部で生成し, コールバック関数実行後に削除されます.
     *
     * @param callable $callback Metadata を生成した後に実行されるコールバック関数
     * @param array<mixed> $generatedFiles Proxy ファイルパスの配列
     * @param string $proxiesDirectory Proxy ファイルを格納したディレクトリ
     * @param string $outputDir Metadata の出力先ディレクトリ
     */
    public function executeCallback(callable $callback, array $generatedFiles, string $proxiesDirectory, ?string $outputDir = null): void
    {
        $createOutputDir = false;
        if (is_null($outputDir)) {
            $outputDir = sys_get_temp_dir().'/metadata_'.StringUtil::random(12);
            mkdir($outputDir);
            $createOutputDir = true;
        }

        try {
            $driver = $this->entityManager->getConfiguration()->getMetadataDriverImpl();

            // DoctrineBundleのMappingDriverラッパーをアンラップ
            if ($driver instanceof MappingDriver) {
                $driver = $driver->getDriver();
            }

            if (!$driver instanceof MappingDriverChain) {
                trigger_error('MappingDriverChain のインスタンスが必要です', E_USER_WARNING);

                return;
            }

            $drivers = $driver->getDrivers();
            foreach ($drivers as $namespace => $oldDriver) {
                if ('Eccube\Entity' === $namespace || preg_match('/^Plugin\\\\.*\\\\Entity$/', $namespace)) {
                    // Setup to AttributeDriver
                    if (!$oldDriver instanceof AttributeDriver) {
                        continue;
                    }
                    $newDriver = new ReloadSafeAttributeDriver($oldDriver->getPaths());
                    $newDriver->setFileExtension($oldDriver->getFileExtension());
                    $newDriver->addExcludePaths($oldDriver->getExcludePaths());
                    $newDriver->setTraitProxiesDirectory($proxiesDirectory);
                    $newDriver->setNewProxyFiles($generatedFiles);
                    $newDriver->setOutputDir($outputDir);
                    $driver->addDriver($newDriver, $namespace);
                }

                if ($this->pluginContext->isUninstall()) {
                    foreach ($this->pluginContext->getExtraEntityNamespaces() as $extraEntityNamespace) {
                        if ($extraEntityNamespace === $namespace) {
                            $driver->addDriver(new NopAttributeDriver([]), $namespace);
                        }
                    }
                }
            }

            $tool = new SchemaTool($this->entityManager);
            $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();

            call_user_func($callback, $tool, $metaData);
        } finally {
            if ($createOutputDir) {
                $files = Finder::create()
                    ->in($outputDir)
                    ->files();
                $f = new Filesystem();
                $f->remove($files);
            }
        }
    }

    /**
     * Doctrine Metadata を生成して UpdateSchema を実行する.
     *
     * @param array<mixed> $generatedFiles Proxy ファイルパスの配列
     * @param string $proxiesDirectory Proxy ファイルを格納したディレクトリ
     * @param bool $saveMode UpdateSchema を即時実行する場合 true
     */
    public function updateSchema(array $generatedFiles, string $proxiesDirectory, bool $saveMode = false): void
    {
        $this->executeCallback(function (SchemaTool $tool, array $metaData) use ($saveMode): void {
            if (!$saveMode) {
                $tool->updateSchema($metaData);

                return;
            }

            // ORM 3 で SchemaTool::updateSchema() の $saveMode 引数 (追加系のみ適用) が廃止されたため,
            // getUpdateSchemaSql() の結果から破壊的な文 (DROP TABLE / DROP COLUMN / DROP CONSTRAINT 等) を
            // 除外して追加系のみ適用し, 従来の saveMode 相当の挙動を再現する.
            // プラグインの Entity テーブル削除は uninstall 時に dropTable() で明示的に行うため,
            // ここで他テーブルを巻き込んで DROP してはならない (例: 依存プラグインの FK が残る
            // テーブルを DROP しようとして "cannot drop ... because other objects depend on it" になる).
            $connection = $this->entityManager->getConnection();
            foreach ($tool->getUpdateSchemaSql($metaData) as $sql) {
                if ($this->isDestructiveSql($sql)) {
                    continue;
                }
                $connection->executeStatement($sql);
            }
        }, $generatedFiles, $proxiesDirectory);
    }

    /**
     * スキーマ更新 SQL がテーブル・カラム・制約などを削除する破壊的な文かどうかを判定する.
     */
    private function isDestructiveSql(string $sql): bool
    {
        return (bool) preg_match('/\bDROP\s+(TABLE|SEQUENCE|INDEX|CONSTRAINT|FOREIGN\s+KEY)\b/i', $sql)
            || (bool) preg_match('/\bALTER\s+TABLE\b.*\bDROP\b/i', $sql);
    }

    /**
     * ネームスペースに含まれるEntityのテーブルを削除する
     *
     * @param  string $targetNamespace 削除対象のネームスペース
     */
    public function dropTable(string $targetNamespace): void
    {
        $driver = $this->entityManager->getConfiguration()->getMetadataDriverImpl();

        // DoctrineBundleのMappingDriverラッパーをアンラップ
        if ($driver instanceof MappingDriver) {
            $driver = $driver->getDriver();
        }

        if (!$driver instanceof MappingDriverChain) {
            trigger_error('MappingDriverChain のインスタンスが必要です', E_USER_WARNING);

            return;
        }

        $drivers = $driver->getDrivers();

        $dropMetas = [];
        foreach ($drivers as $namespace => $currentDriver) {
            if ($targetNamespace === $namespace) {
                $allClassNames = $currentDriver->getAllClassNames();

                foreach ($allClassNames as $className) {
                    $dropMetas[] = $this->entityManager->getMetadataFactory()->getMetadataFor($className);
                }
            }
        }
        $tool = new SchemaTool($this->entityManager);
        $tool->dropSchema($dropMetas);
    }
}
