<?php

namespace Eccube\Controller\Mypage;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RefusalController extends AbstractController
{
    private $title;

    public function __construct()
    {
        $this->title = 'MYページ';
    }

    /**
     * Index
     *
     * @param  Application                                        $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app, Request $request)
    {
        /* @var $Customer \Eccube\Entity\Customer */
        $Customer = $app['user'];

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('form');

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'confirm':
                        return $app['twig']->render('Mypage/refusal_confirm.twig', array(
                            'title' => $this->title,
                            'subtitle' => '退会手続き(確認ページ)',
                            'mypageno' => 'refusal',
                            'form' => $form->createView(),
                        ));
                        break;
                    case 'complete':
                        // 顧客削除
                        $Customer->setDelFlg(1);
                        $app['orm.em']->persist($Customer);
                        $app['orm.em']->flush();

                        $BaseInfo = $app['eccube.repository.base_info']->get();

                        // TODO: 後でEventとして実装する、送信元アドレス、BCCを調整する
                        // $app['eccube.event.dispatcher']->dispatch('customer.refusal::after');
                        $message = $app['mailer']->createMessage()
                            ->setSubject('[EC-CUBE3] 退会手続きのご完了')
                            ->setBody($app['view']->render('Mail/customer_refusal_mail.twig', array(
                                'BaseInfo' => $BaseInfo,
                                'Customer' => $Customer,
                            )))
                            ->setFrom($BaseInfo->getEmail03())
                            ->setBcc($app['config']['mail_cc'])
                            ->setTo(array($Customer->getEmail()));
                        $app['mailer']->send($message);

                        // ログアウト
                        $app['security']->setToken(null);

                        return $app->redirect($app['url_generator']->generate('mypage_refusal_complete'));
                        break;
                }
            }
        }

        return $app['twig']->render('Mypage/refusal.twig', array(
            'title' => $this->title,
            'subtitle' => '退会手続き(入力ページ)',
            'mypageno' => 'refusal',
            'form' => $form->createView(),
        ));
    }

    /**
     * Complete
     *
     * @param  Application $app
     * @return mixed
     */
    public function complete(Application $app, Request $request)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();

        return $app['view']->render('Mypage/refusal_complete.twig', array(
            'title' => $this->title,
            'subtitle' => '退会手続き(完了ページ)',
            'mypageno' => 'refusal',
            'BaseInfo' => $BaseInfo,
        ));
    }

}
