<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Product;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ClassCategoryType;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\CsvExportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ClassCategoryController extends AbstractController
{
    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * ClassCategoryController constructor.
     *
     * @param ProductClassRepository $productClassRepository
     * @param ClassCategoryRepository $classCategoryRepository
     * @param ClassNameRepository $classNameRepository
     * @param CsvExportService $csvExportService
     */
    public function __construct(
        ProductClassRepository $productClassRepository,
        ClassCategoryRepository $classCategoryRepository,
        ClassNameRepository $classNameRepository,
        CsvExportService $csvExportService
    ) {
        $this->productClassRepository = $productClassRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->classNameRepository = $classNameRepository;
        $this->csvExportService = $csvExportService;
    }

    /**
     * @Route("/%eccube_admin_route%/product/class_category/{class_name_id}", requirements={"class_name_id" = "\d+"}, name="admin_product_class_category", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/product/class_category/{class_name_id}/{id}/edit", requirements={"class_name_id" = "\d+", "id" = "\d+"}, name="admin_product_class_category_edit", methods={"GET", "POST"})
     * @Template("@admin/Product/class_category.twig")
     */
    public function index(Request $request, $class_name_id, $id = null)
    {
        $ClassName = $this->classNameRepository->find($class_name_id);
        if (!$ClassName) {
            throw new NotFoundHttpException();
        }
        if ($id) {
            $TargetClassCategory = $this->classCategoryRepository->find($id);
            if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetClassCategory = new \Eccube\Entity\ClassCategory();
            $TargetClassCategory->setClassName($ClassName);
        }

        $builder = $this->formFactory
            ->createBuilder(ClassCategoryType::class, $TargetClassCategory);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'ClassName' => $ClassName,
                'TargetClassCategory' => $TargetClassCategory,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_INITIALIZE);

        $ClassCategories = $this->classCategoryRepository->getList($ClassName);

        $forms = [];
        foreach ($ClassCategories as $ClassCategory) {
            $id = $ClassCategory->getId();
            $forms[$id] = $this->formFactory->createNamed('class_category_'.$id, ClassCategoryType::class, $ClassCategory);
        }

        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                log_info('規格分類登録開始', [$id]);

                $this->classCategoryRepository->save($TargetClassCategory);

                log_info('規格分類登録完了', [$id]);

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'ClassName' => $ClassName,
                        'TargetClassCategory' => $TargetClassCategory,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_COMPLETE);

                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);
            }

            foreach ($forms as $editForm) {
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->classCategoryRepository->save($editForm->getData());
                    $this->addSuccess('admin.common.save_complete', 'admin');

                    return $this->redirectToRoute('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);
                }
            }
        }

        $formViews = [];
        foreach ($forms as $key => $value) {
            $formViews[$key] = $value->createView();
        }

        return [
            'form' => $form->createView(),
            'ClassName' => $ClassName,
            'ClassCategories' => $ClassCategories,
            'TargetClassCategory' => $TargetClassCategory,
            'forms' => $formViews,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/product/class_category/{class_name_id}/{id}/delete", requirements={"class_name_id" = "\d+", "id" = "\d+"}, name="admin_product_class_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $class_name_id, $id)
    {
        $this->isTokenValid();

        $ClassName = $this->classNameRepository->find($class_name_id);
        if (!$ClassName) {
            throw new NotFoundHttpException();
        }

        log_info('規格分類削除開始', [$id]);

        $TargetClassCategory = $this->classCategoryRepository->find($id);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            $this->deleteMessage();

            return $this->redirectToRoute('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);
        }

        try {
            $this->classCategoryRepository->delete($TargetClassCategory);

            $event = new EventArgs(
                [
                    'ClassName' => $ClassName,
                    'TargetClassCategory' => $TargetClassCategory,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            log_info('規格分類削除完了', [$id]);
        } catch (\Exception $e) {
            log_error('規格分類削除エラー', [$id, $e]);

            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $TargetClassCategory->getName()]);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);
    }

    /**
     * @Route("/%eccube_admin_route%/product/class_category/{class_name_id}/{id}/visibility", requirements={"class_name_id" = "\d+", "id" = "\d+"}, name="admin_product_class_category_visibility", methods={"PUT"})
     */
    public function visibility(Request $request, $class_name_id, $id)
    {
        $this->isTokenValid();

        $ClassName = $this->classNameRepository->find($class_name_id);
        if (!$ClassName) {
            throw new NotFoundHttpException();
        }

        log_info('規格分類表示変更開始', [$id]);

        $TargetClassCategory = $this->classCategoryRepository->find($id);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            $this->deleteMessage();

            return $this->redirectToRoute('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);
        }

        $this->classCategoryRepository->toggleVisibility($TargetClassCategory);

        log_info('規格分類表示変更完了', [$id]);

        $event = new EventArgs(
            [
                'ClassName' => $ClassName,
                'TargetClassCategory' => $TargetClassCategory,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_DELETE_COMPLETE);

        if ($TargetClassCategory->isVisible()) {
            $this->addSuccess(trans('admin.common.to_show_complete', ['%name%' => $TargetClassCategory->getName()]), 'admin');
        } else {
            $this->addSuccess(trans('admin.common.to_hide_complete', ['%name%' => $TargetClassCategory->getName()]), 'admin');
        }

        return $this->redirectToRoute('admin_product_class_category', ['class_name_id' => $ClassName->getId()]);
    }

    /**
     * @Route("/%eccube_admin_route%/product/class_category/sort_no/move", name="admin_product_class_category_sort_no_move", methods={"POST"})
     */
    public function moveSortNo(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        if ($this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $categoryId => $sortNo) {
                $ClassCategory = $this->classCategoryRepository
                    ->find($categoryId);
                $ClassCategory->setSortNo($sortNo);
                $this->entityManager->persist($ClassCategory);
            }
            $this->entityManager->flush();

            return new Response('Successful');
        }
    }

    /**
     * 規格分類CSVの出力.
     *
     * @Route("/%eccube_admin_route%/product/class_category/export/{class_name_id}", name="admin_product_class_category_export", methods={"GET"})
     *
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function export(Request $request, $class_name_id)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger(null);
        
        $response = new StreamedResponse();
        $response->setCallback(function () use ($request, $class_name_id) {
            // CSV種別を元に初期化.
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_CLASS_CATEGORY);
            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();
            
            $qb = $this->classCategoryRepository
                ->createQueryBuilder('cc')
                ->where('cc.ClassName = :ClassName')
                ->setParameter('ClassName', $class_name_id)
                ->orderBy('cc.sort_no', 'DESC');

        // データ行の出力.
        $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($request) {
                $Csvs = $csvService->getCsvs();

                /** @var $ClassCategory \Eccube\Entity\ClassCategory */
                $ClassCategory = $entity;

                // CSV出力項目と合致するデータを取得.
                $ExportCsvRow = new \Eccube\Entity\ExportCsvRow();
                foreach ($Csvs as $Csv) {
                    $ExportCsvRow->setData($csvService->getData($Csv, $ClassCategory));

                    $event = new EventArgs(
                        [
                            'csvService' => $csvService,
                            'Csv' => $Csv,
                            'ClassCategory' => $ClassCategory,
                            'ExportCsvRow' => $ExportCsvRow,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_CSV_EXPORT);

                    $ExportCsvRow->pushData();
                }
                //$row[] = number_format(memory_get_usage(true));
                // 出力.
                $csvService->fputcsv($ExportCsvRow->getRow());
            });
        });
        $now = new \DateTime();
        $filename = 'class_category_'.$now->format('YmdHis').'.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);

        log_info('規格分類CSV出力ファイル名', [$filename]);

        return $response;
    }
}
