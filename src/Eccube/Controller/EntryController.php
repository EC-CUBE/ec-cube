<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\Validator\Constraints as Assert;

class EntryController
{
    private $title;

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
        // 規約確認
        $referer = parse_url($app['request']->headers->get('referer'));

        $kiyakuUrl = $app['url_generator']->generate('entry_kiyaku');
        $entryUrl = $app['url_generator']->generate('entry');
        
        if (!in_array($referer['path'], array($kiyakuUrl, $entryUrl))) {
            return $app->redirect($kiyakuUrl);
        }

        $form = $app['form.factory']
            ->createBuilder(new \Eccube\FormType\CustomerType(), new \Eccube\Entity\Customer())
            ->getForm();

        $form->handleRequest($app['request']);

        // 戻るボタン時 or validate error時
        if ($app['request']->get('back') || !$form->isValid()) {
            return $app['twig']->render('Entry/index.twig', array(
                'title' => $this->title,
                'form' => $form->createView(),
            ));
        }

        if ($form->isValid()) {
            if (!$app['request']->get('confirm')) {
                // 確認画面へ
                return $app['twig']->render('Entry/confirm.twig', array(
                    'title' => 'かくにん',
                    'form' => $form->createView()
                ));
            }

            $data = $form->getData();
            $message = $app['mail.message']
                ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                ->setFrom(array('sample@example.com'))
                ->setCc(array('shinichi_takahashi@lockon.co.jp'))
                ->setTo(array($data->getEmail()))
                ->setBody('会員登録が完了しました。');
            $app['mailer']->send($message);

            $data
                ->setSecretKey(uniqid())
                ->setCreateDate(new \DateTime())
                ->setUpdateDate(new \DateTime())
                ->setPoint(0)
                ->setStatus(1)
                ->setDelFlg(0);

            $app['orm.em']->persist($data);
            $app['orm.em']->flush();

            return $app->redirect( $app['url_generator']->generate('entry_complete'));
        }
    }

    public function Complete(Application $app)
    {
        return $app['twig']->render('Entry/complete.twig', array(
            'title' => $this->title,
        ));
    }

}