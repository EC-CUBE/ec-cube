<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once CLASS_PATH . 'pages/upgrade/LC_Page_Upgrade_Base.php';

/**
 * 自動アップデートを行う.
 *
 * TODO 要リファクタリング
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_AutoUpdate extends LC_Page_Upgrade_Base {

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
        $this->objJson = new Services_Json();
        $rhis->objReq  = new HTTP_Request();
        $this->objForm = new SC_FormParam();
        $this->objForm->addParam('product_id', 'product_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $this->objForm->setParam($_POST);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $errFormat = '* error! code:%s / debug:%s';

        GC_Utils::gfPrintLog('###Auto Update Start###');

        // IPチェック
        GC_Utils::gfPrintLog('* ip check start');
        if ($this->isValidIP() !== true) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => ''
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], '')
            );
            exit;
        }

        // パラメーチェック
        GC_Utils::gfPrintLog('* post parameter check start');
        if ($this->objForm->checkError()) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROER,
                'errcode' => '',
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($_POST))
            );
            exit;
        }

        // 自動アップデート設定の判定
        GC_Utils::gfPrintLog('* auto update settings check start');
        if ($this->autoUpdateEnable() !== true) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROER,
                'errcode' => '',
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($product_id))
            );
            exit;
        }

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
                'errcode' => '',
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
                'errcode' => '',
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
                    'errcode' => '',
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
                    'errcode' => '',
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

            echo $this->objJson->encode(array('status'  => OWNERSSTORE_STATUS_SUCCESS));
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
        GC_Utils::gfPrintLog('###Auto Update End###');
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
    }

    /**
     * ロックオンからのアクセスかどうかを確認する.
     *
     * @return boolesan
     */
    function isValidIP() {
        if (isset($_SERVER['REMOTE_ADDR'])
        && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {

            return true;
        }

        return false;
    }

    /**
     * 自動アップデートが有効かどうかを判定する.
     *
     * @return boolean
     */
    function autoUpdateEnable() {
        $product_id = $this->objForm->getValue('product_id');

        $where = 'product_id = ?';
        $objQuery = new SC_Query();
        $arrRet = $objQuery->select('auto_update_flg', 'dtb_module', $where, array($product_id));

        if (isset($arrRet[0]['auto_update_flg'])
        && $arrRet[0]['auto_update_flg'] === '1') {

            return true;
        }

        return false;
    }
}
?>
