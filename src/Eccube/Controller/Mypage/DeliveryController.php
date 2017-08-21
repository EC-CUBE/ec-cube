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


namespace Eccube\Controller\Mypage;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\CustomerAddressType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerAddressRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=DeliveryController::class)
 */
class DeliveryController extends AbstractController
{
    /**
     * @Inject(BaseInfoRepository::class)
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(CustomerAddressRepository::class)
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * お届け先一覧画面.
     *
     * @Route("/mypage/delivery", name="mypage_delivery")
     * @Template("Mypage/delivery.twig")
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['user'];

        return [
            'Customer' => $Customer,
        ];
    }

    /**
     * お届け先編集画面.
     *
     * @Route("/mypage/delivery/new", name="mypage_delivery_new")
     * @Route("/mypage/delivery/{id}/edit", name="mypage_delivery_edit", requirements={"id":"\d+"})
     * @Template("Mypage/delivery_edit.twig")
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        $Customer = $app['user'];

        // 配送先住所最大値判定
        // $idが存在する際は、追加処理ではなく、編集の処理ため本ロジックスキップ
        if (is_null($id)) {
            $addressCurrNum = count($Customer->getCustomerAddresses());
            $addressMax = $this->appConfig['deliv_addr_max'];
            if ($addressCurrNum >= $addressMax) {
                throw new NotFoundHttpException('お届け先の登録数の上限を超えています');
            }
        }

        $CustomerAddress = $this->customerAddressRepository->findOrCreateByCustomerAndId($Customer, $id);

        $parentPage = $request->get('parent_page', null);

        // 正しい遷移かをチェック
        $allowdParents = array(
            $app->url('mypage_delivery'),
            $app->url('shopping_redirect_to'),
        );

        // 遷移が正しくない場合、デフォルトであるマイページの配送先追加の画面を設定する
        if (!in_array($parentPage, $allowdParents)) {
            // @deprecated 使用されていないコード
            $parentPage = $app->url('mypage_delivery');
        }

        $builder = $this->formFactory
            ->createBuilder(CustomerAddressType::class, $CustomerAddress);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
                'CustomerAddress' => $CustomerAddress,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_DELIVERY_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('お届け先登録開始', array($id));

            $this->entityManager->persist($CustomerAddress);
            $this->entityManager->flush();

            log_info('お届け先登録完了', array($id));

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Customer' => $Customer,
                    'CustomerAddress' => $CustomerAddress,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_DELIVERY_EDIT_COMPLETE, $event);

            $app->addSuccess('mypage.delivery.add.complete');

            return $app->redirect($app->url('mypage_delivery'));
        }

        $BaseInfo = $this->baseInfoRepository->get();

        return [
            'form' => $form->createView(),
            'parentPage' => $parentPage,
            'BaseInfo' => $BaseInfo,
        ];
    }

    /**
     * お届け先を削除する.
     *
     * @Route("/mypage/delivery/{id}/delete", name="mypage_delivery_delete")
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        log_info('お届け先削除開始', array($id));

        $Customer = $app['user'];

        $status = $this->customerAddressRepository->deleteByCustomerAndId($Customer, $id);

        if ($status) {
            $event = new EventArgs(
                array(
                    'id' => $id,
                    'Customer' => $Customer,
                ), $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_DELIVERY_DELETE_COMPLETE, $event);

            $app->addSuccess('mypage.address.delete.complete');

            log_info('お届け先削除完了', array($id));

        } else {
            $app->addError('mypage.address.delete.failed');

            log_info('お届け先削除失敗', array($id));
        }

        return $app->redirect($app->url('mypage_delivery'));
    }
}
