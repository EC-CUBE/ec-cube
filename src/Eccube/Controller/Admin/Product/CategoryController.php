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

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CategoryType;
use Eccube\Repository\CategoryRepository;
use Eccube\Service\CsvExportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(service=CategoryController::class)
 */
class CategoryController extends AbstractController
{
    /**
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * CategoryController constructor.
     *
     * @param CsvExportService $csvExportService
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        CsvExportService $csvExportService,
        CategoryRepository $categoryRepository
    ) {
        $this->csvExportService = $csvExportService;
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * @Route("/%eccube_admin_route%/product/category", name="admin_product_category")
     * @Route("/%eccube_admin_route%/product/category/{parent_id}", requirements={"parent_id" = "\d+"}, name="admin_product_category_show")
     * @Route("/%eccube_admin_route%/product/category/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_category_edit")
     * @Template("@admin/Product/category.twig")
     */
    public function index(Request $request, $parent_id = null, $id = null)
    {
        if ($parent_id) {
            $Parent = $this->categoryRepository->find($parent_id);
            if (!$Parent) {
                throw new NotFoundHttpException(trans('category.text.error.no_parent_category'));
            }
        } else {
            $Parent = null;
        }
        if ($id) {
            $TargetCategory = $this->categoryRepository->find($id);
            if (!$TargetCategory) {
                throw new NotFoundHttpException(trans('category.text.error.no_category'));
            }
            $Parent = $TargetCategory->getParent();
        } else {
            $TargetCategory = new \Eccube\Entity\Category();
            $TargetCategory->setParent($Parent);
            if ($Parent) {
                $TargetCategory->setHierarchy($Parent->getHierarchy() + 1);
            } else {
                $TargetCategory->setHierarchy(1);
            }
        }

        $Categories = $this->categoryRepository->getList($Parent);

        // ツリー表示のため、ルートからのカテゴリを取得
        $TopCategories = $this->categoryRepository->getList(null);

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

        $forms = [];
        foreach ($Categories as $Category) {
            $forms[$Category->getId()] = $this->formFactory
                ->createNamed('category_' . $Category->getId(), CategoryType::class, $Category);
        }

        //
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($this->eccubeConfig['eccube_category_nest_level'] < $TargetCategory->getHierarchy()) {
                    throw new BadRequestHttpException(trans('category.text.error.invalid_requesy'));
                }
                log_info('カテゴリ登録開始', array($id));

                $this->categoryRepository->save($TargetCategory);

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

                $this->addSuccess('admin.category.save.complete', 'admin');
                if ($Parent) {
                    return $this->redirectToRoute('admin_product_category_show', array('parent_id' => $Parent->getId()));
                } else {
                    return $this->redirectToRoute('admin_product_category');
                }
            }

            foreach ($forms as $editForm) {
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->categoryRepository->save($editForm->getData());

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Parent' => $Parent,
                            'TargetCategory' => $editForm->getData(),
                        ),
                        $request
                    );

                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE, $event);

                    $this->addSuccess('admin.category.save.complete', 'admin');

                    if ($Parent) {
                        return $this->redirectToRoute('admin_product_category_show', array('parent_id' => $Parent->getId()));
                    } else {
                        return $this->redirectToRoute('admin_product_category');
                    }
                }
            }
        }

        $formViews = [];
        foreach ($forms as $key => $value) {
            $formViews[$key] = $value->createView();
        }

        return [
            'form' => $form->createView(),
            'Parent' => $Parent,
            'Categories' => $Categories,
            'TopCategories' => $TopCategories,
            'TargetCategory' => $TargetCategory,
            'forms' => $formViews
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/product/category/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_category_delete")
     */
    public function delete(Request $request, $id)
    {
        $this->isTokenValid();

        $TargetCategory = $this->categoryRepository->find($id);
        if (!$TargetCategory) {
            $this->deleteMessage();
            return $this->redirectToRoute('admin_product_category');
        }
        $Parent = $TargetCategory->getParent();

        log_info('カテゴリ削除開始', array($id));

        try {
            $this->categoryRepository->delete($TargetCategory);

            $event = new EventArgs(
                array(
                    'Parent' => $Parent,
                    'TargetCategory' => $TargetCategory,
                ), $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.category.delete.complete', 'admin');

            log_info('カテゴリ削除完了', array($id));

        } catch (\Exception $e) {
            log_info('カテゴリ削除エラー', [$id, $e]);

            $message = trans('admin.delete.failed.foreign_key', ['%name%' => trans('category.text.name')]);
            $this->addError($message, 'admin');
        }

        if ($Parent) {
            return $this->redirectToRoute('admin_product_category_show', array('parent_id' => $Parent->getId()));
        } else {
            return $this->redirectToRoute('admin_product_category');
        }
    }

    /**
     * @Method("POST")
     * @Route("/%eccube_admin_route%/product/category/sort_no/move", name="admin_product_category_sort_no_move")
     */
    public function moveSortNo(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        if ($this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $categoryId => $sortNo) {
                /* @var $Category \Eccube\Entity\Category */
                $Category = $this->categoryRepository
                    ->find($categoryId);
                $Category->setSortNo($sortNo);
                $this->entityManager->persist($Category);
            }
            $this->entityManager->flush();

            return new Response('Successful');
        }
    }


    /**
     * カテゴリCSVの出力.
     *
     * @Route("/%eccube_admin_route%/product/category/export", name="admin_product_category_export")
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($request) {

            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_CATEGORY);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            $qb = $this->categoryRepository
                ->createQueryBuilder('c')
                ->orderBy('c.sort_no', 'DESC');

            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($request) {

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
