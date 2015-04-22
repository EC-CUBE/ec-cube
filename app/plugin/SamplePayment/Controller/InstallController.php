<?php

namespace Plugin\SamplePayment\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class InstallController
{
    const PAYMENT_ID = 10;

    const PAYMENT_METHOD = 'ロックオン決済';

    public function index(Application $app, Request $request)
    {
        $payment = $app['orm.em']->getRepository('Eccube\Entity\Payment')
            ->findBy(array(
                'module_id' => self::PAYMENT_ID,
            ));
        if (count($payment) > 0) {
            $message = '既にinstallされています。';
        } else {
            $Payment = new \Eccube\Entity\Payment;
            $Payment
                ->setMethod(self::PAYMENT_METHOD)
                ->setModuleId(self::PAYMENT_ID)
                ->setCharge(0)
                ->setFix(2)
                ->setStatus(1)
                ->setDelFlg(0)
                ->setChargeFlg(1)
                ->setCreatorId(1)                
                ->setCreateDate(new \DateTime())
                ->setUpdateDate(new \DateTime())
            ;
            $app['orm.em']->persist($Payment);
            $app['orm.em']->flush();
            $message = 'installしました。';
        }

        return $app['view']->render('SamplePayment/View/install.twig', compact('message'));
    }
}