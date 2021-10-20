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
use Twig\Extension\EscaperExtension;

class SafeTextmailEscaperExtension extends AbstractExtension
{
    public function __construct(\Twig\Environment $twig)
    {
        $twig->getExtension(EscaperExtension::class)->setEscaper(
            'safe_textmail', function ($twig, $string, $charset) {
                return str_replace(['<', '>'], ['＜', '＞'], $string);
            }
        );
    }
}
