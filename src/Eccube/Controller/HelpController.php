<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller;

use Eccube\Repository\HelpRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route(service=HelpController::class)
 */
class HelpController extends AbstractController
{
    /**
     * @var HelpRepository
     */
    protected $helpRepository;

    /**
     * HelpController constructor.
     * @param HelpRepository $helpRepository
     */
    public function __construct(HelpRepository $helpRepository)
    {
        $this->helpRepository = $helpRepository;
    }


    /**
     * 特定商取引法.
     *
     * @Route("/help/tradelaw", name="help_tradelaw")
     * @Template("Help/tradelaw.twig")
     */
    public function tradelaw()
    {
        $Help = $this->helpRepository->get();

        return [
            'help' => $Help,
        ];
    }

    /**
     * ご利用ガイド.
     *
     * @Route("/guide", name="help_guide")
     * @Template("Help/guide.twig")
     */
    public function guide()
    {
        return [];
    }

    /**
     * 当サイトについて.
     *
     * @Route("/help/about", name="help_about")
     * @Template("Help/about.twig")
     */
    public function about()
    {
        return [];
    }

    /**
     * プライバシーポリシー.
     *
     * @Route("/help/privacy", name="help_privacy")
     * @Template("Help/privacy.twig")
     */
    public function privacy()
    {
        return [];
    }

    /**
     * 利用規約.
     *
     * @Route("/help/agreement", name="help_agreement")
     * @Template("Help/agreement.twig")
     */
    public function agreement()
    {
        $Help = $this->helpRepository->get();

        return [
            'help' => $Help,
        ];
    }
}
