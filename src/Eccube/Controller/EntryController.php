<?php

namespace Eccube\Controller;

use Eccube\Application;

class EntryController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = '会員登録';

    }

    public function Kiyaku(Application $app)
    {
        $kiyaku = '規約内容を取得して表示';
        // TODO: 規約内容を取得
        // $kiyaku = $app['orm.em']
        //     ->getRepository('Eccube\Entity\Kiyaku')
        //     ->findAll();

        $form = $app['form.factory']->createBuilder()->getForm();

        return $app['twig']->render('Entry/kiyaku.twig', array(
            'title' => $this->title,
            'kiyaku' => $kiyaku,
            'form' => $form->createView(),
        ));
    }

    public function Index(Application $app)
    {
        $this->redirectKiyakuPage($app);

        $customer = $app['eccube.repository.customer']->newCustomer();

        $form = $app['form.factory']
            ->createBuilder('customer', $customer)
            ->getForm();
        $form->handleRequest($app['request']);

        if ($app['request']->getMethod() === 'POST' && $form->isValid()) {
            
            switch ($app['request']->get('mode')) {
                case 'confirm' :
                    return $app['twig']->render('Entry/confirm.twig', array(
                        'title' => $this->title,
                        'form' => $form->createView(),
                    ));
                    break;
                case 'complete':
                    $app['orm.em']->persist($customer);
                    $app['orm.em']->flush();

                    // TODO: 後でEventとして実装する
                    // $app['eccube.event.dispatcher']->dispatch('customer.regist::after');
                    $message = $app['mail.message']
                        ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                        ->setFrom(array('sample@example.com'))
                        ->setCc(array('shinichi_takahashi@lockon.co.jp'))
                        ->setTo(array($customer->getEmail()))
                        ->setBody('会員登録が完了しました。');
                    $app['mailer']->send($message);

                    return $app->redirect($app['url_generator']->generate('entry_complete'));
                    break;
            }
        }

        return $app['twig']->render('Entry/index.twig', array(
            'title' => $this->title,
            'form' => $form->createView(),
        ));
    }

    public function Complete(Application $app)
    {
        return $app['twig']->render('Entry/complete.twig', array(
            'title' => $this->title,
        ));
    }

    // 規約画面からの遷移でない場合、規約ページへリダイレクト
    private function redirectKiyakuPage($app)
    {
        // 規約確認
        $referer = parse_url($app['request']->headers->get('referer'));

        $kiyakuUrl = $app['url_generator']->generate('entry_kiyaku');
        $indexUrl = $app['url_generator']->generate('entry');
        $confirmUrl = $app['url_generator']->generate('entry_confirm');
        
        if (!in_array($referer['path'], array($kiyakuUrl, $indexUrl, $confirmUrl))) {
            return $app->redirect($kiyakuUrl);
        }
    }

}