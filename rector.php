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

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodParameterRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\DeadCode\Rector\Switch_\RemoveDuplicatedCaseInSwitchRector;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Property\NestedAnnotationToAttributeRector;
use Rector\Php81\Rector\MethodCall\RemoveReflectionSetAccessibleCallsRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\AnnotationWithValueToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\RequiresAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\CodeQuality\Rector\MethodCall\AssertEqualsToSameRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony61\Rector\Class_\CommandConfigureToAttributeRector;
use Rector\Symfony\Symfony61\Rector\Class_\CommandPropertyToAttributeRector;
use Rector\ValueObject\PhpVersion;
use Eccube\Rector\CodingStyle\AttributeArgumentsOrderRector;
use Eccube\Rector\CodingStyle\NormalizePhpDocArrayGenericSpacingRector;

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
               __DIR__ . '/src/Eccube/Rector',
               // 特定のルールを除外する場合
               // Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector::class,
               //ClassPropertyAssignToConstructorPromotionRector::class, // プロモーション構文に変換する際に、@paramなどが削除されるため除外
               ClosureToArrowFunctionRector::class, // アロー関数への変換は一旦スキップ
               // 8.3以上で対応可能
               AddOverrideAttributeToOverriddenMethodsRector::class, // オーバーライドメソッドに @Override 属性を追加する PHP 8.3 以降で有効
               AddTypeToConstRector::class, // [BC]定数に型を追加する PHP 8.3 以降で有効
               /* Rector 2系へアップデート */
               // アトリビュート系を適用
               AnnotationWithValueToAttributeRector::class, // PHPUnitのバージョンアップ必須
               RequiresAnnotationWithValueToAttributeRector::class, // @requires アノテーションを属性に変換する。↑と同時に進める。
               RenameMethodRector::class, //addがaddCommandに変換されてしまうため一旦スキップ
           ])
           // 個別にルールを追加する場合はここに記述
           ->withRules([
               AssertEqualsToSameRector::class, // PHPUnitのassertEqualsをassertSameに変換する,
               CommandConfigureToAttributeRector::class, // Symfonyコマンドのconfigureメソッドをアトリビュートに変換する
               CommandPropertyToAttributeRector::class, // Symfonyコマンドのプロパティをアトリビュートに変換する,
               StaticDataProviderClassMethodRector::class, // PHPUnitのデータプロバイダを静的メソッドに変換する
               AttributeArgumentsOrderRector::class, // すべての Attribute の引数をコンストラクタ引数順序に統一する
               NormalizePhpDocArrayGenericSpacingRector::class, // PHPDoc の配列ジェネリクス表記のカンマ後のスペースを統一する
           ])
           // よく使われるルールセットを有効化
           ->withSets([
               SetList::DEAD_CODE,
               LevelSetList::UP_TO_PHP_84, // PHPバージョンに合わせる
               SymfonySetList::SYMFONY_74, // Symfonyのバージョンに合わせる (EC-CUBEのバージョンによって調整が必要)
               // SymfonySetList::SYMFONY_CODE_QUALITY,
               // SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
               // DoctrineSetList::DOCTRINE_CODE_QUALITY,
               // DoctrineSetList::DOCTRINE_DBAL_30, // Doctrine DBALのバージョンに合わせる
               // DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES, // Doctrine Annotations を Attributes に変換
               // PHPUnitSetList::PHPUNIT_CODE_QUALITY,
               PHPUnitSetList::PHPUNIT_110, // PHPUnitのバージョンに合わせる
           ])
           // オプション: SymfonyのコンテナXMLパス (EC-CUBEの構成に合わせて調整が必要な場合があります)
           // $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/Eccube_KernelDevDebugContainer.xml');
           ->withSymfonyContainerXml(__DIR__.'/var/cache/dev/Eccube_KernelDevDebugContainer.xml')
           // オプション: キャッシュ設定 (パフォーマンス向上のために推奨)
           ->withCache(
               cacheClass: FileCacheStorage::class,
               cacheDirectory: './var/rector_cache'
           )
           // オプション: import文の整理
           ->withImportNames(
               importShortClasses: false,
               importDocBlockNames: false,
               importNames: true
           )
           // アノテーション→アトリビュートの変更
           ->withAttributesSets()
           // オプション: Rectorの実行をパラレルで行う (パフォーマンス向上)
           ->withParallel();
