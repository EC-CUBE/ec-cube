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

use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service=HelpController::class)
 */
class HelpController extends AbstractController
{
    
    /**
     * 特定商取引法.
     *
     * @Route("/help/tradelaw", name="help_tradelaw")
     * @Template("Help/tradelaw.twig")
     */
    public function tradelaw(Application $app, Request $request)
    {
        return [];
    }

    /**
     * ご利用ガイド.
     *
     * @Route("/guide", name="help_guide")
     * @Template("Help/guide.twig")
     */
    public function guide(Application $app, Request $request)
    {
        return [];
    }

    /**
     * 当サイトについて.
     *
     * @Route("/help/about", name="help_about")
     * @Template("Help/about.twig")
     */
    public function about(Application $app, Request $request)
    {
        return [];
    }

    /**
     * プライバシーポリシー.
     *
     * @Route("/help/privacy", name="help_privacy")
     * @Template("Help/privacy.twig")
     */
    public function privacy(Application $app, Request $request)
    {
        return [];
    }

    /**
     * 利用規約.
     *
     * @Route("/help/agreement", name="help_agreement")
     * @Template("Help/agreement.twig")
     */
    public function agreement(Application $app, Request $request)
    {
        return [];
    }
}
