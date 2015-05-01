<?php

namespace Eccube\Controller\Mypage;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DeliveryController extends AbstractController
{
    private $title;

    public function __construct()
    {
        $this->title = 'MYページ';
    }

    /**
     * Index
     *
     * @param  Application $app
     * @return string
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['user'];

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('form', $Customer);
        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'delete':
                        // 別のお届け先削除
                        if (!$app['eccube.repository.other_deliv']->deleteByCustomerAndId($Customer, $request->get('other_deliv_id'))) {
                            $app['session']->getFlashBag()->set('error', '別のお届け先を削除できませんでした。');
                        }

                        return $app->redirect($app['url_generator']->generate('mypage_delivery'));
                        break;
                }
            }
        }

        return $app['twig']->render('Mypage/delivery.twig', array(
            'title' => $this->title,
            'subtitle' => 'お届け先追加･変更',
            'mypageno' => 'delivery',
            'form' => $form->createView(),
            'Customer' => $Customer,
        ));
    }

    /**
     * Complete
     *
     * @param  Application $app
     * @return mixed
     */
    public function address(Application $app, Request $request)
    {
        $Customer = $app['user'];

        $OtherDeliv = $app['eccube.repository.other_deliv']->findOrCreateByCustomerAndId($Customer, $request->get('other_deliv_id', null));

        $parentPage = $request->get('parent_page', null);

        // 正しい遷移かをチェック
        $allowdParents = array(
            $app['url_generator']->generate('mypage_delivery'),
            $app['url_generator']->generate('shopping_delivery'),
            $app['url_generator']->generate('shopping_shipping_multiple'),
        );

        // 遷移が正しくない場合、デフォルトであるマイページの配送先追加の画面を設定する
        if (!in_array($parentPage, $allowdParents)) {
            $parentPage  = $app['url_generator']->generate('mypage_delivery');
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('other_deliv', $OtherDeliv);
        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $app['orm.em']->persist($OtherDeliv);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->set('mypage_delivery_address.just_added', $OtherDeliv->getId());

                return $app->redirect($app['url_generator']->generate('mypage_delivery_address', array('other_deliv_id' => $OtherDeliv->getId())));
            }
        }

        $BaseInfo = $app['eccube.repository.base_info']->get();

        return $app['view']->render('Mypage/delivery_address.twig', array(
            'title' => 'お届け先の追加･変更',
            'parentPage' => $parentPage,
            'form' => $form->createView(),
            'BaseInfo' => $BaseInfo,
        ));
    }

}
