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

use Twig\Extension\AbstractExtension;
use Twig\TemplateWrapper;
use Twig\TwigFunction;

class TwigIncludeExtension extends AbstractExtension
{
    /**
     * @var \Twig\Environment
     */
    protected $twig;

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    #[\Override]
    public function getFunctions()
    {
        return [
            new TwigFunction('include_dispatch', $this->include_dispatch(...),
                ['needs_context' => true, 'is_safe' => ['all']]),
        ];
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
    public function include_dispatch($context, $template, $variables = [])
    {
        if (!empty($variables)) {
            $context = array_merge($context, $variables);
        }

        return $this->twig->render($template, $context);
    }
}
