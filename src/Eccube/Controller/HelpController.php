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

        // todo 返り値がnullの場合の処理をどうすればいいのか調べる
        $tradelaw = $app['orm.em']
            ->getRepository('Eccube\Entity\BaseInfo')
            ->findAll();

        $tradelaw = $tradelaw[0];

        // todo id => key の配列にして返す
        $pref = $app['orm.em']
            ->getRepository('Eccube\Entity\Pref')
            ->findMasterData();

        return $app['twig']->render('Help/tradelaw.twig', compact('title', 'tradelaw', 'pref'));
    }

}
