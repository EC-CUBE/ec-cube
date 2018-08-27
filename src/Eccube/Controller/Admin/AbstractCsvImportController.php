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

        $file = file_get_contents($this->eccubeConfig['eccube_csv_temp_realdir'].'/'.$this->csvFileName);

        if ('\\' === DIRECTORY_SEPARATOR && PHP_VERSION_ID >= 70000) {
            // Windows 環境の PHP7 の場合はファイルエンコーディングを CP932 に合わせる
            // see https://github.com/EC-CUBE/ec-cube/issues/1780
            setlocale(LC_ALL, ''); // 既定のロケールに設定
            if (mb_detect_encoding($file) === 'UTF-8') { // UTF-8 を検出したら SJIS-win に変換
                $file = mb_convert_encoding($file, 'SJIS-win', 'UTF-8');
            }
        } else {
            // アップロードされたファイルがUTF-8以外は文字コード変換を行う
            $encode = StringUtil::characterEncoding($file);
            if (!empty($encode) && $encode != 'UTF-8') {
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

    protected function sendTemplateResponse(Request $request, $columns, $filename)
    {
        set_time_limit(0);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($request, $columns) {
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
        $response->send();

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
