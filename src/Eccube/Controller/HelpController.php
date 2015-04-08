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

        $baseInfo = $app['orm.em']
            ->getRepository('Eccube\Entity\BaseInfo')
            ->getBaseInfo();

        // todo mtb_*を処理する共通クラス
        $pref = $app['orm.em']
            ->getRepository('Eccube\Entity\Master\Pref')
            ->findMasterData();

        return $app['twig']->render('Help/tradelaw.twig', compact('title', 'baseInfo', 'pref'));
    }


    public function guide(Application $app)
    {
        $title = 'ご利用ガイド';

        return $app['twig']->render('Help/guide.twig', compact('title'));
    }


    public function about(Application $app)
    {
        $title = '当サイトについて';

        $baseInfo = $app['orm.em']
            ->getRepository('Eccube\Entity\BaseInfo')
            ->getBaseInfo();

        $pref = $app['orm.em']
            ->getRepository('Eccube\Entity\Master\Pref')
            ->findMasterData();

        return $app['twig']->render('Help/about.twig', compact('title', 'baseInfo', 'pref'));
    }


    public function privacy(Application $app)
    {
        $title = 'プライバシーポリシー';

        $baseInfo = $app['orm.em']
            ->getRepository('Eccube\Entity\BaseInfo')
            ->getBaseInfo();

        return $app['twig']->render('Help/privacy.twig', compact('title', 'baseInfo'));
    }
}
