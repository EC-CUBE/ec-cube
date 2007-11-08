<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once CLASS_PATH . 'pages/LC_Page.php';
require_once 'utils/LC_Utils_Upgrade.php';
require_once 'utils/LC_Utils_Upgrade_Log.php';

/**
 * オーナーズストアからダウンロードデータを取得する.
 *
 * TODO 要リファクタリング
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_Download extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->objJson = new Services_Json();
        $this->objLog = new LC_Utils_Upgrade_Log('Download');

        $this->objForm = new SC_FormParam();
        $this->objForm->addParam(
            'product_id', 'product_id', INT_LEN, '',
            array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $this->objForm->setParam($_POST);

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->objLog->start();

        // 管理画面ログインチェック
        $this->objLog->log('* admin auth start');
        if (LC_Utils_Upgrade::isLoggedInAdminPage() !== true) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_ADMIN_AUTH,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_DL_ADMIN_AUTH)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode']);
            exit;
        }

        // パラメーチェック
        $this->objLog->log('* post parameter check start');
        if ($this->objForm->checkError()) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROER,
                'errcode' => OWNERSSTORE_ERR_DL_POST_PARAM,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_DL_POST_PARAM)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode'], $_POST);
            exit;
        }

        // TODO CSRF対策が必須

        // ダウンロードリクエストを開始
        $this->objLog->log('* http request start');
        $objReq = LC_Utils_Upgrade::request(
            'download',
            array('product_id' => $this->objForm->getValue('product_id'))
        );

        // リクエストの懸賞
        $this->objLog->log('* http request check start');
        if (PEAR::isError($objReq)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_HTTP_REQ,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_DL_HTTP_REQ)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode'], $objReq);
            exit;
        }

        // レスポンスの検証
        $this->objLog->log('* http response check start');
        if ($objReq->getResponseCode() !== 200) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_HTTP_RESP_CODE,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_DL_HTTP_RESP_CODE)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode'], $objReq);
            exit;
        }

        // JSONデータの検証
        $body = $objReq->getResponseBody();
        $objRet = $this->objJson->decode($body);

        $this->objLog->log('* json data check start');
        if (empty($objRet)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_INVALID_JSON_DATA,
                'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_DL_INVALID_JSON_DATA)
            );
            echo $this->objJson->encode($arrErr);
            $this->objLog->errLog($arrErr['errcode'], $objReq);
            exit;
        }
        // ダウンロードデータの保存
        if ($objRet->status === OWNERSSTORE_STATUS_SUCCESS) {
            $this->objLog->log('* save file start');
            $time = time();
            $dir  = DATA_PATH . 'downloads/tmp/';
            $filename = $time . '.tar.gz';

            $data = base64_decode($objRet->body);

            $this->objLog->log("* open ${filename} start");
            if ($fp = fopen($dir . $filename, "w")) {
                fwrite($fp, $data);
                fclose($fp);
            } else {
                $arrErr = array(
                    'status'  => OWNERSSTORE_STATUS_ERROR,
                    'errcode' => OWNERSSTORE_ERR_DL_FILE_WRITE,
                    'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_DL_FILE_WRITE)
                );
                echo $this->objJson->encode($arrErr);
                $this->objLog->errLog($arrErr['errcode'], $dir . $filename);
                exit;
            }

            // ダウンロードアーカイブを展開する
            $exract_dir = $dir . $time;
            $this->objLog->log("* mkdir ${exract_dir} start");
            if (!@mkdir($exract_dir)) {
                $arrErr = array(
                    'status'  => OWNERSSTORE_STATUS_ERROR,
                    'errcode' => OWNERSSTORE_ERR_DL_MKDIR,
                    'body' => LC_Utils_Upgrade::getErrMessage(OWNERSSTORE_ERR_DL_MKDIR)
                );
                echo $this->objJson->encode($arrErr);
                $this->objLog->errLog($arrErr['errcode'], $exract_dir);
                exit;
            }

            $this->objLog->log("* extract ${dir}${filename} start");
            $tar = new Archive_Tar($dir . $filename);
            $tar->extract($exract_dir);

            $this->objLog->log("* copy batch start");
            include_once CLASS_PATH . 'batch/SC_Batch_Update.php';
            $objBatch = new SC_Batch_Update();
            $arrCopyLog = $objBatch->execute($exract_dir);

            // テーブルの更新
            $this->objLog->log("* insert/update dtb_module start");
            $this->updateMdlTable($objRet->product_data);

            // 配信サーバへ通知
            $this->objLog->log("* notify to lockon server start");
            $this->notifyDownload($objReq->getResponseCookies());

            $arrParam = array(
                'status'  => OWNERSSTORE_STATUS_SUCCESS,
                'body' => 'インストール/アップデートに成功しました！'
            );
            echo $this->objJson->encode($arrParam);
            $this->objLog->log('* file save ok');
            exit;
        } else {
            echo $body;
            $this->objLog->errLog($arrErr['errcode'], array($objRet, $objReq));
            exit;
        }
    }

    /**
     * デストラクタ
     *
     * @return void
     */
    function destroy() {
        $this->objLog->end();
    }

    /**
     * dtb_moduleを更新する
     *
     * @param object $objRet
     */
    function updateMdlTable($objRet) {
        $table = 'dtb_module';
        $where = 'module_id = ?';
        $objQuery = new SC_Query;

        $count = $objQuery->count($table, $where, array($objRet->product_id));
        if ($count) {
            $arrUpdate = array(
                'module_name' => $objRet->product_code,
                'update_date' => 'NOW()'
            );
            $objQuery->update($table, $arrUpdate ,$where, array($objRet->product_id));
        } else {
            $arrInsert = array(
                'module_id' => $objRet->product_id,
                'module_name' => $objRet->product_code,
                'auto_update_flg' => '0',
                'create_date'     => 'NOW()',
                'update_date' => 'NOW()'
            );
            $objQuery->insert($table, $arrInsert);
        }
    }

    /**
     * 配信サーバへダウンロード完了を通知する.
     *
     * FIXME エラーコード追加
     * @param array #arrCookies Cookie配列
     * @retrun
     */
    function notifyDownload($arrCookies) {
        $objReq = LC_Utils_Upgrade::request('download_log', array(), $arrCookies);

        return true;
    }
}
?>
