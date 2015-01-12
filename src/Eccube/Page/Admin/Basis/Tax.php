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

namespace Eccube\Page\Admin\Basis;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\TaxRuleHelper;
use Eccube\Framework\Util\Utils;

/**
 * 税率設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Tax extends AbstractAdminPage
{
    /** エラー情報 */
    public $arrErr;

    /** @var Date objDate */
    public $objDate;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/tax.tpl';
        $this->tpl_subno = 'tax';
        $this->tpl_mainno = 'basis';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '税率設定';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXCALCRULE = $masterData->getMasterData('mtb_taxrule');

        //適用時刻の項目値設定
        $this->objDate = Application::alias('eccube.date');
        //適用時間の年を、「現在年~現在年＋2」の範囲に設定
        for ($year=date("Y"); $year<=date("Y") + 2;$year++) {
            $arrYear[$year] = $year;
        }
        $this->arrYear = $arrYear;

        for ($minutes=0; $minutes< 60; $minutes++) {
            $arrMinutes[$minutes] = $minutes;
        }
        $this->arrMinutes = $arrMinutes;

        $this->arrEnable = array( '1' => '有効', '0' => '無効');

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        $objTaxRule = new TaxRuleHelper();
        $objFormParam = Application::alias('eccube.form_param');

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // POST値をセット
        $objFormParam->setParam($_POST);

        // POST値の入力文字変換
        $objFormParam->convParam();

        //tax_rule_idを変数にセット
        $tax_rule_id = $objFormParam->getValue('tax_rule_id');

        // モードによる処理切り替え
        switch ($this->getMode()) {
            // 共通設定登録
            case 'param_edit':
                $arrErr = $this->lfCheckError($objFormParam, $objTaxRule);
                if (Utils::isBlank($arrErr['product_tax_flg'])) {
                    // POST値の引き継ぎ
                    $arrParam = $objFormParam->getHashArray();
                    // 登録実行
                    if ($this->doParamRegist($arrParam)) {
                        // 完了メッセージ
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                } else {
                    // エラーが存在する場合、メッセージを表示する為に代入
                    $this->arrErr['product_tax_flg'] = $arrErr['product_tax_flg'];
                }
                
                break;

            // 編集処理
            case 'edit':
                // エラーチェック
                $this->arrErr = $this->lfCheckError($objFormParam, $objTaxRule);

                if (count($this->arrErr) <= 0) {
                    // POST値の引き継ぎ
                    $arrParam = $objFormParam->getHashArray();
                    // 登録実行
                    $res_tax_rule_id = $this->doRegist($tax_rule_id, $arrParam, $objTaxRule);
                    if ($res_tax_rule_id !== FALSE) {
                        // 完了メッセージ
                        $this->tpl_onload = "alert('登録が完了しました。');";

                        // リロード
                        Application::alias('eccube.response')->reload();
                    }
                } elseif (Utils::isBlank($this->arrErr['tax_rule_id'])) {
                    // 税率ID以外のエラーの場合、ID情報を引き継ぐ
                    $this->tpl_tax_rule_id = $tax_rule_id;
                }

                break;

            // 編集前処理
            case 'pre_edit':
                $TaxRule = $objTaxRule->getTaxRuleData($tax_rule_id);

                $tmp = explode(" ", $TaxRule['apply_date']);
                $tmp_ymd = explode("-", $tmp[0]);
                $TaxRule['apply_date_year'] = $tmp_ymd[0];
                $TaxRule['apply_date_month'] = $tmp_ymd[1];
                $TaxRule['apply_date_day'] = $tmp_ymd[2];
                $tmp_hm = explode(":", $tmp[1]);
                $TaxRule['apply_date_hour'] = $tmp_hm[0];
                $TaxRule['apply_date_minutes'] = $tmp_hm[1];

                $objFormParam->setParam($TaxRule);

                // POSTデータを引き継ぐ
                $this->tpl_tax_rule_id = $tax_rule_id;
                break;

            // 削除
            case 'delete':
                $objTaxRule->deleteTaxRuleData($tax_rule_id);

                // リロード
                Application::alias('eccube.response')->reload();
                break;

            default:
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();

        // 税規約情報読み込み
        $this->arrTaxrule = $objTaxRule->getTaxRuleList();
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('商品個別 税率設定機能', 'product_tax_flg', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'), OPTION_PRODUCT_TAX_RULE);
        $objFormParam->addParam('税規約ID', 'tax_rule_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('消費税率', 'tax_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('課税規則', 'calc_rule', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

        // 適用日時
        $objFormParam->addParam('適用年', 'apply_date_year', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('適用月', 'apply_date_month', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('適用日', 'apply_date_day', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('適用時', 'apply_date_hour', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('適用分', 'apply_date_minutes', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }

    /**
     * 登録処理を実行.
     *
     * @param  integer  $tax_rule_id
     * @param  TaxRuleHelper   $objTaxRule
     * @return multiple
     */
    public function doRegist($tax_rule_id, $arrParam, TaxRuleHelper $objTaxRule)
    {
        $apply_date = Utils::sfGetTimestampistime(
                $arrParam['apply_date_year'],
                sprintf("%02d", $arrParam['apply_date_month']),
                sprintf("%02d", $arrParam['apply_date_day']),
                sprintf("%02d", $arrParam['apply_date_hour']),
                sprintf("%02d", $arrParam['apply_date_minutes'])
                );

        $calc_rule = $arrParam['calc_rule'];
        $tax_rate = $arrParam['tax_rate'];

        return $objTaxRule->setTaxRule($calc_rule, $tax_rate, $apply_date, $tax_rule_id);
    }

    /**
     * 共通設定の登録処理を実行.
     *
     * @param  array   $arrParam
     * @return integer
     */
    public function doParamRegist($arrParam)
    {
        $arrData = array();
        foreach ($arrParam as $key => $val) {
            switch ($key) {
            case 'product_tax_flg':
                $arrData['OPTION_PRODUCT_TAX_RULE'] = $val;
                break;
            default:
            }
        }
        $masterData = Application::alias('eccube.db.master_data');
        // DBのデータを更新
        $res = $masterData->updateMasterData('mtb_constants', array(), $arrData);
        // キャッシュを生成
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));

        return $res;
    }

    /**
     * 入力エラーチェック.
     *
     * @param FormParam $objFormParam
     * @return array $objErr->arrErr エラー内容
     */
    public function lfCheckError(&$objFormParam, TaxRuleHelper &$objTaxRule)
    {
        $arrErr = $objFormParam->checkError();
        $arrForm = $objFormParam->getHashArray();
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrForm);

        // tax_rule_id の正当性チェック
        if (!empty($arrForm['tax_rule_id'])) {
            if (!Utils::sfIsInt($arrForm['tax_rule_id'])
                || !$objTaxRule->getTaxRuleData($arrForm['tax_rule_id'])
            ) {
                // tax_rule_idが指定されていて、且つその値が不正と思われる場合はエラー
                $arrErr['tax_rule_id'] = '※ 税規約IDが不正です<br />';
            }
        }

        // 適用日時チェック
        $objErr->doFunc(array('適用日時', 'apply_date_year', 'apply_date_month', 'apply_date_day'), array('CHECK_DATE'));
        if (Utils::isBlank($objErr->arrErr['apply_date_year']) && $arrForm['tax_rule_id'] != '0') {
            $apply_date = Utils::sfGetTimestampistime(
                    $arrForm['apply_date_year'],
                    sprintf("%02d", $arrForm['apply_date_month']),
                    sprintf("%02d", $arrForm['apply_date_day']),
                    sprintf("%02d", $arrForm['apply_date_hour']),
                    sprintf("%02d", $arrForm['apply_date_minutes'])
                    );

            // 税規約情報読み込み
            $arrTaxRuleByTime = $objTaxRule->getTaxRuleByTime($apply_date);

            // 編集中のレコード以外に同じ消費税率、課税規則が存在する場合
            if (
                !Utils::isBlank($arrTaxRuleByTime)
                && $arrTaxRuleByTime['tax_rule_id'] != $arrForm['tax_rule_id']
                && $arrTaxRuleByTime['apply_date'] == $apply_date
            ) {
                $arrErr['apply_date'] = '※ 既に同じ適用日時で登録が存在します。<br />';
            }
        }
        if (!Utils::isBlank($objErr->arrErr)) {
            $arrErr = array_merge($arrErr, $objErr->arrErr);
        }

        return $arrErr;
    }
}
