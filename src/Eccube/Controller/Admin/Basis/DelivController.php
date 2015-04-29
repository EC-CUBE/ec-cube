<?php

namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class DelivController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->sub_title = '配送方法設定';
    }

    public function index(Application $app)
    {
        $Delivs = $app['orm.em']->getRepository('Eccube\Entity\Deliv')
            ->findBy(array('del_flg' => 0), array('rank' => 'DESC'));

        return $app['view']->render('Admin/Basis/deliv.twig', array(
            'Delivs' => $Delivs,
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle' => $this->sub_title,
        ));
    }

    public function edit(Application $app, $delivId = 0)
    {
        $Deliv = $app['orm.em']->getRepository('\Eccube\Entity\Deliv')
            ->findOrCreate($delivId);

        // FormType: DelivFeeの生成
        $Prefs = $app['orm.em']
            ->getRepository('\Eccube\Entity\Master\Pref')
            ->findAll();

        foreach ($Prefs as $Pref) {
            $DelivFee = $app['orm.em']
                ->getRepository('\Eccube\Entity\DelivFee')
                ->findOrCreate(array(
                    'Deliv' => $Deliv,
                    'deliv_id' => $delivId,
                    'Pref' => $Pref,
                ));
            if (!$DelivFee->getFee()) {
                $Deliv->addDelivFee($DelivFee);
            }
        }

        $DelivFees = $Deliv->getDelivFees();
        $DelivFeesIndex = array();
        foreach ($DelivFees as $DelivFee) {
            $Deliv->removeDelivFee($DelivFee);
            $DelivFeesIndex[$DelivFee->getPref()->getId()] = $DelivFee;
        }
        ksort($DelivFeesIndex);
        foreach ($DelivFeesIndex as $timeId => $DelivFee) {
            $Deliv->addDelivFee($DelivFee);
        }

        // FormType: DelivTimeの生成
        for ($timeId = 1; $timeId <= 16; $timeId++) {
            $DelivTime = $app['orm.em']
                ->getRepository('\Eccube\Entity\DelivTime')
                ->findOrCreate(array(
                    'Deliv' => $Deliv,
                    'deliv_id' => $delivId,
                    'time_id' => $timeId,
                ));
            if (!$DelivTime->getDelivTime()) {
                $Deliv->addDelivTime($DelivTime);
            }
        }


        // 商品種別をセット
        $productTypeId = $Deliv->getProductTypeId();
        $ProductType = $app['orm.em']->getRepository('\Eccube\Entity\Master\ProductType')
            ->find($productTypeId);
        $Deliv->setProductType($ProductType);

        // 配送方法を順番に並び替え
        $DelivTimes = $Deliv->getDelivTimes();
        $DelivTimesIndex = array();
        foreach ($DelivTimes as $DelivTime) {
            $Deliv->removeDelivTime($DelivTime);
            $DelivTimesIndex[$DelivTime->getTimeId()] = $DelivTime;
        }
        ksort($DelivTimesIndex);
        foreach ($DelivTimesIndex as $timeId => $DelivTime) {
            $Deliv->addDelivTime($DelivTime);
        }

        $builder = $app['form.factory']
            ->createBuilder('deliv');

        $form = $builder->getForm();
        $form->setData($Deliv);

        // 支払方法をセット
        $Payments = array();
        foreach ($Deliv->getPaymentOptions() as $PaymentOption) {
            $Payments[] = $app['orm.em']->getRepository('\Eccube\Entity\Payment')
                ->find($PaymentOption->getPaymentId());
        }
        $form->get('payments')->setData($Payments);

        // 登録ボタン押下
        if ($app['request']->getMethod() === 'POST') {

            $form->handleRequest($app['request']);

            if ($form->isValid()) {

                $DelivData = $form->getData();

                // 商品種別をEntityからIDに変換してセット
                $ProductType = $DelivData->getProductType();
                $DelivData->setProductTypeId($ProductType->getId());

                // 配送時間の登録
                $DelivTimes = $form->get('deliv_times')->getData();
                foreach ($DelivTimes as $DelivTime) {
                    if (is_null($DelivTime->getDelivTime())) {
                        $Deliv->removeDelivTime($DelivTime);
                    }
                }


                // お支払いの登録
                $PaymentOptions = $app['orm.em']->getRepository('\Eccube\Entity\PaymentOption')
                    ->findBy(array('deliv_id' => $delivId));
                // 消す
                foreach ($PaymentOptions as $PaymentOption) {
                    $DelivData->removePaymentOption($PaymentOption);
                    $app['orm.em']->remove($PaymentOption);
                }
                $app['orm.em']->persist($DelivData);
                $app['orm.em']->flush();

                // 新しく今登録したIDを取得する必要がある
                $delivId = $Deliv->getId();

                // いれる
                $rank = 1;
                $PaymentsData = $form->get('payments')->getData();
                foreach ($PaymentsData as $PaymentData) {
                    $PaymentOption = new \Eccube\Entity\PaymentOption();
                    $PaymentOption
                        ->setDelivId($delivId)
                        ->setPaymentId($PaymentData->getId())
                        ->setDeliv($DelivData)
                        ->setPayment($PaymentData)
                        ->setRank($rank)
                    ;
                    $DelivData->addPaymentOption($PaymentOption);
                    $rank ++;
                }

                $app['orm.em']->persist($DelivData);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('deliv.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_basis_delivery'));
            }
        }


        return $app['view']->render('Admin/Basis/deliv_edit.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tplsubtitle' => $this->sub_title,
            'form' => $form->createView(),
            'deliv_id' => $delivId,
        ));
    }

    public function delete(Application $app, $delivId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Deliv');
        $Deliv = $repo->find($delivId);

        $Deliv
            ->setDelFlg(1)
            ->setRank(0);
        $app['orm.em']->persist($Deliv);

        $rank = 1;
        $Delivs = $repo->findBy(array('del_flg' => 0), array('rank' => 'ASC'));
        foreach ($Delivs as $Deliv) {
            if ($Deliv->getId() != $delivId) {
                $Deliv->setRank($rank);
                $rank ++;
            }
        }
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('deliv.complete', 'admin.delete.complete') ;

        return $app->redirect($app['url_generator']->generate('admin_basis_deliv'));
    }

    public function up(Application $app, $delivId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Deliv');

        $current = $repo->find($delivId);
        $currentRank = $current->getRank();

        $targetRank = $currentRank + 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('deliv.complete', 'admin.rank.move.complete');

        return $app->redirect($app['url_generator']->generate('admin_basis_deliv'));
    }

    public function down(Application $app, $delivId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Deliv');

        $current = $repo->find($delivId);
        $currentRank = $current->getRank();

        $targetRank = $currentRank - 1;
        $target = $repo->findOneBy(array('rank' => $targetRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('deliv.complete', 'admin.rank.move.complete');

        return $app->redirect($app['url_generator']->generate('admin_basis_deliv'));
    }

}