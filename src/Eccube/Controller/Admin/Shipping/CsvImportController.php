<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Shipping;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Shipping;
use Eccube\Form\Type\Admin\CsvImportType;
use Eccube\Repository\ShippingRepository;
use Eccube\Service\CsvImportService;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class CsvImportController extends AbstractController
{
    /**
     * @var ShippingRepository
     */
    private $shippingRepository;

    public function __construct(ShippingRepository $shippingRepository)
    {
        $this->shippingRepository = $shippingRepository;
    }

    /**
     * 出荷CSVアップロード
     *
     * @Route("/%eccube_admin_route%/shipping/shipping_csv_upload", name="admin_shipping_csv_import")
     * @Template("@admin/Shipping/csv_shipping.twig")
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
                    $this->entityManager->getConfiguration()->setSQLLogger(null);
                    $this->entityManager->getConnection()->beginTransaction();

                    $csv = $this->getImportData($formFile);
                    $this->loadCsv($csv, $errors);

                    if ($errors) {
                        $this->entityManager->getConnection()->rollBack();
                    } else {
                        $this->entityManager->flush();
                        $this->entityManager->getConnection()->commit();

                        $this->addInfo('admin.shipping.csv_import.save.complete', 'admin');
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
            $errors[] = trans('csvimport.text.error.format_invalid');
        }

        // 必須カラムの確認
        $requiredColumns = array_map(function ($value) {
            return $value['name'];
        }, array_filter($columnConfig, function ($value) {
            return $value['required'];
        }));
        $csvColumns = $csv->getColumnHeaders();
        if (count(array_diff($requiredColumns, $csvColumns)) > 0) {
            $errors[] = trans('csvimport.text.error.format_invalid');
        }

        // 行数の確認
        $size = count($csv);
        if ($size < 1) {
            $errors[] = trans('csvimport.text.error.format_invalid');
        }

        $columnNames = array_combine(array_keys($columnConfig), array_column($columnConfig, 'name'));

        foreach ($csv as $line => $row) {
            // 出荷IDがなければエラー
            if (!isset($row[$columnNames['id']])) {
                $errors[] = trans('csvimportcontroller.require', ['%line%' => $line, '%name%' => $columnNames['id']]);
                continue;
            }

            /* @var Shipping $Shipping */
            $Shipping = is_numeric($row[$columnNames['id']]) ? $this->shippingRepository->find($row[$columnNames['id']]) : null;

            // 存在しない出荷IDはエラー
            if (is_null($Shipping)) {
                $errors[] = trans('csvimportcontroller.notfound', ['%line%' => $line, '%name%' => $columnNames['id']]);
                continue;
            }

            if (isset($row[$columnNames['tracking_number']])) {
                $Shipping->setTrackingNumber($row[$columnNames['tracking_number']]);
            }

            if (isset($row[$columnNames['shipping_date']])) {
                // 日付フォーマットが異なる場合はエラー
                $shippingDate = \DateTime::createFromFormat('Y-m-d', $row[$columnNames['shipping_date']]);
                if ($shippingDate === false) {
                    $errors[] = trans('csvimportcontroller.invalid_date_format', ['%line%' => $line, '%name%' => $columnNames['id']]);
                    continue;
                }

                $Shipping->setShippingDate($shippingDate);
            }
        }
    }

    /**
     * アップロードされたCSVファイルの行ごとの処理
     *
     * @param UploadedFile $formFile
     *
     * @return CsvImportService|bool
     */
    protected function getImportData(UploadedFile $formFile)
    {
        // アップロードされたCSVファイルを一時ディレクトリに保存
        $fileName = 'upload_'.StringUtil::random().'.'.$formFile->getClientOriginalExtension();
        $formFile->move($this->eccubeConfig['eccube_csv_temp_realdir'], $fileName);

        $file = file_get_contents($this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$fileName);

        if ('\\' === DIRECTORY_SEPARATOR && PHP_VERSION_ID >= 70000) {
            // Windows 環境の PHP7 の場合はファイルエンコーディングを CP932 に合わせる
            // see https://github.com/EC-CUBE/ec-cube/issues/1780
            setlocale(LC_ALL, ''); // 既定のロケールに設定
            if (mb_detect_encoding($file) === 'UTF-8') { // UTF-8 を検出したら SJIS-win に変換
                $file = mb_convert_encoding($file, 'SJIS-win', 'UTF-8');
            }
        } else {
            // アップロードされたファイルがUTF-8以外は文字コード変換を行う
            $encode = StringUtil::characterEncoding(substr($file, 0, 6));
            if ($encode != 'UTF-8') {
                $file = mb_convert_encoding($file, 'UTF-8', $encode);
            }
        }

        $file = StringUtil::convertLineFeed($file);

        $tmp = tmpfile();
        fwrite($tmp, $file);
        rewind($tmp);
        $meta = stream_get_meta_data($tmp);
        $file = new \SplFileObject($meta['uri']);

        set_time_limit(0);

        // アップロードされたCSVファイルを行ごとに取得
        $data = new CsvImportService($file, $this->eccubeConfig['eccube_csv_import_delimiter'], $this->eccubeConfig['eccube_csv_import_enclosure']);

        return $data->setHeaderRowNumber(0) ? $data : false;
    }

    protected function getColumnConfig()
    {
        return [
            'id' => [
                'name' => trans('admin.shipping.csv_shipping.id'),
                'description' => trans('admin.shipping.csv_shipping.id.description'),
                'required' => true,
            ],
            'tracking_number' => [
                'name' => trans('admin.shipping.csv_shipping.tracking_number'),
                'description' => trans('admin.shipping.csv_shipping.tracking_number.description'),
                'required' => false,
            ],
            'shipping_date' => [
                'name' => trans('admin.shipping.csv_shipping.shipping_date'),
                'description' => trans('admin.shipping.csv_shipping.shipping_date.description'),
                'required' => false,
            ],
        ];
    }
}
