<?php

namespace Eccube\Controller\Admin\Design;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class DesignController
{
    public $title;

    public $device_type_id;

    public function __construct()
    {
        $this->title = 'レイアウト管理';
    }

    public function index(Application $app, $pageId = 1)
    {
        $this->device_type_id = $app['config']['device_type_pc'];

        // 一覧表示用
        $PageLayouts = $app['orm.em']->getRepository('\Eccube\Entity\PageLayout')
            ->findBy(array(
                'device_type_id' => $this->device_type_id,
            ));

        $query = $app['orm.em']->createQueryBuilder()
            ->select('t')
            ->from('\Eccube\Entity\Master\Target', 't', 't.id')
            ->getQuery();
        $Target = $query->getResult();

        // 編集ページ情報の取得
        // bloc_rowでソートしたいため、find()ではなくQueryBuilderを使う
        $query = $app['orm.em']->createQueryBuilder()
            ->select('pl', 'bp', 'b')
            ->from('\Eccube\Entity\PageLayout', 'pl')
            ->leftJoin('pl.BlocPositions', 'bp')
            ->leftJoin('bp.Bloc', 'b')
            ->orderby('bp.bloc_row', 'ASC')
            ->andWhere('pl.device_type_id = :device_type_id')
            ->setParameter('device_type_id', $this->device_type_id)
            ->andWhere('pl.page_id = :page_id')
            ->setParameter('page_id', $pageId)
            ->getQuery();
        $Layout = $query->getSingleResult();

        // 全ページ適用データの取得
        $allPageBlocPositions = $app['orm.em']->getRepository('\Eccube\Entity\BlocPosition')
            ->findBy(array(
                'device_type_id' => $this->device_type_id,
                'anywhere' => 1,
            ));
        foreach ($allPageBlocPositions as $allPageBlocPosition) {
            if ($allPageBlocPosition->getPageId() != $pageId) {
                $Layout->addBlocPosition($allPageBlocPosition);
            }
        }
        // 未使用ブロックの取得
        $Blocs = $app['orm.em']->getRepository('\Eccube\Entity\Bloc')
            ->findBy(array(
                'device_type_id' => $this->device_type_id,
            ));
        $BlocPositions = $Layout->getBlocPositions();
        $usedBlocIds = array();
        foreach ($BlocPositions as $BlocPosition) {
            $usedBlocIds[] = $BlocPosition->getBlocId();
        }
        foreach ($Blocs as $Bloc) {
            if (!in_array($Bloc->getBlocId(), $usedBlocIds)) {
                $UnuseBlocPositions = new \Eccube\Entity\BlocPosition();
                $UnuseBlocPositions
                    ->setDeviceTypeId($this->device_type_id)
                    ->setPageId($pageId)
                    ->setTargetId($app['config']['target_id_unused'])
                    ->setAnywhere(0)
                    ->setBlocRow(0)
                    ->setBlocId($Bloc->getBlocId())
                    ->setBloc($Bloc)
                    ->setPageLayout($Layout);
                $Layout->addBlocPosition($UnuseBlocPositions);
            }
        }

        $form = $app['form.factory']
            ->createBuilder()
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                // 消す
                $blocCount = count($BlocPositions);

                foreach ($BlocPositions as $BlocPosition) {
                    if ($BlocPosition->getPageId() == $pageId || $BlocPosition->getAnywhere() == 0) {
                        $Layout->removeBlocPosition($BlocPosition);
                        $app['orm.em']->remove($BlocPosition);
                    }
                }
                $app['orm.em']->flush();

                $TargetHash = $this->getTragetHash($Target);

                // TODO: collection を利用
                $data = $app['request']->request->all();
                for ($i = 1; $i <= $blocCount; $i++) {
                    // bloc_id が取得できない場合は INSERT しない
                    if (!isset($data['id_' . $i])) {
                        continue;
                    }
                    // 未使用は INSERT しない
                    if ($TargetHash[$data['target_id_' . $i]] === $app['config']['target_id_unused']) {
                        continue;
                    }
                    // 他のページに anywhere が存在する場合は INSERT しない
                    $anywhere = (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) ? 1 : 0;
                    if (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) {
                        $Other = $app['orm.em']->getRepository('\Eccube\Entity\BlocPosition')
                            ->findBy(array(
                                'anywhere' => 1,
                                'bloc_id' => $data['id_' . $i],
                                'device_type_id' => $this->device_type_id,
                            ));
                        if (count($Other) > 0) {
                            continue;
                        }
                    }

                    $BlocPosition = new \Eccube\Entity\BlocPosition();
                    $Bloc = $app['orm.em']->getRepository('\Eccube\Entity\Bloc')
                        ->findOneBy(array(
                            'bloc_id' => $data['id_' . $i],
                            'device_type_id' => $this->device_type_id,
                        ));
                    $BlocPosition
                        ->setDeviceTypeId($this->device_type_id)
                        ->setPageId($pageId)
                        ->setBlocId($data['id_' . $i])
                        ->setBlocRow($data['top_' . $i])
                        ->setTargetId($TargetHash[$data['target_id_' . $i]])
                        ->setBloc($Bloc)
                        ->setPageLayout($Layout)
                        ->setAnywhere($anywhere);
                    if ($pageId == 0) {
                        $BlocPosition->setAnywhere(0);
                    }
                    $Layout->addBlocPosition($BlocPosition);
                }

                $app['orm.em']->persist($Layout);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('design.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_design_edit', array('pageId' => $pageId)));
            }

        }

        return $app['view']->render('Admin/Design/index.twig', array(
            'form' => $form->createView(),
            'title' => $this->title,
            'tpl_maintitle' => 'デザイン管理＞レイアウト管理',
            'PageLayouts' => $PageLayouts,
            'Layout' => $Layout,
            'Target' => $Target,
        ));
    }

    public function getTragetHash($Targets)
    {
        $TargetHash = array();
        foreach ($Targets as $key => $Target) {
            $TargetHash[$Target->getName()] = $key;
        }

        return $TargetHash;
    }
}
