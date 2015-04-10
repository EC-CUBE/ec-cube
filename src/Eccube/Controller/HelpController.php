<?php

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

        $baseInfo = $app['eccube.repository.base_info']->get();

        return $app['twig']->render('Help/tradelaw.twig', compact('title', 'baseInfo'));
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
}
