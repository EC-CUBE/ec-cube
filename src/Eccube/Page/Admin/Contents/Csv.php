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

namespace Eccube\Page\Admin\Contents;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\CsvHelper;
use Eccube\Framework\Util\Utils;

/**
 * CSV項目設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Csv extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'contents/csv.tpl';
        $this->tpl_subno = 'csv';
        $this->tpl_mainno = 'contents';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'CSV出力設定';

        /* @var $objCSV CsvHelper */
        $objCSV = Application::alias('eccube.helper.csv');
        $this->arrSubnavi = $objCSV->arrSubnavi; // 別名
        $this->tpl_subno_csv = $objCSV->arrSubnavi[1]; //デフォルト
        $this->arrSubnaviName = $objCSV->arrSubnaviName; // 表示名
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
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター設定
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->setParam($_GET);
        $objFormParam->convParam();

        // CSV_IDの読み込み
        $this->tpl_subno_csv = $objFormParam->getValue('tpl_subno_csv');
        $this->tpl_csv_id = $this->lfGetCsvId($this->tpl_subno_csv);

        switch ($this->getMode()) {
            case 'confirm':
                // 入力パラメーターチェック
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    // 更新
                    $this->tpl_is_update = $this->lfUpdCsvOutput($this->tpl_csv_id, $objFormParam->getValue('output_list'));
                }
                break;
            case 'defaultset':
                //初期値に戻す
                $this->tpl_is_update = $this->lfSetDefaultCsvOutput($this->tpl_csv_id);
                break;
            default:
                break;
        }
        $this->arrSelected = $this->lfGetSelected($this->tpl_csv_id);
        $this->arrOptions = $this->lfGetOptions($this->tpl_csv_id);
        $this->tpl_subtitle .= '＞' . $this->arrSubnaviName[ $this->tpl_csv_id ];

        if ($this->tpl_is_update) {
            $this->tpl_onload = "window.alert('正常に更新されました。');";
        }

    }

    /**
     * パラメーター情報の初期化
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('編集種別', 'tpl_subno_csv', STEXT_LEN, 'a', array('ALNUM_CHECK', 'MAX_LENGTH_CHECK'), 'product');
        $objFormParam->addParam('出力設定リスト', 'output_list', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        //デフォルト値で上書き
        $objFormParam->setParam(array('tpl_subno_csv' => 'product'));
    }

    /**
     * CSVカラム設定の読み込み
     *
     * @param  integer $csv_id         CSV ID
     * @param  integer $csv_status_flg 読み込む対象のフラグ CSV_COLUMN_STATUS_FLG_ENABLE or ''
     * @return integer   SwapArrayしたカラム設定
     */
    public function lfGetCSVColumn($csv_id, $csv_status_flg = '', $order ='rank, no')
    {
        /* @var $objCSV CsvHelper */
        $objCSV = Application::alias('eccube.helper.csv');
        if (Utils::sfIsInt($csv_id)) {
            if ($csv_status_flg !='') {
                $arrData = $objCSV->sfGetCsvOutput($csv_id, 'status = ?', array($csv_status_flg), $order);
            } else {
                $arrData = $objCSV->sfGetCsvOutput($csv_id, '', array(), $order);
            }
            $arrData = Utils::sfSwapArray($arrData);
        } else {
            $arrData = array();
        }

        return $arrData;
    }

    /**
     * 選択済みカラム列情報を取得
     *
     * @param  integer $csv_id CSV ID
     * @return array   選択済みカラム列情報
     */
    public function lfGetSelected($csv_id)
    {
        $arrData = $this->lfGetCSVColumn($csv_id, CSV_COLUMN_STATUS_FLG_ENABLE);
        if (!isset($arrData['no'])) {
            $arrData['no'] = array();
        }

        return $arrData['no'];
    }

    /**
     * カラム列情報と表示名情報を取得
     *
     * @param  integer $csv_id CSV ID
     * @return array   選択済みカラム列情報
     */
    public function lfGetOptions($csv_id)
    {
        $arrData = $this->lfGetCSVColumn($csv_id);
        if (!isset($arrData['no'])) {
            $arrData['no'] = array();
            $arrData['disp_name'] = array();
        }
        $arrData = Utils::sfArrCombine($arrData['no'], $arrData['disp_name']);

        return $arrData;
    }

    /**
     * CSV名からCSV_IDを取得する。
     *
     * @param  string  $subno_csv CSV名
     * @return integer CSV_ID
     */
    public function lfGetCsvId($subno_csv)
    {
        /* @var $objCSV CsvHelper */
        $objCSV = Application::alias('eccube.helper.csv');
        $arrKey = array_keys($objCSV->arrSubnavi, $subno_csv);
        $csv_id = $arrKey[0];
        if (!Utils::sfIsInt($csv_id)) {
            //初期値取りだし
            $arrKey = array_keys($objCSV->arrSubnavi);
            $csv_id = $arrKey[0];
        }

        return $csv_id;
    }

    /**
     * CSV出力項目設定を初期化する
     *
     * @param  integer $csv_id CSV_ID
     * @return boolean 成功:true
     */
    public function lfSetDefaultCsvOutput($csv_id)
    {
        $arrData = $this->lfGetCSVColumn($csv_id, '', 'no');
        if (!isset($arrData['no'])) {
            $arrData['no'] = array();
        }

        return $this->lfUpdCsvOutput($csv_id, $arrData['no']);
    }

    /**
     * CSV出力項目設定を更新する処理
     *
     * @param  integer $csv_id  CSV_ID
     * @param  array   $arrData 有効にするCSV列データ配列
     * @return boolean 成功:true
     */
    public function lfUpdCsvOutput($csv_id, $arrData = array())
    {
        $objQuery = Application::alias('eccube.query');
        // ひとまず、全部使用しないで更新する
        $table = 'dtb_csv';
        $where = 'csv_id = ?';
        $arrWhereVal = array($csv_id);
        $arrUpdVal = array('status' => '2', 'rank' => NULL, 'update_date' => 'CURRENT_TIMESTAMP');

        $objQuery->begin();
        $objQuery->update($table, $arrUpdVal, $where, $arrWhereVal);
        // 使用するものだけ、再更新する。
        if (is_array($arrData)) {
            $where .= ' AND no = ?';
            $arrUpdVal = array('status' => '1');
            foreach ($arrData as $key => $val) {
                $arrWhereVal = array($csv_id, $val);
                $arrUpdVal['rank'] = $key + 1;
                $objQuery->update($table, $arrUpdVal, $where, $arrWhereVal);
            }
        }
        $objQuery->commit();

        return true;
    }
}
