<?php

namespace Eccube\Controller;

use Eccube\Application;
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
        $Customer = $app['eccube.repository.customer']->newCustomer();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('entry', $Customer);

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


                        $Customer->setSalt(
                            $app['orm.em']
                                ->getRepository('Eccube\Entity\Customer')
                                ->createSalt(5)
                        );

                        $Customer->setPassword(
                            $app['orm.em']
                                ->getRepository('Eccube\Entity\Customer')
                                ->encryptPassword($app, $Customer)
                        );

                        $Customer->setSecretKey(
                            $app['orm.em']
                                ->getRepository('Eccube\Entity\Customer')
                                ->getUniqueSecretKey($app)
                        );


                        $app['orm.em']->persist($Customer);
                        $app['orm.em']->flush();

                        $activateUrl = $app['url_generator']
                            ->generate('entry_activate', array(
                                'id' => $Customer->getSecretKey()
                            ), true);

                        if ($app['config']['customer_confirm_mail']) {

                            $message = $app['mail.message']
                                ->setSubject('[EC-CUBE3] 会員登録のご確認')
                                ->setBody($app['view']->render('Mail/entry_confirm.twig', array(
                                    'Customer' => $Customer,
                                    'activateUrl' => $activateUrl,
                                )));

                            $this->sendMail($app, $Customer, $message);

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

            $Customer = $app['orm.em']->getRepository('Eccube\\Entity\\Customer')
                ->getNonActiveCustomerBySecretKey($secret_key);

            if (!$Customer) {
                throw new HttpException\NotFoundHttpException('※ 既に会員登録が完了しているか、無効なURLです。');
            }

            $Customer->setStatus(
                $app['orm.em']
                    ->getRepository('Eccube\Entity\Master\CustomerStatus')
                    ->find(2)
            );
            $app['orm.em']->persist($Customer);
            $app['orm.em']->flush();

            $message = $app['mail.message']
                ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                ->setBody($app['view']->render('Mail/entry_complete.twig', array(
                    'customer' => $Customer,
                )));

            $this->sendMail($app, $Customer, $message);

            return $app['view']->render('Entry/activate.twig', array(
                'title' => $this->title,
            ));
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
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