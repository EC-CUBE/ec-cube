<?php

declare(strict_types=1);

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

use Eccube\Rector\CodingStyle\AttributeArgumentsOrderRector;
use Eccube\Rector\CodingStyle\NormalizePhpDocArrayGenericSpacingRector;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\Doctrine\Bundle210\Rector\Class_\EventSubscriberInterfaceToAttributeRector;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\CodeQuality\Rector\Class_\ControllerMethodInjectionToConstructorRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony34\Rector\Closure\ContainerGetNameToTypeInTestsRector;
use Rector\Symfony\Symfony61\Rector\Class_\CommandConfigureToAttributeRector;
use Rector\Symfony\Symfony61\Rector\Class_\CommandPropertyToAttributeRector;
use Rector\ValueObject\PhpVersion;

// この設定ファイルは Rector の CLI 実行専用。
// 公開ディレクトリに配置された場合に Web 経由で実行されないようガードする。
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

return RectorConfig::configure()
           // EC-CUBEのPHPバージョンに合わせて設定
           ->withPhpVersion(PhpVersion::PHP_83)

           // Rectorが解析するパスを指定
           ->withPaths([
               __DIR__.'/src',
               // __DIR__ . '/app',
               __DIR__.'/tests',
               __DIR__.'/codeception',
               // プラグインディレクトリ等、個別案件の場合は必要に応じて追加
               // __DIR__ . '/app/Plugin',
           ])
           // スキップするパスやルールを指定
           ->withSkip([
               // 特定のファイルやディレクトリを除外する場合
               __DIR__ . '/rector',
               // Codeception 自動生成ファイル (codecept build で再生成されるため Rector の指摘は意味なし)
               __DIR__ . '/codeception/_support/_generated',
               // 特定のルールを除外する場合
               // 親の $entityManager 再宣言と step5 の接続専用 EM の取り違えを防ぐため
               ControllerMethodInjectionToConstructorRector::class => [
                   __DIR__.'/src/Eccube/Controller/Install/InstallController.php',
               ],
               // Codeception の grabMultiple() 等は戻り値の要素が null になり得るため、
               // NullToStrictStringFuncCallArgRector が追加する (string) キャストを
               // RecastingRemovalRector が除去しないようにスキップする
               // (ローカル/CI 間でのルール適用揺らぎ対策)
               RecastingRemovalRector::class => [
                   __DIR__.'/codeception/_support/Page/Admin/CustomerManagePage.php',
                   __DIR__.'/codeception/_support/Page/Admin/OrderManagePage.php',
                   __DIR__.'/codeception/acceptance/EF06OtherCest.php',
               ],
               // shopping/order の各購入フローは同じ PurchaseFlow 型の別サービスであり、
               // 型解決(get(PurchaseFlow::class))に置き換えると両者の区別が失われテストが無意味化する
               ContainerGetNameToTypeInTestsRector::class => [
                   __DIR__.'/tests/Eccube/Tests/Service/PurchaseFlow/OrderMemoFlowTest.php',
               ],
               // 8.3以上で対応可能
               AddTypeToConstRector::class, // [BC]定数に型を追加する PHP 8.3 以降で有効
               RenameMethodRector::class, //addがaddCommandに変換されてしまうため一旦スキップ
           ])
           // 個別にルールを追加する場合はここに記述
           ->withRules([
               CommandConfigureToAttributeRector::class, // Symfonyコマンドのconfigureメソッドをアトリビュートに変換する
               CommandPropertyToAttributeRector::class, // Symfonyコマンドのプロパティをアトリビュートに変換する,
               StaticDataProviderClassMethodRector::class, // PHPUnitのデータプロバイダを静的メソッドに変換する
               EventSubscriberInterfaceToAttributeRector::class, // Doctrine EventSubscriberをAsDoctrineListenerアトリビュートに変換する
               AttributeArgumentsOrderRector::class, // すべての Attribute の引数をコンストラクタ引数順序に統一する
               NormalizePhpDocArrayGenericSpacingRector::class, // PHPDoc の配列ジェネリクス表記のカンマ後のスペースを統一する
               // $container->get('service.id') の文字列サービスIDを get(Type::class) へ変換する。
               // クラス名のエイリアスを持たない private サービス (例: 'doctrine.debug_data_holder',
               // 'event_dispatcher', 'twig', 'session.factory') では ServiceNotFoundException に
               // なるため, それらはサービスIDを変数へ代入して get($serviceId) の形にし変換対象から
               // 外すこと (ルールは無効化せず変数経由で回避する)。
               // 既存例: AbstractWebTestCase / MailServiceTest / CartServiceTest / TwigExtensionPass
               ContainerGetNameToTypeInTestsRector::class,
           ])
           // よく使われるルールセットを有効化
           ->withSets([
               SetList::DEAD_CODE,
               LevelSetList::UP_TO_PHP_84, // PHPバージョンに合わせる
               SymfonySetList::SYMFONY_74, // Symfonyのバージョンに合わせる (EC-CUBEのバージョンによって調整が必要)
               SymfonySetList::SYMFONY_CODE_QUALITY,
               SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
               DoctrineSetList::DOCTRINE_CODE_QUALITY,
               DoctrineSetList::DOCTRINE_DBAL_30, // Doctrine DBALのバージョンに合わせる
               DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES, // Doctrine Annotations を Attributes に変換
               PHPUnitSetList::PHPUNIT_CODE_QUALITY,
               PHPUnitSetList::PHPUNIT_110, // PHPUnitのバージョンに合わせる
           ])
           // Symfony のコンテナ XML（EC-CUBE の構成に合わせて調整が必要な場合があります）
           ->withSymfonyContainerXml(__DIR__.'/var/cache/dev/Eccube_KernelDevDebugContainer.xml')
           // オプション: キャッシュ設定 (パフォーマンス向上のために推奨)
           ->withCache(
               cacheClass: FileCacheStorage::class,
               cacheDirectory: './var/rector_cache'
           )
           // オプション: import文の整理
           ->withImportNames(
               importShortClasses: false,
               importDocBlockNames: true,
               importNames: true
           )
           // アノテーション→アトリビュートの変更
           ->withAttributesSets()
           // オプション: Rectorの実行をパラレルで行う (パフォーマンス向上)
           ->withParallel();
