<?php
namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class AgreementController extends AbstractController
{
    private $main_title;
    
    private $title;
    
    public $form;
    
    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->title = '会員規約設定';
    }
    
    public function index(Application $app)
    {
        $Kiyakus = $app['orm.em']->getRepository("Eccube\Entity\Kiyaku")->findAll();
        

//        $form = $app['form.factory']->createBuilder('agreement', $kiyaku)->getForm();
//        if ($app['request']->getMethod() === 'POST') {
//            $form->handleRequest($app['request']);
//            if ($form->isValid()) {
//                $data = $form->getData();
//                $app['orm.em']->persist($data);
//                $app['orm.em']->flush();
//                return $app->redirect($app['url_generator']->generate('admin_basis_agreement'));
//            }
//        }

        return $app['view']->render('Admin/Basis/agreement.twig', array(
            'main_title' => $this->main_title,
            'title' => $this->title,
            //'form' => $form->createView(),
            'Kiyakus' => $Kiyakus,
        ));
    }
}