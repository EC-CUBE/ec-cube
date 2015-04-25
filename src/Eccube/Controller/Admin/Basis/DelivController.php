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

        // 商品種別をセット
        $productTypeId = $Deliv->getProductTypeId();
        $ProductType = $app['orm.em']->getRepository('\Eccube\Entity\Master\ProductType')
            ->find($productTypeId);
        $Deliv->setProductType($ProductType);

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

                // 複合主キーのため、一回消して、再度連番をふりなおす
                // FIXME :順番がラリってる
                $DelivTimesData = $DelivData->getDelivTimes();
                $timeId = 1;
                foreach ($DelivTimesData as $DelivTimeData) {
                    $DelivData->removeDelivTime($DelivTimeData);

                    $DelivTimeData
                        ->setDeliv($DelivData)
                        // ->setDelivId($delivId)
                        // ->setTimeId($timeId)
                    ;

                    $DelivData->addDelivTime($DelivTimeData);
                    $timeId ++;
                }

                $PaymentOptionsOld = $DelivData->getPaymentOptions();
                // 消す
                foreach ($PaymentOptionsOld as $PaymentOptionOld) {
                    $DelivData->removePaymentOption($PaymentOptionOld);
                    $app['orm.em']->remove($PaymentOptionOld);

                }
                // いれる
                $PaymentsData = $form->get('payments')->getData();
                foreach ($PaymentsData as $PaymentData) {
                    $PaymentOption
                        ->setPayment($PaymentData)
                        ->setDeliv($DelivData)
                    ;

                    $DelivData->addPaymentOption($PaymentOption);
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
        $Deliv = $app['orm.em']->getRepository('Eccube\Entity\Deliv')->find($delivId);

        $app['orm.em']->persist($Deliv->setDelFlg(1));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('deliv.complete', 'admin.deliv.delete.complete') ;

        return $app->redirect($app['url_generator']->generate('admin_basis_deliv'));
    }

    public function up(Application $app, $delivId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Deliv');

        $current = $repo->find($delivId);
        $currentRank = $current->getRank();

        $targetRank = $currentRank + 1;
        $target = $repo->findOneBy(array('rank' => $currentRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('deliv.complete', 'admin.deliv.up.complete');

        return $app->redirect($app['url_generator']->generate('admin_basis_deliv'));
    }

    public function down(Application $app, $delivId)
    {
        $repo = $app['orm.em']->getRepository('Eccube\Entity\Deliv');

        $current = $repo->find($delivId);
        $currentRank = $current->getRank();

        $targetRank = $currentRank - 1;
        $target = $repo->findOneBy(array('rank' => $currentRank));

        $app['orm.em']->persist($target->setRank($currentRank));
        $app['orm.em']->persist($current->setRank($targetRank));
        $app['orm.em']->flush();

        $app['session']->getFlashBag()->add('deliv.complete', 'admin.deliv.up.complete');

        return $app->redirect($app['url_generator']->generate('admin_basis_deliv'));
    }

}