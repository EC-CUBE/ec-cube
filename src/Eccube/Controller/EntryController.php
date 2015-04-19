<?php

namespace Eccube\Controller;

use Eccube\Application;
use Eccube\Framework\Util\Utils;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntryController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = '会員登録';

    }

    public function kiyaku(Application $app)
    {
        $app['session']->remove('entry');
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

    public function index(Application $app)
    {
        $customer = $app['eccube.repository.customer']->newCustomer();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('customer', $customer);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                switch ($app['request']->get('mode')) {
                    case 'confirm' :
                        $builder->setAttribute('freeze', true);
                        $form = $builder->getForm();
                        $form->handleRequest($app['request']);

                        return $app['twig']->render('Entry/confirm.twig', array(
                            'title' => $this->title,
                            'form' => $form->createView(),
                        ));
                        break;
                    case 'complete':

                        $customer->setSecretKey($this->getUniqueSecretKey($app));

                        // secpassword
                        $generator = new SecureRandom();
                        $salt = bin2hex($generator->nextBytes(10));
                        $customer->setSalt($salt);
                        $encoder = $app['security.encoder_factory']->getEncoder($customer);
                        $encoded_password = $encoder->encodePassword($customer->getPassword(), $customer->getSalt());
                        $customer->setPassword($encoded_password);

                        $activateUrl = $app['url_generator']->generate('entry_activate')
                            . '?id=' . $customer->getSecretKey();

                        $app['orm.em']->persist($customer);
                        $app['orm.em']->flush();

                        if ($app['config']['CUSTOMER_CONFIRM_MAIL']) {

                            $app['mail.message']
                                ->setSubject('[EC-CUBE3] 会員登録のご確認')
                                ->setBody('認証URL：' . $activateUrl);

                            $this->sendMail($app, $customer);

                            return $app->redirect($app['url_generator']->generate('entry_complete'));

                        } else {
                            return $app->redirect($activateUrl);
                        }

                        break;
                }
            }
        }

        return $app['view']->render('Entry/index.twig', array(
            'title' => $this->title,
            'form' => $form->createView(),
        ));
    }


    public function complete(Application $app)
    {

        return $app['view']->render('Entry/complete.twig', array(
            'title' => $this->title,
        ));
    }

    public function activate(Application $app)
    {

        //--　本登録完了のためにメールから接続した場合の処理
        if ($app['request']->getMethod() === 'GET') {
            //-- 入力チェック
            // シークレットキーからユーザーを取得
            $secret_key = $app['request']->get('id');
            $customer = $app['orm.em']->getRepository('Eccube\\Entity\\Customer')
                ->findOneBy(array(
                        'secret_key' => $secret_key,
                        'status' => 1,
                        'del_flg' => 0,
                    )
                );
            if (!$customer) {
                throw new NotFoundHttpException('※ 既に会員登録が完了しているか、無効なURLです。');
            }
            $customer->setStatus(2);
            $app['orm.em']->persist($customer);
            $app['orm.em']->flush();

            $app['mail.message']
                ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                ->setBody('会員登録が完了しました。');

            $this->sendMail($app, $customer);

            return $app['view']->render('Entry/activate.twig', array(
                'title' => $this->title,
            ));
        } else {

            return 'error';//--　それ以外のアクセスは無効としてエラーメッセージをだす
        }
    }

    // ユニークなキーを取得する
    private function getUniqueSecretKey($app)
    {
        $unique = md5(uniqid(rand(), 1));
        $customer = $app['eccube.repository.customer']->findBy(array(
            'secret_key' => $unique,
        ));
        if (count($customer) == 0) {
            return $unique;
        } else {
            return $this->getUniqueSecretKey($app);
        }
    }

    private function sendMail(Application $app, $customer)
    {
        // TODO: 後でEventとして実装する
        // $app['eccube.event.dispatcher']->dispatch('customer.regist::after');
        $message = $app['mail.message']
            ->setFrom(array('sample@example.com'))
            ->setCc($app['config']['mail_cc'])
            ->setTo(array($customer->getEmail()));
        $app['mailer']->send($message);
    }

}