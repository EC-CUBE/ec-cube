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

namespace Eccube\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * コンテナ再構築に伴う自動 warmup を, ビルドディレクトリへ書くものだけに絞る.
 *
 * kernel.cache_dir と kernel.build_dir が別パスになると, Kernel::initializeContainer() が
 * コンテナ再構築のたびに CacheWarmerAggregate::enableOptionalWarmers() を呼ぶ
 * (vendor/symfony/http-kernel/Kernel.php). その結果, 次の 2 つの問題が起きる.
 *
 * 1. %eccube_runtime_dir% へ書く warmer が CLI から実行される.
 *    ランタイムディレクトリは Web サーバー所有のため, 権限を分離した構成では
 *    eccube:cache:build が Permission denied で失敗する.
 * 2. 全テンプレートのコンパイルがデプロイ時以外でも走り, composer install 時の
 *    ピークメモリが 87.5MiB から 219MiB へ跳ね上がる (実測).
 *
 * いずれも「リクエスト処理中に生成されるキャッシュ」であり, 事前生成しなくても
 * 初回アクセス時に Web サーバーが生成する. そのため kernel.cache_warmer タグを外し,
 * 自動 warmup の対象から除く. テンプレートの事前コンパイルだけは eccube:cache:build が
 * 明示的に呼び出すため, サービスを public にしておく.
 */
class BuildDirCacheWarmerPass implements CompilerPassInterface
{
    public const TWIG_WARMER_ID = 'twig.template_cache_warmer';

    /**
     * %eccube_runtime_dir% 配下へ書き込む warmer.
     *
     * - translation.warmer: Translator::warmUp() は引数の cacheDir ではなく
     *   framework.translator.cache_dir へ書く (Translator.php:95-114)
     * - exercise_html_purifier.cache_warmer.serializer: 設定値 (default_cache_serializer_path) へ書く
     *
     * @var list<string>
     */
    private const RUNTIME_WARMER_IDS = [
        'translation.warmer',
        'exercise_html_purifier.cache_warmer.serializer',
    ];

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        foreach (self::RUNTIME_WARMER_IDS as $id) {
            if ($container->hasDefinition($id)) {
                $container->getDefinition($id)->clearTag('kernel.cache_warmer');
            }
        }

        if (!$container->hasDefinition(self::TWIG_WARMER_ID)) {
            return;
        }

        $container->getDefinition(self::TWIG_WARMER_ID)
            ->clearTag('kernel.cache_warmer')
            // eccube:cache:build が再起動後のコンテナから取得するため public にする.
            ->setPublic(true);
    }
}
