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

namespace Eccube\Page\Admin\System;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;

/**
 * 高度なデータベース管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Editdb extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'system/editdb.tpl';
        $this->tpl_subno    = 'editdb';
        $this->tpl_mainno   = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = '高度なデータベース管理';
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
        $objFormParam = Application::alias('eccube.form_param');

        // パラメーターの初期化
        $this->initForm($objFormParam, $_POST);

        switch ($this->getMode()) {
            case 'confirm' :
                $message = $this->lfDoChange($objFormParam);
                if (!is_array($message) && $message != '') {
                    $this->tpl_onload = $message;
                }
                break;
            default:
                break;
        }

        //インデックスの現在値を取得
        $this->arrForm = $this->lfGetIndexList();
    }

    /**
     * フォームパラメーター初期化
     *
     * @param  FormParam $objFormParam
     * @param  array  $arrParams    $_POST値
     * @return void
     */
    public function initForm(&$objFormParam, &$arrParams)
    {
        $objFormParam->addParam('モード', 'mode', INT_LEN, 'n', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('テーブル名', 'table_name');
        $objFormParam->addParam('カラム名', 'column_name');
        $objFormParam->addParam('インデックス', 'indexflag');
        $objFormParam->addParam('インデックス（変更後）', 'indexflag_new');
        $objFormParam->setParam($arrParams);
    }

    /**
     * @param FormParam $objFormParam
     */
    public function lfDoChange(&$objFormParam)
    {
        $objQuery = Application::alias('eccube.query');
        $arrTarget = $this->lfGetTargetData($objFormParam);
        $message = '';
        if (is_array($arrTarget) && count($arrTarget) == 0) {
            $message = "window.alert('変更対象となるデータはありませんでした。');";

            return $message;
        } elseif (!is_array($arrTarget) && $arrTarget != '') {
            return $arrTarget; // window.alert が返ってきているはず。
        }

        // 変更対象の設定変更
        foreach ($arrTarget as $item) {
            $index_name = $item['table_name'] . '_' . $item['column_name'] . '_key';
            $arrField = array('fields' => array($item['column_name'] => array()));
            if ($item['indexflag_new'] == '1') {
                $objQuery->createIndex($item['table_name'], $index_name, $arrField);
            } else {
                $objQuery->dropIndex($item['table_name'], $index_name);
            }
        }
        $message = "window.alert('インデックスの変更が完了しました。');";

        return $message;
    }

    /**
     * @param FormParam $objFormParam
     */
    public function lfGetTargetData(&$objFormParam)
    {
        $objQuery = Application::alias('eccube.query');
        $arrIndexFlag    = $objFormParam->getValue('indexflag');
        $arrIndexFlagNew = $objFormParam->getValue('indexflag_new');
        $arrTableName    = $objFormParam->getValue('table_name');
        $arrColumnName   = $objFormParam->getValue('column_name');
        $arrTarget = array();
        $message = '';

        // 変更されている対象を走査
        for ($i = 1; $i <= count($arrIndexFlag); $i++) {
            //入力値チェック
            $param = array('indexflag' => $arrIndexFlag[$i],
                            'indexflag_new' => $arrIndexFlagNew[$i],
                            'table_name' => $arrTableName[$i],
                            'column_name' => $arrColumnName[$i]);
            /* @var $objErr CheckError */
            $objErr = Application::alias('eccube.check_error', $param);
            $objErr->doFunc(array('インデックス(' . $i . ')', 'indexflag', INT_LEN), array('NUM_CHECK'));
            $objErr->doFunc(array('インデックス変更後(' . $i . ')', 'indexflag_new', INT_LEN), array('NUM_CHECK'));
            $objErr->doFunc(array('インデックス変更後(' . $i . ')', 'indexflag_new', INT_LEN), array('NUM_CHECK'));
            $objErr->doFunc(array('テーブル名(' . $i . ')', 'table_name', STEXT_LEN), array('GRAPH_CHECK', 'EXIST_CHECK', 'MAX_LENGTH_CHECK'));
            $objErr->doFunc(array('カラム名(' . $i . ')', 'column_name', STEXT_LEN), array('GRAPH_CHECK', 'EXIST_CHECK', 'MAX_LENGTH_CHECK'));
            $arrErr = $objErr->arrErr;
            if (count($arrErr) != 0) {
                // 通常の送信ではエラーにならないはずです。
                $message = "window.alert('不正なデータがあったため処理を中断しました。');";

                return $message;
            }
            if ($param['indexflag'] != $param['indexflag_new']) {
                // 入力値がデータにある対象テーブルかのチェック
                if ($objQuery->exists('dtb_index_list', 'table_name = ? and column_name = ?', array($param['table_name'], $param['column_name']))) {
                    $arrTarget[] = $param;
                }
            }
        }

        return $arrTarget;
    }

    /**
     * インデックス設定を行う一覧を返す関数
     *
     * @return void
     */
    public function lfGetIndexList()
    {
        // データベースからインデックス設定一覧を取得する
        $objQuery = Application::alias('eccube.query');
        $objQuery->setOrder('table_name, column_name');
        $arrIndexList = $objQuery->select('table_name , column_name , recommend_flg, recommend_comment', 'dtb_index_list');

        $table = '';
        foreach ($arrIndexList as $key => $arrIndex) {
            // テーブルに対するインデックス一覧を取得
            if ($table !== $arrIndex['table_name']) {
                $table = $arrIndex['table_name'];
                $arrIndexes = $objQuery->listTableIndexes($table);
            }
            // インデックスが設定されているかを取得
            $idx_name = $table . '_' . $arrIndex['column_name'] . '_key';
            if (array_search($idx_name, $arrIndexes) === false) {
                $arrIndexList[$key]['indexflag'] = '';
            } else {
                $arrIndexList[$key]['indexflag'] = '1';
            }
        }

        return $arrIndexList;
    }
}
