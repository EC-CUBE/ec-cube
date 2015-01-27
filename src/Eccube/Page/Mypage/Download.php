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

namespace Eccube\Page\Mypage;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\Customer;
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\MobileUserAgent;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\Helper\MobileHelper;
use Eccube\Framework\Helper\PluginHelper;
use Eccube\Framework\Util\Utils;

/**
 * ダウンロード商品ダウンロード のページクラス.
 *
 * @package Page
 * @author CUORE CO.,LTD.
 */
class Download extends AbstractPage
{
    /** フォームパラメーターの配列 */
    public $objFormParam;

    /** 基本Content-Type */
    public $defaultContentType = 'Application/octet-stream';

    /** 拡張Content-Type配列
     * Application/octet-streamで対応出来ないファイルタイプのみ拡張子をキーに記述する
     * 拡張子が本配列に存在しない場合は $defaultContentTypeを利用する */
    public $arrContentType = array('apk' => 'application/vnd.android.package-archive',
                                'pdf' => 'application/pdf'
        );

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        ob_end_clean();
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        // ログインチェック
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        if (!$objCustomer->isLoginSuccess(true)) {
            Utils::sfDispSiteError(DOWNFILE_NOT_FOUND, '', true);
        }

        // パラメーターチェック
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam);
        // GET、SESSION['customer']値の取得
        $objFormParam->setParam($_SESSION['customer']);
        $objFormParam->setParam($_GET);
        $this->arrErr = $this->lfCheckError($objFormParam);
        if (count($this->arrErr)!=0) {
            Utils::sfDispSiteError(DOWNFILE_NOT_FOUND, '', true);
        }

    }

    /**
     * Page のResponse.
     *
     * todo たいした処理でないのに異常に処理が重い
     * @return void
     */
    public function sendResponse()
    {
        // TODO sendResponseをオーバーライドしている為、afterフックポイントが実行されない.直接実行する.(#1790)
        $objPlugin = PluginHelper::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_Mypage_DownLoad_action_after', array($this));

        $this->objDisplay->noAction();

        // パラメーター取得
        $customer_id = $_SESSION['customer']['customer_id'];
        $order_id = $_GET['order_id'];
        $product_class_id = $_GET['product_class_id'];

        //DBから商品情報の読込
        $arrForm = $this->lfGetRealFileName($customer_id, $order_id, $product_class_id);

        //ファイル情報が無い場合はNG
        if ($arrForm['down_realfilename'] == '') {
            Utils::sfDispSiteError(DOWNFILE_NOT_FOUND, '', true);
        }
        //ファイルそのものが無い場合もとりあえずNG
        $realpath = DOWN_SAVE_REALDIR . $arrForm['down_realfilename'];
        if (!file_exists($realpath)) {
            Utils::sfDispSiteError(DOWNFILE_NOT_FOUND, '', true);
        }
        //ファイル名をエンコードする Safariの対策はUTF-8で様子を見る
        $encoding = 'Shift_JIS';
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
            $encoding = 'UTF-8';
        }
        $sdown_filename = mb_convert_encoding($arrForm['down_filename'], $encoding, 'auto');

        // flushなどを利用しているので、現行のDisplayは利用できません。
        // DisplayやResponseに大容量ファイルレスポンスが実装されたら移行可能だと思います。

        // ダウンロード実行 モバイル端末はダウンロード方法が異なる
        if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
            // キャリアがAUのモバイル端末はさらにダウンロード方法が異なる
            if (MobileUserAgent::getCarrier() == 'ezweb') {
                // AUモバイル
                $this->lfMobileAuDownload($realpath, $sdown_filename);
            } else {
                // AU以外のモバイル
                $this->lfMobileDownload($realpath, $sdown_filename);
            }
        } else {
            // PC、スマフォ
            $this->lfDownload($realpath, $sdown_filename);
        }
    }

    /**
     * 商品情報の読み込みを行う.
     *
     * @param  integer $customer_id      会員ID
     * @param  integer $order_id         受注ID
     * @param  integer $product_class_id 商品規格ID
     * @return string   商品情報の配列
     */
    public function lfGetRealFileName($customer_id, $order_id, $product_class_id)
    {
        $objQuery = Application::alias('eccube.query');
        $col = <<< __EOS__
            pc.down_realfilename AS down_realfilename,
            pc.down_filename AS down_filename
__EOS__;

        $table = <<< __EOS__
            dtb_order AS o
            JOIN dtb_order_detail AS od USING(order_id)
            JOIN dtb_products_class AS pc USING(product_id, product_class_id)
__EOS__;

        $dbFactory = Application::alias('eccube.db.factory');
        $where = 'o.customer_id = ? AND o.order_id = ? AND od.product_class_id = ?';
        $where .= ' AND ' . $dbFactory->getDownloadableDaysWhereSql('o');
        $where .= ' = 1';
        $arrWhereVal = array($customer_id, $order_id, $product_class_id);
        $arrRet = $objQuery->select($col, $table, $where, $arrWhereVal);

        return $arrRet[0];
    }

    /* パラメーター情報の初期化 */

    /**
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('customer_id', 'customer_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('order_id', 'order_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('product_class_id', 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK'));
    }

    /* 入力内容のチェック */

    /**
     * @param FormParam $objFormParam
     */
    public function lfCheckError(&$objFormParam)
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        return $objErr->arrErr;
    }

    /**
     * モバイル端末用ヘッダー出力処理
     *
     * @param string $realpath       ダウンロードファイルパス
     * @param string $sdown_filename ダウンロード時の指定ファイル名
     */
    public function lfMobileHeader($realpath, $sdown_filename)
    {
        /* @var $objHelperMobile MobileHelper */
        $objHelperMobile = Application::alias('eccube.helper.mobile');
        //ファイルの拡張子からコンテンツタイプを取得する
        $mime_type = $objHelperMobile->getMIMEType($realpath);
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename=' . $sdown_filename);
        header('Accept-Ranges: bytes');
        header('Last-Modified: ' . gmdate('D,d M Y H:i:s') . ' GMT');
        header('Cache-Control: public');
    }

    /**
     * モバイル端末（AU）ダウンロード処理
     *
     * @param string $realpath       ダウンロードファイルパス
     * @param string $sdown_filename ダウンロード時の指定ファイル名
     */
    public function lfMobileAuDownload($realpath, $sdown_filename)
    {
        //モバイル用ヘッダー出力
        $this->lfMobileHeader($realpath, $sdown_filename);
        //ファイルサイズを取得する
        $file_size = filesize($realpath);
        //読み込み
        $fp = fopen($realpath, 'rb');
        if (isset($_SERVER['HTTP_RANGE'])) {
            // 二回目以降のリクエスト
            list($range_offset, $range_limit) = sscanf($_SERVER['HTTP_RANGE'], 'bytes=%d-%d');
            $content_range = sprintf('bytes %d-%d/%d', $range_offset, $range_limit, $file_size);
            $content_length = $range_limit - $range_offset + 1;
            fseek($fp, $range_offset, SEEK_SET);
            header('HTTP/1.1 206 Partial Content');
            header('Content-Lenth: ' . $content_length);
            header('Content-Range: ' . $content_range);
        } else {
            // 一回目のリクエスト
            $content_length = $file_size;
            header('Content-Length: ' . $content_length);
        }
        echo fread($fp, $content_length);
        Utils::sfFlush();
    }

    /**
     * モバイル端末（AU以外）ダウンロード処理
     *
     * @param string $realpath       ダウンロードファイルパス
     * @param string $sdown_filename ダウンロード時の指定ファイル名
     */
    public function lfMobileDownload($realpath, $sdown_filename)
    {
        //モバイル用ヘッダー出力
        $this->lfMobileHeader($realpath, $sdown_filename);
        //ファイルサイズを取得する
        $file_size = filesize($realpath);

        //出力用バッファをクリアする
        @ob_end_clean();

        //HTTP_RANGEがセットされていた場合
        if (isset($_SERVER['HTTP_RANGE'])) {
            // 二回目以降のリクエスト
            list($a, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            list($range) = explode(',', $range, 2);
            list($range, $range_end) = explode('-', $range);
            $range=intval($range);

            if (!$range_end) {
                $range_end=$file_size-1;
            } else {
                $range_end=intval($range_end);
            }

            $new_length = $range_end-$range+1;
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range-$range_end/$file_size");
        } else {
            // 一回目のリクエスト
            $new_length=$file_size;
            header('Content-Length: '.$file_size);
        }

        //ファイル読み込み
        $chunksize = 1*(DOWNLOAD_BLOCK*1024);
        $bytes_send = 0;
        if ($realpath = fopen($realpath, 'r')) {
            // 二回目以降のリクエスト
            if (isset($_SERVER['HTTP_RANGE'])) fseek($realpath, $range);

            while (!feof($realpath) && (!connection_aborted()) && ($bytes_send<$new_length)) {
                $buffer = fread($realpath, $chunksize);
                print($buffer);
                Utils::sfFlush();
                Utils::extendTimeOut();
                $bytes_send += strlen($buffer);
            }
            fclose($realpath);
        }
        die();
    }

    /**
     * モバイル端末以外ダウンロード処理
     *
     * @param string $realpath       ダウンロードファイルパス
     * @param string $sdown_filename ダウンロード時の指定ファイル名
     */
    public function lfDownload($realpath, $sdown_filename)
    {
        // 拡張子を取得
        $extension = pathinfo($realpath, PATHINFO_EXTENSION);
        $contentType = $this->defaultContentType;
        // 拡張ContentType判定（拡張子をキーに拡張ContentType対象か判断）
        if (isset($this->arrContentType[$extension])) {
            // 拡張ContentType対象の場合は、ContentTypeを変更
            $contentType = $this->arrContentType[$extension];
        }
        header('Content-Type: '.$contentType);
        //ファイル名指定
        header('Content-Disposition: attachment; filename="' . $sdown_filename . '"');
        header('Content-Transfer-Encoding: binary');
        //キャッシュ無効化
        header('Expires: Mon, 26 Nov 1962 00:00:00 GMT');
        header('Last-Modified: ' . gmdate('D,d M Y H:i:s') . ' GMT');
        //IE6+SSL環境下は、キャッシュ無しでダウンロードできない
        header('Cache-Control: private');
        header('Pragma: private');
        //ファイルサイズ指定
        $zv_filesize = filesize($realpath);
        header('Content-Length: ' . $zv_filesize);
        //ファイル読み込み
        $handle = fopen($realpath, 'rb');
        if ($handle === false) {
            Utils::sfDispSiteError(DOWNFILE_NOT_FOUND, '', true);
            Application::alias('eccube.response')->actionExit();
        }
        while (!feof($handle)) {
            echo fread($handle, DOWNLOAD_BLOCK*1024);
            Utils::sfFlush();
            Utils::extendTimeOut();
        }
        fclose($handle);
    }
}
