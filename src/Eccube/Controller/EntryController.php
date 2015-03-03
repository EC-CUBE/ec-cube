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
            return $app->redirect('kiyaku.php');
        }

        $form = $app['form.factory']->createBuilder('form', new \Eccube\Entity\Customer)
            ->add('name01', 'text')
            ->add('name02', 'text')
            ->add('kana01', 'text')
            ->add('kana02', 'text')
            ->add('email', 'email')
            ->add('password', 'password')
            ->getForm();

        $form->handleRequest($app['request']);

        if ($form->isValid()) {
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

            return $app->redirect('complete.php');
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

}