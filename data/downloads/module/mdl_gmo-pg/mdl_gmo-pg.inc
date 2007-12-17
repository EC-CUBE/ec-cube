<?php
/**
 * @copyright 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: mdl_epsilon.inc 7162 2006-11-18 09:53:33Z kakinaka $
 * @link      http://www.lockon.co.jp/
 */
require_once(DATA_PATH . "module/Request.php");

// モジュールID
define("MDL_GMOPG_ID", 11);

/**
 * クレジット決済
 * 本番稼働の際は下記URLを本番用に書き換える
 */
define("GMO_ENTRY_URL", "http://mod-i.ccsware.net/ohayou/EntryTran.php");
define("GMO_EXEC_URL",  "http://mod-i.ccsware.net/ohayou/ExecTran.php");
define("GMO_3D_URL",    "https://mod-i.ccsware.net/ohayou/3dVerify.php");

// mdl_gmo-pg.incのフルパス
define('GMO_INC_PATH', realpath(__FILE__));

// 3Dセキュア戻り用URL
define("GMO_RETURL", SSL_URL . "shopping/load_payment_module.php");

// GMOログパス
define('GMO_LOG_PATH', DATA_PATH . 'logs/gmo-pg.log');

// クレジット分割回数
$arrPayMethod = array(
    '1-0' => "一括払い",
    '2-3' => "分割3回払い",
    '2-6' => "分割6回払い",
    '2-10'=> "分割10回払い",
    '2-15'=> "分割15回払い",
    '2-20'=> "分割20回払い",
    '5-0' => "リボ払い"
);

/**
 * モジュール設定を取得する。
 *
 * @param string $where
 * @param array $arrWhereVal
 * @return array
 */
function sfGetPaymentDB($where = "", $arrWhereVal = array()){
    $arrVal = array(MDL_GMOPG_ID);
    $arrVal = array_merge($arrVal, $arrWhereVal);

    $sql =<<<END
SELECT
    module_id,
    memo01 as gmo_shopid,
    memo02 as gmo_shoppass,
    memo03 as gmo_tenantno,
    memo04 as gmo_3d
FROM
    dtb_payment
WHERE
    module_id = ?
END;

    $sql .= $where;
    $objQuery = new SC_Query;
    $arrRet = $objQuery->getAll($sql, $arrVal);

    return $arrRet;
}

/**
 * 共通の表示処理
 *
 * @param LC_Page $objPage
 * @param integer $payment_id
 * @return LC_Page $objPage
 */
function sfGmoDisp($objPage, $payment_id) {
    $objQuery = new SC_Query();
    // 支払い方法の説明画像を取得する。
    $arrRet = $objQuery->select("payment_method, payment_image", "dtb_payment", "payment_id = ?", array($payment_id));
    $objPage->tpl_payment_method = $arrRet[0]['payment_method'];
    $objPage->tpl_payment_image = $arrRet[0]['payment_image'];
    return $objPage;
}
?>
