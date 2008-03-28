<?php
/**
 * モジュール設定の取得・更新を行う
 *
 */
class Mdl_Cybs_Config {
    var $arrConfig;

    /**
     * dtb_paymentのmemo**へ登録する項目を増やす場合は、
     * この配列にmemoに対応するキー名を追加し、
     * テンプレート側で追加したキー名を使用する
     *
     * @see $this->_getConfig()
     * @see $this->createSqlArray()
     * @var array
     */
    var $arrPaymentMemoCols = array(
        'memo01' => 'cybs_request_url',
        'memo02' => 'cybs_merchant_id',
        'memo03' => 'cybs_ondemand_use',
        'memo04' => 'cybs_3d_use'
    );

    /**
     * Mdl_Cybs_Configのインスタンスを取得する.
     * インスタンス生成はnew演算子を使用せずgetInstanse()を使用する
     *
     * @return Mdl_Cybs_Config
     */
    function &getInstanse() {
        static $_CybsConfigObj;

        if ($_CybsConfigObj == null) {
            $_CybsConfigObj = new Mdl_Cybs_Config();
        }
        return $_CybsConfigObj;
    }

    /**
     * 設定を取得する
     *
     * @param string $key
     * @return array|null
     */
    function getConfig($key = null) {
        if (empty($this->arrConfig)) {
            $this->arrConfig = $this->_getConfig();
        }

        // 引数が無い場合は全てのデータを返す
        if (empty($key)) {
            return $this->arrConfig;
        }

        // $keyが引数で渡された場合は$keyに対応する値を返す
        return isset($this->arrConfig[$key])
            ? $this->arrConfig[$key]
            : null;
    }

    /**
     * DBから設定を取得する
     *
     * @return array
     */
    function _getConfig() {
        // memo01~memo10
        $arrMemo = array();
        foreach ($this->arrPaymentMemoCols as $k => $v) {
            $arrMemo[] = "$k as $v";
        }
        $memoCols = implode(',', $arrMemo);
        $sql =<<<END
SELECT
    module_id,
    $memoCols
FROM
    dtb_payment
WHERE
    module_id = ?
END;
        $objQuery = new SC_Query;
        $arrRet = $objQuery->getAll($sql, array(MDL_CYBS_ID));
        return isset($arrRet[0]) ? $arrRet[0] : array();
    }

    /**
     * DBへ設定を登録する.
     *
     * @param array $arrConfig
     */
    function registerConfig($arrConfig) {
        $table = 'dtb_payment';
        $where = 'module_id = ' . MDL_CYBS_ID;

        $objQuery = new SC_Query;
        $count = $objQuery->count($table, $where);

        if ($count) {
            $objQuery->update($table, $arrConfig, $where);
        } else {
            $objQuery->insert($table, $arrConfig);
        }
    }

    /**
     * Insert/Update用の連想配列を生成する
     *
     * @param SC_FormParam $objForm
     * @return array
     */
    function createSqlArray($objForm) {
        $objSess = new SC_Session;

        $arrData = array();
        $arrData["payment_method"] = "サイバーソースクレジット";
        $arrData["fix"] = 3;
        $arrData["module_id"] = MDL_CYBS_ID;
        $arrData["module_path"] = MODULE_PATH . "mdl_cybs/mdl_cybs_credit.php";
        // memo01~memo10
        foreach ($this->arrPaymentMemoCols as $k => $v) {
            $arrData[$k] = $objForm->getValue($v);
        }
        $arrData["del_flg"] = "0";
        $arrData["creator_id"] = $objSess->member_id;
        $arrData["update_date"] = "NOW()";

        return $arrData;
    }
}
?>
