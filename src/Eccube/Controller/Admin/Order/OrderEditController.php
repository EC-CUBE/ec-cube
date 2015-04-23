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

class OrderEditController {

    public function index(Application $app, $orderId)
    {
        $Order = $app['orm.em']
            ->getRepository('Eccube\Entity\Order')
            ->find($orderId);

        $form = $app['form.factory']
            ->createBuilder()
            ->getForm();

        if (is_null($Order)) {
            throw new HttpException\NotFoundHttpException('order is not found.');
        }

        return $app['view']->render('Admin/Order/edit.twig', array(
                'form' => $form->createView(),
                'title' => '受注管理',
                'sub_title' => '受注編集',
        ));
    }
} 