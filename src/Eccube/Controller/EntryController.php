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
        $form = $this->getBoundForm($app, 'customer');
        // $form = $app['form.factory']
        //     ->createBuilder(new \Eccube\Form\Type\CustomerType(), new \Eccube\Entity\Customer())
        //     ->getForm();
        // $form->handleRequest($app['request']);

        return $app['twig']->render('Entry/index.twig', array(
            'title' => $this->title,
            'form' => $form->createView(),
        ));
    }

    public function Confirm(Application $app)
    {
        $this->redirectKiyakuPage($app);
        $form = $this->getBoundForm($app, 'customer');

        if ($form->isValid()) {

            return $app['twig']->render('Entry/confirm.twig', array(
                'title' => $this->title,
                'form' => $form->createView(),
            ));

        } else {

            return $this->Index($app);

        }
    }

    public function Complete(Application $app)
    {
        $this->redirectKiyakuPage($app);
        $form = $this->getBoundForm($app, 'customer');

        if ($form->isValid()) {

            $data = $form->getData();
            $data->setSecretKey(uniqid())
                ->setCreateDate(new \DateTime())
                ->setUpdateDate(new \DateTime())
                ->setPoint(0)
                ->setStatus(1)
                ->setDelFlg(0);

            $app['orm.em']->persist($data);
            $app['orm.em']->flush();

            $message = $app['mail.message']
                ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                ->setFrom(array('sample@example.com'))
                ->setCc(array('shinichi_takahashi@lockon.co.jp'))
                ->setTo(array($data->getEmail()))
                ->setBody('会員登録が完了しました。');

            $app['mailer']->send($message);

            return $app['twig']->render('Entry/complete.twig', array(
                'title' => $this->title,
            ));

        } else {

            return $this->Index($app);

        }

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