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

namespace Eccube\Twig\Extension;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\AbstractExtension;
use Twig\Extension\SandboxExtension;
use Twig\Sandbox\SecurityError;
use Twig\TwigFunction;

/**
 * \vendor\twig\twig\src\Extension\CoreExtension の拡張
 */
class IgnoreTwigSandboxErrorExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('include', [$this, 'twig_include'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]),
        ];
    }

    /**
     * twig sandboxの例外を操作します
     * app_env = devの場合、エラーを表示する
     * app_env = prodの場合、エラーを表示しない
     *
     * @param Environment $env
     * @param $context
     * @param $template
     * @param $variables
     * @param $withContext
     * @param $ignoreMissing
     * @param $sandboxed
     *
     * @return string|void
     *
     * @throws LoaderError
     * @throws SecurityError
     */
    public function twig_include(Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = false, $sandboxed = false)
    {
        try {
            return \twig_include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);
        } catch (SecurityError $e) {

            // devではエラー画面が表示されるようにする
            $appEnv = env('APP_ENV');
            if ($appEnv === 'dev') {
                throw $e;
            } else {
                // ログ出力
                log_warning($e->getMessage(), ['exception' => $e]);

                // 例外がスローされた場合、sandboxが効いた状態になってしまうため追加
                $sandbox = $env->getExtension(SandboxExtension::class);
                if (!$sandbox->isSandboxedGlobally()) {
                    $sandbox->disableSandbox();
                }
            }
        }
    }
}
