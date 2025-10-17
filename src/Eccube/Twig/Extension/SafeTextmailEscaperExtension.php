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
use Twig\Extension\AbstractExtension;
use Twig\Runtime\EscaperRuntime;

class SafeTextmailEscaperExtension extends AbstractExtension
{
    public function __construct(Environment $twig)
    {
        /** @var EscaperRuntime $escaper */
        $escaper = $twig->getRuntime(EscaperRuntime::class);
        $escaper->setEscaper(
            'safe_textmail', function ($string, $charset) {
                return str_replace(['<', '>'], ['＜', '＞'], $string);
            }
        );
    }
}
