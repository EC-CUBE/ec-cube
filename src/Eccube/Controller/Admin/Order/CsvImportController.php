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

namespace Eccube\Controller\Admin\Order;

use Eccube\Controller\Admin\AbstractCsvImportController;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Shipping;
use Eccube\Form\Type\Admin\CsvImportType;
use Eccube\Repository\ShippingRepository;
use Eccube\Service\CsvImportService;
use Eccube\Service\OrderStateMachine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CsvImportController extends AbstractCsvImportController
{
    /**
     * @var ShippingRepository
     */
    private $shippingRepository;

    /**
     * @var OrderStateMachine
     */
    protected $orderStateMachine;

    public function __construct(
        ShippingRepository $shippingRepository,
        OrderStateMachine $orderStateMachine
    ) {
        $this->shippingRepository = $shippingRepository;
        $this->orderStateMachine = $orderStateMachine;
    }

    /**
     * 出荷CSVアップロード
     *
     * @Route("/%eccube_admin_route%/order/shipping_csv_upload", name="admin_shipping_csv_import", methods={"GET", "POST"})
     * @Template("@admin/Order/csv_shipping.twig")
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function csvShipping(Request $request)
    {
        $form = $this->formFactory->createBuilder(CsvImportType::class)->getForm();
        $columnConfig = $this->getColumnConfig();
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $formFile = $form['import_file']->getData();

                if (!empty($formFile)) {
                    $csv = $this->getImportData($formFile);

                    try {
                        $this->entityManager->getConfiguration()->setSQLLogger(null);
                        $this->entityManager->getConnection()->beginTransaction();

                        $this->loadCsv($csv, $errors);

                        if ($errors) {
                            $this->entityManager->getConnection()->rollBack();
                        } else {
                            $this->entityManager->flush();
                            $this->entityManager->getConnection()->commit();

                            $this->addInfo('admin.common.csv_upload_complete', 'admin');
                        }
                    } finally {
                        $this->removeUploadedFile();
                    }
                }
            }
        }

        return [
            'form' => $form->createView(),
            'headers' => $columnConfig,
            'errors' => $errors,
        ];
    }

    protected function loadCsv(CsvImportService $csv, &$errors)
    {
        $columnConfig = $this->getColumnConfig();

        if ($csv === false) {
            $errors[] = trans('admin.common.csv_invalid_format');
        }

        // 必須カラムの確認
        $requiredColumns = array_map(function ($value) {
            return $value['name'];
        }, array_filter($columnConfig, function ($value) {
            return $value['required'];
        }));
        $csvColumns = $csv->getColumnHeaders();
        if (count(array_diff($requiredColumns, $csvColumns)) > 0) {
            $errors[] = trans('admin.common.csv_invalid_format');

            return;
        }

        // 行数の確認
        $size = count($csv);
        if ($size < 1) {
            $errors[] = trans('admin.common.csv_invalid_format');

            return;
        }

        $columnNames = array_combine(array_keys($columnConfig), array_column($columnConfig, 'name'));

        foreach ($csv as $line => $row) {
            // 出荷IDがなければエラー
            if (!isset($row[$columnNames['id']])) {
                $errors[] = trans('admin.common.csv_invalid_required', ['%line%' => $line + 1, '%name%' => $columnNames['id']]);
                continue;
            }

            /* @var Shipping $Shipping */
            $Shipping = is_numeric($row[$columnNames['id']]) ? $this->shippingRepository->find($row[$columnNames['id']]) : null;

            // 存在しない出荷IDはエラー
            if (is_null($Shipping)) {
                $errors[] = trans('admin.common.csv_invalid_not_found', ['%line%' => $line + 1, '%name%' => $columnNames['id']]);
                continue;
            }

            if (isset($row[$columnNames['tracking_number']])) {
                $Shipping->setTrackingNumber($row[$columnNames['tracking_number']]);
            }

            if (isset($row[$columnNames['shipping_date']])) {
                // 日付フォーマットが異なる場合はエラー
                $shippingDate = \DateTime::createFromFormat('Y-m-d', $row[$columnNames['shipping_date']]);
                if ($shippingDate === false) {
                    $errors[] = trans('admin.common.csv_invalid_date_format', ['%line%' => $line + 1, '%name%' => $columnNames['shipping_date']]);
                    continue;
                }

                $shippingDate->setTime(0, 0, 0);
                $Shipping->setShippingDate($shippingDate);
            }

            $Order = $Shipping->getOrder();
            $RelateShippings = $Order->getShippings();
            $allShipped = true;
            foreach ($RelateShippings as $RelateShipping) {
                if (!$RelateShipping->getShippingDate()) {
                    $allShipped = false;
                    break;
                }
            }
            $OrderStatus = $this->entityManager->find(OrderStatus::class, OrderStatus::DELIVERED);
            if ($allShipped) {
                if ($this->orderStateMachine->can($Order, $OrderStatus)) {
                    $this->orderStateMachine->apply($Order, $OrderStatus);
                } else {
                    $from = $Order->getOrderStatus()->getName();
                    $to = $OrderStatus->getName();
                    $errors[] = trans('admin.order.failed_to_change_status', [
                        '%name%' => $Shipping->getId(),
                        '%from%' => $from,
                        '%to%' => $to,
                    ]);
                }
            }
        }
    }

    /**
     * アップロード用CSV雛形ファイルダウンロード
     *
     * @Route("/%eccube_admin_route%/order/csv_template", name="admin_shipping_csv_template", methods={"GET"})
     */
    public function csvTemplate(Request $request)
    {
        $columns = array_column($this->getColumnConfig(), 'name');

        return $this->sendTemplateResponse($request, $columns, 'shipping.csv');
    }

    protected function getColumnConfig()
    {
        return [
            'id' => [
                'name' => trans('admin.order.shipping_csv.shipping_id_col'),
                'description' => trans('admin.order.shipping_csv.shipping_id_description'),
                'required' => true,
            ],
            'tracking_number' => [
                'name' => trans('admin.order.shipping_csv.tracking_number_col'),
                'description' => trans('admin.order.shipping_csv.tracking_number_description'),
                'required' => false,
            ],
            'shipping_date' => [
                'name' => trans('admin.order.shipping_csv.shipping_date_col'),
                'description' => trans('admin.order.shipping_csv.shipping_date_description'),
                'required' => true,
            ],
        ];
    }
}
