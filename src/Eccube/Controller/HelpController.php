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

class HelpController extends AbstractController
{
    public function __construct()
    {
    }

    public function tradelaw(Application $app)
    {
        $title = '特定商取引法';

        $Help = $app['orm.em']->getRepository('Eccube\Entity\Help')->find(1);

        return $app['view']->render('Help/tradelaw.twig', array(
                        'title' => $title,
                        'help' => $Help,
        ));

//        return $app['twig']->render('Help/tradelaw.twig', compact('title', 'help'));
    }

    public function guide(Application $app)
    {
        $title = 'ご利用ガイド';

        return $app['twig']->render('Help/guide.twig', compact('title'));
    }

    public function about(Application $app)
    {
        $title = '当サイトについて';

        $baseInfo = $app['eccube.repository.base_info']->get();

        return $app['twig']->render('Help/about.twig', compact('title', 'baseInfo'));
    }

    public function privacy(Application $app)
    {
        $title = 'プライバシーポリシー';

        $baseInfo = $app['eccube.repository.base_info']->get();

        return $app['twig']->render('Help/privacy.twig', compact('title', 'baseInfo'));
    }

    public function agreement(Application $app)
    {
        $title = '会員規約';

        $Help = $app['orm.em']->getRepository('Eccube\Entity\Help')->find(1);

        return $app['view']->render('Help/agreement.twig', array(
                        'title' => $title,
                        'help' => $Help,
        ));
    }
}
