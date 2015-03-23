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

        $tradelaw = $app['orm.em']
            ->getRepository('Eccube\Entity\BaseInfo')
            ->findAll();

        // todo getRow的なものが欲しい
        $tradelaw = $tradelaw[0];

        // todo mtb_*を処理する共通クラス
        $pref = $app['orm.em']
            ->getRepository('Eccube\Entity\Pref')
            ->findMasterData();

        return $app['twig']->render('Help/tradelaw.twig', compact('title', 'tradelaw', 'pref'));
    }
}
