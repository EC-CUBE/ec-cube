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

namespace Eccube\Controller\Admin;

use Eccube\Controller\AbstractController;
use Eccube\Service\CsvImportService;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AbstractCsvImportController extends AbstractController
{
    /**
     * アップロードされたCSVファイル名
     *
     * @var string
     */
    protected $csvFileName;

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
        $this->csvFileName = 'upload_'.StringUtil::random().'.'.$formFile->getClientOriginalExtension();
        $formFile->move($this->eccubeConfig['eccube_csv_temp_realdir'], $this->csvFileName);

        $file = new \SplFileObject($this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$this->csvFileName);

        set_time_limit(0);

        // アップロードされたCSVファイルを行ごとに取得
        $data = new CsvImportService($file, $this->eccubeConfig['eccube_csv_import_delimiter'], $this->eccubeConfig['eccube_csv_import_enclosure']);

        return $data->setHeaderRowNumber(0) ? $data : false;
    }

    protected function sendTemplateResponse(Request $request, $columns, $filename)
    {
        set_time_limit(0);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($columns) {
            // ヘッダ行の出力
            $row = [];
            foreach ($columns as $column) {
                $row[] = mb_convert_encoding($column, $this->eccubeConfig['eccube_csv_export_encoding'], 'UTF-8');
            }

            $fp = fopen('php://output', 'w');
            fputcsv($fp, $row, $this->eccubeConfig['eccube_csv_export_separator']);
            fclose($fp);
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);

        return $response;
    }

    /**
     * アップロードされたCSVファイルの削除
     */
    protected function removeUploadedFile()
    {
        if (!empty($this->csvFileName)) {
            try {
                $fs = new Filesystem();
                $fs->remove($this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$this->csvFileName);
            } catch (\Exception $e) {
                // エラーが発生しても無視する
            }
        }
    }
}
