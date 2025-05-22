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

namespace Eccube\Controller;

use Eccube\Repository\TradeLawRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class TradeLawController extends AbstractController
{
    /** @var TradeLawRepository */
    protected $tradeLawRepository;

    /**
     * @param TradeLawRepository $tradeLawRepository
     */
    public function __construct(
        TradeLawRepository $tradeLawRepository
    ) {
        $this->tradeLawRepository = $tradeLawRepository;
    }

    /**
     * @Route("/help/tradelaw", name="help_tradelaw", methods={"GET"})
     * @Template("Help/tradelaw.twig")
     */
    public function index()
    {
        $tradelaws = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);

        return [
            'tradelaws' => $tradelaws,
        ];
    }
}
