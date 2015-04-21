<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception as HttpException;

class EntryController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = '会員登録';

    }

    /**
     * Index
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
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

                        // create Secretkey
                        $customer->setSecretKey($this->getUniqueSecretKey($app));

                        // secure password
                        $generator = new SecureRandom();
                        $salt = bin2hex($generator->nextBytes(5));
                        $customer->setSalt($salt);
                        $encoder = $app['security.encoder_factory']->getEncoder($customer);
                        $encoded_password = $encoder->encodePassword($customer->getPassword(), $customer->getSalt());
                        $customer->setPassword($encoded_password);

                        $activateUrl = $app['url_generator']
                            ->generate('entry_activate',
                                array('id' => $customer->getSecretKey()),
                                true
                            );

                        $app['orm.em']->persist($customer);
                        $app['orm.em']->flush();

                        if ($app['config']['customer_confirm_mail']) {

                            $message = $app['mail.message']
                                ->setSubject('[EC-CUBE3] 会員登録のご確認')
                                ->setBody($app['view']->render('Mail/entry_confirm.twig', array(
                                        'customer' => $customer,
                                        'activateUrl' => $activateUrl,
                                    )));

                            $this->sendMail($app, $customer, $message);

                            return $app->redirect($app['url_generator']->generate('entry_complete'));

                        } else {
                            return $app->redirect($activateUrl);
                        }

                        break;
                }
            }
        }

        $kiyaku = $app['orm.em']
            ->getRepository('Eccube\Entity\Kiyaku')
            ->findAll();
        return $app['view']->render('Entry/index.twig', array(
            'title' => $this->title,
            'kiyaku' => $kiyaku,
            'form' => $form->createView(),
        ));
    }


    /**
     * Complete
     *
     * @param Application $app
     * @return mixed
     */
    public function complete(Application $app)
    {

        return $app['view']->render('Entry/complete.twig', array(
            'title' => $this->title,
        ));
    }

    /**
     * 会員のアクティベート（本会員化）を行う
     *
     * @param Application $app
     * @return mixed
     */
    public function activate(Application $app)
    {

        $secret_key = $app['request']->get('id');
        $errors = $app['validator']->validateValue($secret_key, array(
                new Assert\NotBlank(),
                new Assert\Regex(array(
                    'pattern' => '/^[a-zA-Z0-9]+$/',
                ))
            )
        );

        if ($app['request']->getMethod() === 'GET' && count($errors) <= 0) {

            $customer = $app['orm.em']->getRepository('Eccube\\Entity\\Customer')
                ->findOneBy(array(
                        'secret_key' => $secret_key,
                        'status' => 1,
                        'del_flg' => 0,
                    )
                );

            if (!$customer) {
                throw new HttpException\NotFoundHttpException('※ 既に会員登録が完了しているか、無効なURLです。');
            }

            $customer->setStatus(2);
            $app['orm.em']->persist($customer);
            $app['orm.em']->flush();

            $message = $app['mail.message']
                ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                ->setBody($app['view']->render('Mail/entry_complete.twig', array(
                    'customer' => $customer,
                )));

            $this->sendMail($app, $customer, $message);

            return $app['view']->render('Entry/activate.twig', array(
                'title' => $this->title,
            ));
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
        }
    }

    /**
     * 認証用の会員毎にユニークなキーを生成・取得する
     *
     * @param $app
     * @return string
     */
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

    /**
     * 顧客に確認メールを送信する
     *
     * @param Application $app
     * @param $customer
     * @param $message
     */
    private function sendMail(Application $app, $customer, $message)
    {
        // TODO: 後でEventとして実装する、送信元アドレス、BCCを調整する
        // $app['eccube.event.dispatcher']->dispatch('customer.regist::after');
        $message->setFrom(array('sample@example.com'))
            ->setBcc($app['config']['mail_cc'])
            ->setTo(array($customer->getEmail()));
        $app['mailer']->send($message);
    }

}