<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Upgrade;

use Eccube\Application;
use Eccube\Page\Upgrade\Helper\LogHelper;
use Eccube\Page\Upgrade\Helper\JsonHelper;
use Eccube\Framework\Batch\BatchUpdate;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;

/**
 * オーナーズストアからダウンロードデータを取得する.
 *
 * TODO 要リファクタリング
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Download extends AbstractUpgrade
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process($mode)
    {
        $objLog  = new LogHelper;
        $objLog->start($mode);

        $objJson = new JsonHelper;

        // アクセスチェック
        $objLog->log('* auth start');
        if ($this->isValidAccess($mode) !== true) {
            // TODO
            $objJson->setError(OSTORE_E_C_INVALID_ACCESS);
            $objJson->display();
            $objLog->error(OSTORE_E_C_INVALID_ACCESS);

            return;
        }

        // パラメーチェック
        $this->initParam();
        $objLog->log('* post param check start');
        $arrErr = $this->objForm->checkError();
        if ($arrErr) {
            $objJson->setError(OSTORE_E_C_INVALID_PARAM);
            $objJson->display();
            $objLog->error(OSTORE_E_C_INVALID_PARAM, $_POST);
            $objLog->log('* post param check error ' . print_r($arrErr, true));

            return;
        }

        $objLog->log('* auto update check start');
        if ($mode == 'auto_update'
        && $this->autoUpdateEnable($this->objForm->getValue('product_id')) !== true) {
            $objJson->setError(OSTORE_E_C_AUTOUP_DISABLE);
            $objJson->display();
            $objLog->error(OSTORE_E_C_AUTOUP_DISABLE, $_POST);

            return;
        }

        // TODO CSRF対策

        // 認証キーの取得
        $public_key = $this->getPublicKey();
        $sha1_key = $this->createSeed();

        // 認証キーチェック
        $objLog->log('* public key check start');
        if (empty($public_key)) {
            $objJson->setError(OSTORE_E_C_NO_KEY);
            $objJson->display();
            $objLog->error(OSTORE_E_C_NO_KEY);

            return;
        }

        // リクエストを開始
        $objLog->log('* http request start');

        switch ($mode) {
            case 'patch_download':
                $arrPostData = array(
                    'eccube_url' => HTTP_URL,
                    'public_key' => sha1($public_key . $sha1_key),
                    'sha1_key'   => $sha1_key,
                    'patch_code' => 'latest'
                );
                break;
            default:
                $arrPostData = array(
                    'eccube_url' => HTTP_URL,
                    'public_key' => sha1($public_key . $sha1_key),
                    'sha1_key'   => $sha1_key,
                    'product_id' => $this->objForm->getValue('product_id')
                );
                break;
        }

        $objReq = $this->request($mode, $arrPostData);

        // リクエストチェック
        $objLog->log('* http request check start');
        if (\PEAR::isError($objReq)) {
            $objJson->setError(OSTORE_E_C_HTTP_REQ);
            $objJson->display();
            $objLog->error(OSTORE_E_C_HTTP_REQ, $objReq);

            return;
        }

        // レスポンスチェック
        $objLog->log('* http response check start');
        if ($objReq->getResponseCode() !== 200) {
            $objJson->setError(OSTORE_E_C_HTTP_RESP);
            $objJson->display();
            $objLog->error(OSTORE_E_C_HTTP_RESP, $objReq);

            return;
        }

        $body = $objReq->getResponseBody();
        $objRet = $objJson->decode($body);

        // JSONデータのチェック
        $objLog->log('* json data check start');
        if (empty($objRet)) {
            $objJson->setError(OSTORE_E_C_FAILED_JSON_PARSE);
            $objJson->display();
            $objLog->error(OSTORE_E_C_FAILED_JSON_PARSE, $objReq);

            return;
        }

        // ダウンロードデータの保存
        if ($objRet->status === OSTORE_STATUS_SUCCESS) {
            $objLog->log('* save file start');
            $time = time();
            $dir  = DATA_REALDIR . 'downloads/tmp/';
            $filename = $time . '.tar.gz';

            $data = base64_decode($objRet->data->dl_file);

            $objLog->log("* open ${filename} start");
            if ($fp = @fopen($dir . $filename, 'w')) {
                @fwrite($fp, $data);
                @fclose($fp);
            } else {
                $objJson->setError(OSTORE_E_C_PERMISSION);
                $objJson->display();
                $objLog->error(OSTORE_E_C_PERMISSION, $dir . $filename);

                return;
            }

            // ダウンロードアーカイブを展開する
            $exract_dir = $dir . $time;
            $objLog->log("* mkdir ${exract_dir} start");
            if (!@mkdir($exract_dir)) {
                $objJson->setError(OSTORE_E_C_PERMISSION);
                $objJson->display();
                $objLog->error(OSTORE_E_C_PERMISSION, $exract_dir);

                return;
            }

            $objLog->log("* extract ${dir}${filename} start");
            $tar = new Archive_Tar($dir . $filename);
            $tar->extract($exract_dir);

            $objLog->log('* copy batch start');
            $objBatch = new BatchUpdate();
            $arrCopyLog = $objBatch->execute($exract_dir);

            $objLog->log('* copy batch check start');
            if (count($arrCopyLog['err']) > 0) {
                $objJson->setError(OSTORE_E_C_BATCH_ERR);
                $objJson->display();
                $objLog->error(OSTORE_E_C_BATCH_ERR, $arrCopyLog);
                $this->registerUpdateLog($arrCopyLog, $objRet->data);
                $this->updateMdlTable($objRet->data);

                return;
            }

            // dtb_module_update_logの更新
            $objLog->log('* insert dtb_module_update start');
            $this->registerUpdateLog($arrCopyLog, $objRet->data);

            // dtb_moduleの更新
            $objLog->log('* insert/update dtb_module start');
            $this->updateMdlTable($objRet->data);

            // DB更新ファイルの読み込み、実行
            $objLog->log('* file execute start');
            $this->fileExecute($objRet->data->product_code);

            // 配信サーバーへ通知
            $objLog->log('* notify to lockon server start');
            $objReq = $this->notifyDownload($mode, $objReq->getResponseCookies());

            $objLog->log('* dl commit result:' . serialize($objReq));

            $productData = $objRet->data;
            $productData->dl_file = '';
            $objJson->setSUCCESS($productData, 'インストール/アップデートに成功しました。');
            $objJson->display();
            $objLog->end();

            return;
        } else {
            // 配信サーバー側でエラーを補足
            echo $body;
            $objLog->error($objRet->errcode, $objReq);

            return;
        }
    }

    public function initParam()
    {
        $this->objForm = Application::alias('eccube.form_param');
        $this->objForm->addParam(
            'product_id', 'product_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $this->objForm->setParam($_POST);
    }

    /**
     * dtb_moduleを更新する
     *
     * @param object $objRet
     */
    public function updateMdlTable($objRet)
    {
        $table = 'dtb_module';
        $where = 'module_id = ?';
        $objQuery = Application::alias('eccube.query');

        $exists = $objQuery->exists($table, $where, array($objRet->product_id));
        if ($exists) {
            $arrUpdate = array(
                'module_code' => $objRet->product_code,
                'module_name' => $objRet->product_name,
                'update_date' => 'CURRENT_TIMESTAMP'
            );
            $objQuery->update($table, $arrUpdate, $where, array($objRet->product_id));
        } else {
            $arrInsert = array(
                'module_id'   => $objRet->product_id,
                'module_code' => $objRet->product_code,
                'module_name' => $objRet->product_name,
                'auto_update_flg' => '0',
                'create_date'     => 'CURRENT_TIMESTAMP',
                'update_date' => 'CURRENT_TIMESTAMP'
            );
            $objQuery->insert($table, $arrInsert);
        }
    }

    /**
     * 配信サーバーへダウンロード完了を通知する.
     *
     * FIXME エラーコード追加
     * @param array #arrCookies Cookie配列
     * @return
     */
    public function notifyDownload($mode, $arrCookies)
    {
        $arrPOSTParams = array(
            'eccube_url' => HTTP_URL
        );
        $objReq = $this->request($mode . '_commit', $arrPOSTParams, $arrCookies);

        return $objReq;
    }

    /**
     * アクセスチェック
     *
     * @return boolean
     */
    public function isValidAccess($mode)
    {
        $objLog = new LogHelper;
        switch ($mode) {
        // モジュールダウンロード
        case 'download':
            if ($this->isLoggedInAdminPage() === true) {
                $objLog->log('* admin login ok');

                return true;
            }
            break;
        // 自動アップロード最新ファイル取得
        case 'patch_download':
        // モジュール自動アップロード
        case 'auto_update':
            $objForm = Application::alias('eccube.form_param');
            $objForm->addParam('public_key', 'public_key', MTEXT_LEN, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objForm->addParam('sha1_key', 'sha1_key', MTEXT_LEN, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objForm->setParam($_POST);

            $objLog->log('* param check start');
            $arrErr = $objForm->checkError();
            if ($arrErr) {
                $objLog->log('* invalid param ' . print_r($arrErr, true));

                return false;
            }

            $objLog->log('* public_key check start');
            $public_key = $this->getPublicKey();
            if (empty($public_key)) {
                $objLog->log('* public_key not found');

                return false;
            }

            $sha1_key = $objForm->getValue('sha1_key');
            $public_key_sha1 = $objForm->getValue('public_key');

            $objLog->log('* ip check start');
            if ($public_key_sha1 === sha1($public_key . $sha1_key)) {
                $objLog->log('* auto update login ok');

                return true;
            }
            break;
        default:
            $objLog->log('* mode invalid ' . $mode);

            return false;
        }

        return false;
    }

    public function registerUpdateLog($arrLog, $objRet)
    {
        $objQuery = Application::alias('eccube.query');
        $arrInsert = array(
            'log_id'      => $objQuery->nextVal('dtb_module_update_logs_log_id'),
            'module_id'   => $objRet->product_id,
            'buckup_path' => $arrLog['buckup_path'],
            'error_flg'   => count($arrLog['err']),
            'error'       => implode("\n", $arrLog['err']),
            'ok'          => implode("\n", $arrLog['ok']),
            'update_date' => 'CURRENT_TIMESTAMP',
            'create_date' => 'CURRENT_TIMESTAMP',
        );
        $objQuery->insert('dtb_module_update_logs', $arrInsert);
    }

    /**
     * DB更新ファイルの読み込み、実行
     *
     * パッチ側でupdate.phpを用意する.
     * 他の変数・関数とかぶらないよう、
     * LC_Update_Updater::execute()で処理を実行する.
     */
    public function fileExecute($productCode)
    {
        $file = DATA_REALDIR . 'downloads/update/' . $productCode . '_update.php';
        if (file_exists($file)) {
            @include_once $file;
            if (class_exists('LC_Update_Updater')) {
                $update = new LC_Update_Updater;
                $update->execute();
            }
        }
    }
}
