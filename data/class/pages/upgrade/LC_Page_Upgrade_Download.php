<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once CLASS_PATH . 'pages/upgrade/LC_Page_Upgrade_Base.php';
error_reporting(E_ALL);
/**
 * ダウンロード処理を担当する.
 *
 * TODO 要リファクタリング
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_Download extends LC_Page_Upgrade_Base {

    /** SC_Sessionオブジェクト */
    var $objSession = null;
    /** Services_Jsonオブジェクト */
    var $objJson = null;
    /** HTTP_Requestオブジェクト */
    var $objReq = null;
    /** SC_FromParamオブジェクト */
    var $objForm = null;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->objSess = new SC_Session();
        $this->objJson = new Services_Json();
        $rhis->objReq  = new HTTP_Request();
        $this->objForm = new SC_FormParam();
        $this->objForm->addParam(
            'product_id', 'product_id', INT_LEN, '',
            array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $this->objForm->setParam($_POST);
    }

    /**
     * 使用してません
     * こんな感じで書けたら楽かな...
     */
    function _process() {
        $result = $this->_try();
        if ($e = $this->_catch($result)) {
            GC_Utils::gfPrintLog(sprintf($e->log_format, $e->stacktrace));
            $this->_throw($e->json);
            exit;
        }
        echo $result;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $errFormat = '* error! code:%s / debug:%s';

        GC_Utils::gfPrintLog('###Download Start###');

        // 管理画面ログインチェック
        GC_Utils::gfPrintLog('* admin auth start');
        if ($this->objSess->isSuccess() !== SUCCESS) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_ADMIN_AUTH,
                'body' => '管理画面にログインしていません'
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($_SESSION))
            );
            exit;
        }

        // パラメーチェック
        GC_Utils::gfPrintLog('* post parameter check start');
        if ($this->objForm->checkError()) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROER,
                'errcode' => OWNERSSTORE_ERR_DL_POST_PARAM,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_DL_POST_PARAM
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($_POST))
            );
            exit;
        }

        // TODO CSRF対策が必須

        // ダウンロードリクエストを開始
        GC_Utils::gfPrintLog('* http request start');
        $resp = $this->request(
            'download',
            array('product_id' => $this->objForm->getValue('product_id'))
        );

        // リクエストのエラーチェック
        GC_Utils::gfPrintLog('* http response check start');
        if (PEAR::isError($resp)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_HTTP_REQ,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_DL_HTTP_REQ
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($resp))
            );
            exit;
        }

        // JSONデータの検証
        $jsonData = $resp->getResponseBody();
        $objRet   = $this->objJson->decode($resp->getResponseBody($jsonData));
        GC_Utils::gfPrintLog('* json data check start');
        if (empty($objRet)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_DL_INVALID_JSON_DATA,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_DL_INVALID_JSON_DATA
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($resp))
            );
            exit;
        }
        // ダウンロードデータの保存
        if ($objRet->status === OWNERSSTORE_STATUS_SUCCESS) {
            GC_Utils::gfPrintLog('* save file start');
            $time = time();
            $dir  = DATA_PATH . 'downloads/tmp/';
            $filename = $time . '.tar.gz';

            $data = base64_decode($objRet->body);

            if ($fp = fopen($dir . $filename, "w")) {
                fwrite($fp, $data);
                fclose($fp);
            } else {
                $arrErr = array(
                    'status'  => OWNERSSTORE_STATUS_ERROR,
                    'errcode' => OWNERSSTORE_ERR_DL_FILE_WRITE,
                    'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_DL_FILE_WRITE
                );
                echo $this->objJson->encode($arrErr);
                GC_Utils::gfPrintLog(
                    sprintf($errFormat, $arrErr['errcode'], serialize($dir . $filename))
                );
                exit;
            }
            // ダウンロードアーカイブを展開する
            $exract_dir = $dir . $time;
            if (!@mkdir($exract_dir)) {
                $arrErr = array(
                    'status'  => OWNERSSTORE_STATUS_ERROR,
                    'errcode' => OWNERSSTORE_ERR_DL_MKDIR,
                    'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_DL_MKDIR
                );
                echo $this->objJson->encode($arrErr);
                GC_Utils::gfPrintLog(
                    sprintf($errFormat, $arrErr['errcode'], serialize($exract_dir))
                );
                exit;
            }

            $tar = new Archive_Tar($dir . $filename);
            $tar->extract($exract_dir);

            include_once CLASS_PATH . 'batch/SC_Batch_Update.php';
            $objBatch = new SC_Batch_Update();
            $arrCopyLog = $objBatch->execute($exract_dir);

            $this->notifyDownload($resp->getResponseCookies(), $objRet->product_data);
            // テーブルの更新
            // $this->updateMdlTable($objRet);

            $arrParam = array(
                'status'  => OWNERSSTORE_STATUS_SUCCESS,
                'body' => 'インストール/アップデートに成功しました！'
            );
            echo $this->objJson->encode($arrParam);
            GC_Utils::gfPrintLog('* file save ok');
            exit;
        } else {
            echo $jsonData;
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $objRet->errcode, serialize(array($resp, $objRet)))
            );
            exit;
        }
    }

    /**
     * デストラクタ
     *
     * @return void
     */
    function destroy() {
        GC_Utils::gfPrintLog('###Download End###');
    }

    /**
     * dtb_moduleを更新する
     *
     * @param object $objRet
     */
    function updateMdlTable($objRet) {
        $table = 'dtb_module';
        $objQuery = new SC_Query;

        $count = $objQuery->count($objRet, 'module_id=?', array($objRet->product_id));
        if ($count) {
            $arrUpdate = array();
            $objQuery->update($table, $arrUpdate);
        } else {
            $arrInsert = array();
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
        $objReq = new HTTP_Request();
        $objReq->setUrl('http://cube-shopaccount/upgrade/index.php');
        $objReq->setMethod('POST');
        $objReq->addPostData('mode', 'download_log');

        // Cookie追加
        foreach ($arrCookies as $cookie) {
            $objReq->addCookie($cookie['name'], $cookie['value']);
        }

        $e = $objReq->sendRequest();
        if (PEAR::isError($e)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => 999,
                'body'    => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . 999
            );
            return $arrErr;
        }

        if ($objReq->getResponseCode() !== 200) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => 999,
                'body'    => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . 999
            );
            return $arrErr;
        }
        echo $objReq->getResponseBody();
        // TODO STATUSチェック
        return true;
    }
}
?>
