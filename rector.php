<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentImplodeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodParameterRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\DeadCode\Rector\Switch_\RemoveDuplicatedCaseInSwitchRector;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\CodeQuality\Rector\MethodCall\AssertEqualsToSameRector;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php56\Rector\FuncCall\PowToExpRector;
use Rector\Php70\Rector\Ternary\TernaryToNullCoalescingRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php71\Rector\List_\ListToArrayDestructRector;
use Rector\Php73\Rector\FuncCall\StringifyStrNeedlesRector;
use Rector\Php74\Rector\Assign\NullCoalescingOperatorRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php80\Rector\ClassConstFetch\ClassOnThisVariableObjectRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php80\Rector\Identical\StrStartsWithRector;
use Rector\Php80\Rector\NotIdentical\StrContainsRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
           // EC-CUBEのPHPバージョンに合わせて設定
           ->withPhpVersion(PhpVersion::PHP_81)

           // Rectorが解析するパスを指定
           ->withPaths([
               __DIR__ . '/src',
               // __DIR__ . '/app',
               __DIR__.'/tests',
               __DIR__.'/codeception',
               // プラグインディレクトリ等、個別案件の場合は必要に応じて追加
               // __DIR__ . '/app/Plugin',
           ])
           // スキップするパスやルールを指定
           ->withSkip([
               // 特定のファイルやディレクトリを除外する場合
               // __DIR__ . '/src/Eccube/Legacy',
               // 特定のルールを除外する場合
               // Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector::class,
               RemoveUselessParamTagRector::class, // まだ @param に頼っているケースがありそうなので除外
               RemoveUselessReturnTagRector::class, // まだ @return に頼っているケースがありそうなので除外
               RemoveUselessVarTagRector::class, // まだ @var に頼っているケースがありそうなので除外
               SimplifyUselessVariableRector::class, // まだ不要な変数が多いので除外
               RemoveAlwaysTrueIfConditionRector::class, // 条件式の簡略化はまだ慎重に行う必要があるため除外
               RemoveDuplicatedCaseInSwitchRector::class, // switch文の重複ケース削除は視認性が悪くなるため除外
               RemoveUnusedPrivateMethodParameterRector::class, // 未使用のパラメータ削除は慎重に行う必要があるため除外
               RemoveUnusedConstructorParamRector::class, // コンストラクタの未使用パラメータはプラグインで使用される可能性があるため除外
               RemoveUnusedPrivatePropertyRector::class, // 未使用のプライベートプロパティ削除は慎重に行う必要があるため除外
               ClassPropertyAssignToConstructorPromotionRector::class, // プロモーション構文に変換する際に、@paramなどが削除されるため除外
               MixedTypeRector::class,// mixed を付与することだけではなく、@param行が冗長と判断された場合は削除するため除外
               ClosureToArrowFunctionRector::class, // アロー関数への変換は一旦スキップ
               // TODO:こちらを段々と適応します
               //StringClassNameToClassConstantRector::class, // クラス名を文字列で指定している箇所は、クラス定数に変換する
               StringableForToStringRector::class, // __toString() メソッドを返り値の型付き（: string）にする
               TernaryToNullCoalescingRector::class, // 三項演算子をnull合体演算子に変換する
               TernaryToElvisRector::class, // 三項演算子をエルビス演算子に変換する
               PowToExpRector::class, // pow()関数を指数演算子に変換する
               RemoveUnusedVariableAssignRector::class, // 未使用の変数代入を削除する
               LongArrayToShortArrayRector::class, // 長い配列構文を短い配列構文に変換する
               ListToArrayDestructRector::class, // list()構文を配列分割に変換する
               StringifyStrNeedlesRector::class, // strpos()のneedleを文字列に変換する
               NullCoalescingOperatorRector::class, // null合体演算子を使用する
               RemoveExtraParametersRector::class, // 不要なパラメータを削除する
               RestoreDefaultNullToNullableTypePropertyRector::class, // nullをデフォルト値に設定する
               ChangeSwitchToMatchRector::class, // switch文をmatch式に変換する
               ConsistentImplodeRector::class, // implode()の引数を一貫性のある形式に変換する
               StrStartsWithRector::class, // str_starts_with()を使用する
               StrContainsRector::class, // str_contains()を使用する
               RemoveUnusedVariableInCatchRector::class, // catchブロック内の未使用変数を削除する
               ClassOnThisVariableObjectRector::class, // `$this::class` を `static::class`／`self::class` に書き換え
               ClassOnObjectRector::class,  // `get_class($obj)` を `$obj::class` に書き換え,
           ])
           // 個別にルールを追加する場合はここに記述
           ->withRules([
               AssertEqualsToSameRector::class, // PHPUnitのassertEqualsをassertSameに変換する
           ])
           // よく使われるルールセットを有効化
           ->withSets([
               SetList::DEAD_CODE,
               LevelSetList::UP_TO_PHP_80, // PHPバージョンに合わせる
               // SymfonySetList::SYMFONY_64, // Symfonyのバージョンに合わせる (EC-CUBEのバージョンによって調整が必要)
               // SymfonySetList::SYMFONY_CODE_QUALITY,
               // SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
               // DoctrineSetList::DOCTRINE_CODE_QUALITY,
               // DoctrineSetList::DOCTRINE_DBAL_30, // Doctrine DBALのバージョンに合わせる
               // DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES, // Doctrine Annotations を Attributes に変換
               // PHPUnitSetList::PHPUNIT_CODE_QUALITY,
               PHPUnitSetList::PHPUNIT_90, // PHPUnitのバージョンに合わせる
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
               importNames: false
           )
           // オプション: Rectorの実行をパラレルで行う (パフォーマンス向上)
           ->withParallel();
