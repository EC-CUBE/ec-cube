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

use Twig\Attribute\AsTwigFunction;
use Twig\Environment;
use Twig\TemplateWrapper;

class TwigIncludeExtension
{
    public function __construct(protected Environment $twig)
    {
    }

    /**
     * 指定したテンプレートをレンダリングして返す
     *
     * @param array<mixed> $context 現在のコンテキスト
     * @param string|TemplateWrapper $template レンダリングするテンプレート名
     * @param array<mixed> $variables テンプレートに渡す変数
     *
     * @return string レンダリング結果
     */
    #[AsTwigFunction(name: 'include_dispatch', needsContext: true, isSafe: ['all'])]
    public function include_dispatch(array $context, string|TemplateWrapper $template, array $variables = []): string
    {
        if (!empty($variables)) {
            $context = array_merge($context, $variables);
        }

        return $this->twig->render($template, $context);
    }
}
