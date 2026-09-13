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
 * テンプレートの一括事前コンパイルを eccube:cache:build のときだけ実行するようにする.
 *
 * kernel.cache_dir と kernel.build_dir が別パスになると, Kernel::initializeContainer() が
 * コンテナ再構築のたびに CacheWarmerAggregate::enableOptionalWarmers() を呼ぶ
 * (vendor/symfony/http-kernel/Kernel.php). その結果, 本来デプロイ時にだけ実行したい
 * 全テンプレートのコンパイルが composer install や Web リクエスト起因の再構築でも走り,
 * ピークメモリが 87.5MiB から 219MiB へ跳ね上がる (実測).
 *
 * kernel.cache_warmer タグを外して自動実行の対象から除き, eccube:cache:build が
 * 明示的に呼び出す. 事前コンパイルされなかったテンプレートは, 従来どおり
 * リクエスト処理中に twig.template_cache.runtime_cache へコンパイルされる.
 *
 * 他の warmer は外さない. 翻訳カタログや HTMLPurifier のシリアライザキャッシュは
 * ソースから導かれるビルド生成物で, kernel.cache_dir 配下へ出力される
 * (リクエスト処理中は読み取りのみ). とくに HTMLPurifier は基底ディレクトリを
 * 自分では作らず警告を出すため (DefinitionCache/Serializer::_prepareDir()),
 * warmer を外すとテンプレートの描画が失敗する.
 */
class BuildDirCacheWarmerPass implements CompilerPassInterface
{
    public const TWIG_WARMER_ID = 'twig.template_cache_warmer';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::TWIG_WARMER_ID)) {
            return;
        }

        $container->getDefinition(self::TWIG_WARMER_ID)
            ->clearTag('kernel.cache_warmer')
            // eccube:cache:build が再起動後のコンテナから取得するため public にする.
            ->setPublic(true);
    }
}
