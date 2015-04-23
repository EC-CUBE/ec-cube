<?php

namespace Eccube\Controller\MyPage;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ChangeController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = 'MYページ';

    }

    /**
     * Index
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app)
    {
       $Customer = $app['orm.em']->getRepository('Eccube\\Entity\\Customer')
            ->findOneBy(array(
                    'id' => $app['user']->getId(),
                )
            );

        $previous_password = $Customer->getPassword();
        $Customer->setPassword($app['config']['default_password']);

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('entry', $Customer);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                switch ($app['request']->get('mode')) {
                    case 'complete':

                        if ( $Customer->getPassword() === $app['config']['default_password']) {
                            $Customer->setPassword($previous_password);
                        } else {
                            $Customer->setPassword(
                                $app['orm.em']
                                    ->getRepository('Eccube\Entity\Customer')
                                    ->encryptPassword($app, $Customer)
                            );
                        }

                        $app['orm.em']->persist($Customer);
                        $app['orm.em']->flush();

                        return $app->redirect($app['url_generator']->generate('mypage_change_complete'));
                        break;
                }
            }
        }

        return $app['twig']->render('MyPage/change.twig', array(
            'title' => $this->title,
            'subtitle' => '会員登録内容変更(入力ページ)',
            'mypageno' => 'change',
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

        return $app['view']->render('MyPage/change_complete.twig', array(
            'title' => $this->title,
            'subtitle' => '会員登録内容変更(完了ページ)',
            'mypageno' => 'change',
        ));
    }

}