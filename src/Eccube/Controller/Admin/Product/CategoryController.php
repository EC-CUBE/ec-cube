<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller\Admin\Product;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CategoryType;
use Eccube\Repository\CategoryRepository;
use Eccube\Service\CsvExportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=CategoryController::class)
 */
class CategoryController extends AbstractController
{
    /**
     * @Inject(CsvExportService::class)
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(CategoryRepository::class)
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @Route("/{_admin}/product/category", name="admin_product_category")
     * @Route("/{_admin}/product/category/{parent_id}", requirements={"parent_id" = "\d+"}, name="admin_product_category_show")
     * @Route("/{_admin}/product/category/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_category_edit")
     * @Template("Product/category.twig")
     */
    public function index(Application $app, Request $request, $parent_id = null, $id = null)
    {
        if ($parent_id) {
            $Parent = $this->categoryRepository->find($parent_id);
            if (!$Parent) {
                throw new NotFoundHttpException('親カテゴリが存在しません');
            }
        } else {
            $Parent = null;
        }
        if ($id) {
            $TargetCategory = $this->categoryRepository->find($id);
            if (!$TargetCategory) {
                throw new NotFoundHttpException('カテゴリが存在しません');
            }
            $Parent = $TargetCategory->getParent();
        } else {
            $TargetCategory = new \Eccube\Entity\Category();
            $TargetCategory->setParent($Parent);
            if ($Parent) {
                $TargetCategory->setLevel($Parent->getLevel() + 1);
            } else {
                $TargetCategory->setLevel(1);
            }
        }

        //
        $builder = $this->formFactory
            ->createBuilder(CategoryType::class, $TargetCategory);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Parent' => $Parent,
                'TargetCategory' => $TargetCategory,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        //
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($this->appConfig['category_nest_level'] < $TargetCategory->getLevel()) {
                    throw new BadRequestHttpException('リクエストが不正です');
                }
                log_info('カテゴリ登録開始', array($id));
                $status = $this->categoryRepository->save($TargetCategory);

                if ($status) {

                    log_info('カテゴリ登録完了', array($id));

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Parent' => $Parent,
                            'TargetCategory' => $TargetCategory,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE, $event);

                    $app->addSuccess('admin.category.save.complete', 'admin');

                    if ($Parent) {
                        return $app->redirect($app->url('admin_product_category_show', array('parent_id' => $Parent->getId())));
                    } else {
                        return $app->redirect($app->url('admin_product_category'));
                    }
                } else {
                    log_info('カテゴリ登録エラー', array($id));
                    $app->addError('admin.category.save.error', 'admin');
                }
            }
        }

        $Categories = $this->categoryRepository->getList($Parent);

        // ツリー表示のため、ルートからのカテゴリを取得
        $TopCategories = $this->categoryRepository->getList(null);

        return [
            'form' => $form->createView(),
            'Parent' => $Parent,
            'Categories' => $Categories,
            'TopCategories' => $TopCategories,
            'TargetCategory' => $TargetCategory,
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/product/category/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_category_delete")
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetCategory = $this->categoryRepository->find($id);
        if (!$TargetCategory) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_product_category'));
        }
        $Parent = $TargetCategory->getParent();

        log_info('カテゴリ削除開始', array($id));

        $status = $this->categoryRepository->delete($TargetCategory);

        if ($status === true) {

            log_info('カテゴリ削除完了', array($id));

            $event = new EventArgs(
                array(
                    'Parent' => $Parent,
                    'TargetCategory' => $TargetCategory,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.category.delete.complete', 'admin');
        } else {
            log_info('カテゴリ削除エラー', array($id));
            $app->addError('admin.category.delete.error', 'admin');
        }

        if ($Parent) {
            return $app->redirect($app->url('admin_product_category_show', array('parent_id' => $Parent->getId())));
        } else {
            return $app->redirect($app->url('admin_product_category'));
        }
    }

    /**
     * @Method("POST")
     * @Route("/{_admin}/product/category/rank/move", name="admin_product_category_rank_move")
     */
    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $categoryId => $rank) {
                /* @var $Category \Eccube\Entity\Category */
                $Category = $this->categoryRepository
                    ->find($categoryId);
                $Category->setRank($rank);
                $this->entityManager->persist($Category);
            }
            $this->entityManager->flush();
        }
        return true;
    }


    /**
     * カテゴリCSVの出力.
     *
     * @Route("/{_admin}/product/category/export", name="admin_product_category_export")
     *
     * @param Application $app
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Application $app, Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_CATEGORY);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            $qb = $this->categoryRepository
                ->createQueryBuilder('c')
                ->orderBy('c.rank', 'DESC');

            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($app, $request) {

                $Csvs = $csvService->getCsvs();

                /** @var $Category \Eccube\Entity\Category */
                $Category = $entity;

                // CSV出力項目と合致するデータを取得.
                $ExportCsvRow = new \Eccube\Entity\ExportCsvRow();
                foreach ($Csvs as $Csv) {
                    $ExportCsvRow->setData($csvService->getData($Csv, $Category));

                    $event = new EventArgs(
                        array(
                            'csvService' => $csvService,
                            'Csv' => $Csv,
                            'Category' => $Category,
                            'ExportCsvRow' => $ExportCsvRow,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_CSV_EXPORT, $event);

                    $ExportCsvRow->pushData();
                }

                //$row[] = number_format(memory_get_usage(true));
                // 出力.
                $csvService->fputcsv($ExportCsvRow->getRow());
            });
        });

        $now = new \DateTime();
        $filename = 'category_' . $now->format('YmdHis') . '.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->send();

        log_info('カテゴリCSV出力ファイル名', array($filename));

        return $response;
    }
}
