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
        $Kiyaku = new \Eccube\Entity\Kiyaku();
        if ($kiyakuId) {
            $Kiyaku = $app['orm.em']->getRepository("Eccube\Entity\Kiyaku")->find($kiyakuId);
        }        

        $form = $app['form.factory']->createBuilder('agreement', $Kiyaku)->getForm();

        return $app['view']->render('Admin/Basis/agreement.twig', array(
            'main_title' => $this->main_title,
            'title' => $this->title,
            'form' => $form->createView(),
            'Kiyakus' => $Kiyakus,
            'Kiyaku' => $Kiyaku,
        ));
    }
    
    public function edit(Application $app, $kiyakuId = null){
        $Kiyakus = $app['orm.em']->getRepository("Eccube\Entity\Kiyaku")->findAll();
        if ($kiyakuId) {
            $Kiyaku = $app['orm.em']->getRepository("Eccube\Entity\Kiyaku")->find($kiyakuId);
        } else {
            $Member = $app['orm.em']->getRepository("Eccube\Entity\Member")->find(1);
            $Kiyaku = new \Eccube\Entity\Kiyaku();
            $Kiyaku->setRank(100)
                ->setCreator($Member)
                ->setCreateDate(new \DateTime())
                ->setUpdateDate(new \DateTime())
                ->setDelFlg(0);
        }
        
        $form = $app['form.factory']->createBuilder('agreement', $Kiyaku)->getForm();
        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $app['orm.em']->persist($data);
                $app['orm.em']->flush();
            }
        }
        return $app['view']->render('Admin/Basis/agreement.twig', array(
            'main_title' => $this->main_title,
            'title' => $this->title,
            'form' => $form->createView(),
            'Kiyakus' => $Kiyakus,
            'Kiyaku' => $Kiyaku,
        ));
    }
        
}