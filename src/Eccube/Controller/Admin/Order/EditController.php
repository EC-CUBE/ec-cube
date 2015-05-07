<?php
/**
 * Created by PhpStorm.
 * User: chihiro_adachi
 * Date: 15/04/23
 * Time: 14:01
 */

namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Symfony\Component\HttpKernel\Exception as HttpException;

class EditController
{
    public function index(Application $app, $orderId = 0)
    {
        if ($orderId == 0) {
            $Order = $app['eccube.service.order']->newOrder();
        } else {
            $Order = $app['orm.em']
                ->getRepository('Eccube\Entity\Order')
                ->find($orderId);
        }
        if (is_null($Order)) {
            throw new HttpException\NotFoundHttpException('order is not found.');
        }

        $form = $app['form.factory']
            ->createBuilder('order', $Order)
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $Order = $form->getData();
                $OrderDetails = $Order->getOrderDetails();
                $Shippings = $Order->getShippings();

                $app['orm.em']->persist($Order);
                $app['orm.em']->flush();
                // TODO: リダイレクトすると検索条件が消える
                // return $app->redirect($app['url_generator']->generate('admin_order'));
            }
        }

        return $app['view']->render('Admin/Order/edit.twig', array(
                'form' => $form->createView(),
                'title' => '受注管理',
                'sub_title' => '受注編集',
                'Order' => $Order,
                'orderId' => $orderId,
        ));
    }
}
