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

namespace Eccube\Twig;

use Twig\Environment as TwigEnvironment;
use Twig\Source;

/**
 * Twig 3.8 以降、生成クラスは常に Twig\Template を継承するため symfony/twig-bundle の
 * base_template_class がコンパイル結果に反映されない。compile 後に継承先を差し替える。
 *
 * @see https://github.com/twigphp/Twig/blob/3.x/src/Node/ModuleNode.php (compileClassHeader)
 */
class Environment extends TwigEnvironment
{
    public function compileSource(Source $source): string
    {
        $content = parent::compileSource($source);

        return str_replace('extends Template', 'extends \\Eccube\\Twig\\Template', $content);
    }
}
