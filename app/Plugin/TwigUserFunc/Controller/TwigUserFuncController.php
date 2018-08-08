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

namespace Plugin\TwigUserFunc\Controller;

use Eccube\Annotation\Inject;
use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route(service=TwigUserFuncController::class)
 */
class TwigUserFuncController
{
    /**
     * @Inject("twig")
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @Route("/twiguserfunc")
     */
    public function index(Application $app)
    {
        return $this->twig
            ->createTemplate("{{ eccube_block_hello({'name':'EC-CUBE'}) }}")
            ->render([]);
    }
}
