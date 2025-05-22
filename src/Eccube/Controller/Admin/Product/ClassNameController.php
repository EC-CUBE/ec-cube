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
use Eccube\Entity\ClassName;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\ClassNameType;
use Eccube\Repository\ClassNameRepository;
use Eccube\Service\CsvExportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ClassNameController extends AbstractController
{
    /**
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * ClassNameController constructor.
     *
     * @param ClassNameRepository $classNameRepository
     * @param CsvExportService $csvExportService
     */
    public function __construct(
        ClassNameRepository $classNameRepository,
        CsvExportService $csvExportService
    ) {
        $this->classNameRepository = $classNameRepository;
        $this->csvExportService = $csvExportService;
    }

    /**
     * @Route("/%eccube_admin_route%/product/class_name", name="admin_product_class_name", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/product/class_name/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_class_name_edit", methods={"GET", "POST"})
     * @Template("@admin/Product/class_name.twig")
     */
    public function index(Request $request, $id = null)
    {
        if ($id) {
            $TargetClassName = $this->classNameRepository->find($id);
            if (!$TargetClassName) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetClassName = new \Eccube\Entity\ClassName();
        }

        $builder = $this->formFactory
            ->createBuilder(ClassNameType::class, $TargetClassName);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'TargetClassName' => $TargetClassName,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_INITIALIZE);

        $ClassNames = $this->classNameRepository->getList();

        /**
         * 編集用フォーム
         */
        $forms = [];
        foreach ($ClassNames as $ClassName) {
            $id = $ClassName->getId();
            $forms[$id] = $this->formFactory->createNamed('class_name_'.$id, ClassNameType::class, $ClassName);
        }

        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                log_info('商品規格登録開始', [$id]);

                $this->classNameRepository->save($TargetClassName);

                log_info('商品規格登録完了', [$id]);

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'TargetClassName' => $TargetClassName,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_COMPLETE);

                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_product_class_name');
            }

            /*
             * 編集処理
             */
            foreach ($forms as $editForm) {
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->classNameRepository->save($editForm->getData());

                    $this->addSuccess('admin.common.save_complete', 'admin');

                    return $this->redirectToRoute('admin_product_class_name');
                }
            }
        }
        $formViews = [];
        foreach ($forms as $key => $value) {
            $formViews[$key] = $value->createView();
        }

        return [
            'form' => $form->createView(),
            'ClassNames' => $ClassNames,
            'TargetClassName' => $TargetClassName,
            'forms' => $formViews,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/product/class_name/{id}/delete", requirements={"id" = "\d+"}, name="admin_product_class_name_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ClassName $ClassName)
    {
        $this->isTokenValid();

        log_info('商品規格削除開始', [$ClassName->getId()]);

        try {
            $this->classNameRepository->delete($ClassName);

            $event = new EventArgs(['ClassName' => $ClassName], $request);
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            log_info('商品規格削除完了', [$ClassName->getId()]);
        } catch (\Exception $e) {
            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $ClassName->getName()]);
            $this->addError($message, 'admin');

            log_error('商品規格削除エラー', [$ClassName->getId(), $e]);
        }

        return $this->redirectToRoute('admin_product_class_name');
    }

    /**
     * @Route("/%eccube_admin_route%/product/class_name/sort_no/move", name="admin_product_class_name_sort_no_move", methods={"POST"})
     */
    public function moveSortNo(Request $request)
    {
        if (!$request->isXmlHttpRequest() && $this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        if ($this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $classNameId => $sortNo) {
                $ClassName = $this->classNameRepository
                    ->find($classNameId);
                $ClassName->setSortNo($sortNo);
                $this->entityManager->persist($ClassName);
            }
            $this->entityManager->flush();

            return new Response();
        }
    }

    /**
     * 規格CSVの出力.
     *
     * @Route("/%eccube_admin_route%/product/class_name/export", name="admin_product_class_name_export", methods={"GET"})
     *
     * @param Request $request
     *
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
            $this->csvExportService->initCsvType(CsvType::CSV_TYPE_CLASS_NAME);

            // ヘッダ行の出力.
            $this->csvExportService->exportHeader();

            $qb = $this->classNameRepository
                ->createQueryBuilder('cn')
                ->orderBy('cn.sort_no', 'DESC');

            // データ行の出力.
            $this->csvExportService->setExportQueryBuilder($qb);
            $this->csvExportService->exportData(function ($entity, $csvService) use ($request) {
                $Csvs = $csvService->getCsvs();

                /** @var $ClassName \Eccube\Entity\ClassName */
                $ClassName = $entity;

                // CSV出力項目と合致するデータを取得.
                $ExportCsvRow = new \Eccube\Entity\ExportCsvRow();
                foreach ($Csvs as $Csv) {
                    $ExportCsvRow->setData($csvService->getData($Csv, $ClassName));

                    $event = new EventArgs(
                        [
                            'csvService' => $csvService,
                            'Csv' => $Csv,
                            'ClassName' => $ClassName,
                            'ExportCsvRow' => $ExportCsvRow,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_CSV_EXPORT);

                    $ExportCsvRow->pushData();
                }
                //$row[] = number_format(memory_get_usage(true));
                // 出力.
                $csvService->fputcsv($ExportCsvRow->getRow());
            });
        });

        $now = new \DateTime();
        $filename = 'class_name_'.$now->format('YmdHis').'.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);

        log_info('規格CSV出力ファイル名', [$filename]);

        return $response;
    }
}
