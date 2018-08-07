<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Twig\Extension;

use Twig\Extension\AbstractExtension;

class TwigIncludeExtension extends AbstractExtension
{
    protected $twig;

    public function __construct(\Eccube\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('include_dispatch', [$this, 'include_dispatch'],
                ['needs_context' => true, 'is_safe' => ['all']]),
        ];
    }

    public function include_dispatch($context, $template, $variables = [])
    {
        if (!empty($variables)) {
            $context = array_merge($context, $variables);
        }

        return $this->twig->render($template, $context);
    }
}
