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

/**
 * @deprecated Twig\Environmentを利用してください。
 * https://github.com/EC-CUBE/ec-cube/pull/4362 の修正で不要になったが、互換性のためにクラスは残す。
 */
class Environment extends \Twig_Environment
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render($name, array $context = [])
    {
        return $this->twig->render($name, $context);
    }
}
