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
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * パラメーター設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Parameter extends AbstractAdminPage
{
    /** 定数キーとなる配列 */
    public $arrKeys;

    /** 定数コメントとなる配列 */
    public $arrComments;

    /** 定数値となる配列 */
    public $arrValues;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'system/parameter.tpl';
        $this->tpl_subno = 'parameter';
        $this->tpl_mainno = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'パラメーター設定';
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
        $masterData = Application::alias('eccube.db.master_data');

        // キーの配列を生成
        $this->arrKeys = $this->getParamKeys($masterData);

        switch ($this->getMode()) {
            case 'update':
                // データの引き継ぎ
                $this->arrForm = $_POST;

                // エラーチェック
                $this->arrErr = $this->errorCheck($this->arrKeys, $this->arrForm);
                // エラーの無い場合は update
                if (empty($this->arrErr)) {
                    $this->update($this->arrKeys, $this->arrForm);
                    $this->tpl_onload = "window.alert('パラメーターの設定が完了しました。');";
                } else {
                    $this->arrValues = Utils::getHash2Array($this->arrForm, $this->arrKeys);
                    $this->tpl_onload = "window.alert('エラーが発生しました。入力内容をご確認下さい。');";
                }
                break;
            default:
                break;
        }

        if (empty($this->arrErr)) {
            $this->arrValues = Utils::getHash2Array($masterData->getDBMasterData('mtb_constants'));
        }

        // コメント, 値の配列を生成
        $this->arrComments = Utils::getHash2Array($masterData->getDBMasterData('mtb_constants',
                                                        array('id', 'remarks', 'rank')));
    }

    /**
     * パラメーター情報を更新する.
     *
     * 画面の設定値で mtb_constants テーブルの値とキャッシュを更新する.
     *
     * @access private
     * @return void
     */
    public function update(&$arrKeys, &$arrForm)
    {
        $data = array();
        $masterData = Application::alias('eccube.db.master_data');
        foreach ($arrKeys as $key) {
            $data[$key] = $arrForm[$key];
        }

        // DBのデータを更新
        $masterData->updateMasterData('mtb_constants', array(), $data);

        // キャッシュを生成
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));
    }

    /**
     * エラーチェックを行う.
     *
     * @access private
     * @param  array $arrForm $_POST 値
     * @return void
     */
    public function errorCheck(&$arrKeys, &$arrForm)
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $arrForm);
        for ($i = 0; $i < count($arrKeys); $i++) {
            $objErr->doFunc(array($arrKeys[$i],
                                  $arrForm[$arrKeys[$i]]),
                            array('EXIST_CHECK_REVERSE', 'EVAL_CHECK'));
        }

        return $objErr->arrErr;
    }

    /**
     * パラメーターのキーを配列で返す.
     *
     * @access private
     * @param MasterData $masterData
     * @return array パラメーターのキーの配列
     */
    public function getParamKeys(&$masterData)
    {
        $keys = array();
        $i = 0;
        foreach ($masterData->getDBMasterData('mtb_constants') as $key => $val) {
            $keys[$i] = $key;
            $i++;
        }

        return $keys;
    }
}
