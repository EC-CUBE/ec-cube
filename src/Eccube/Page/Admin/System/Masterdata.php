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
use Eccube\Framework\Query;
use Eccube\Framework\DB\DBFactory;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * マスターデータ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Masterdata extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'system/masterdata.tpl';
        $this->tpl_subno = 'masterdata';
        $this->tpl_mainno = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'マスターデータ管理';
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
        $this->arrMasterDataName = $this->getMasterDataNames(array('mtb_pref', 'mtb_zip', 'mtb_constants'));
        $masterData = Application::alias('eccube.db.master_data');

        switch ($this->getMode()) {
            case 'edit':
                // POST 文字列の妥当性チェック
                $this->masterDataName = $this->checkMasterDataName($_POST, $this->arrMasterDataName);
                $this->errorMessage = $this->checkUniqueID($_POST);

                if (empty($this->errorMessage)) {
                    // 取得したデータからマスターデータを生成
                    $this->registMasterData($_POST, $masterData, $this->masterDataName);
                    $this->tpl_onload = "window.alert('マスターデータの設定が完了しました。');";
                }
                // FIXME break 入れ忘れと思われる。そうでないなら、要コメント。

            case 'show':
                // POST 文字列の妥当性チェック
                $this->masterDataName = $this->checkMasterDataName($_POST, $this->arrMasterDataName);

                // DB からマスターデータを取得
                $this->arrMasterData =
                    $masterData->getDbMasterData($this->masterDataName);
                break;

            default:
                break;
        }

    }

    /**
     * マスターデータ名チェックを行う
     *
     * @access private
     * @param  array  $arrMasterDataName マスターデータテーブル名のリスト
     * @return string $master_data_name 選択しているマスターデータのテーブル名
     */
    public function checkMasterDataName(&$arrParams, &$arrMasterDataName)
    {
        if (in_array($arrParams['master_data_name'], $arrMasterDataName)) {
            $master_data_name = $arrParams['master_data_name'];

            return $master_data_name;
        } else {
            Utils::sfDispError('');
        }

    }

    /**
     * マスターデータ名を配列で取得する.
     *
     * @access private
     * @param  string[] $ignores 取得しないマスターデータ名の配列
     * @return array マスターデータ名の配列
     */
    public function getMasterDataNames($ignores = array())
    {
        /* @var $dbFactory DBFactory */
        $dbFactory = Application::alias('eccube.db.factory');
        $arrMasterDataName = $dbFactory->findTableNames('mtb_');

        $i = 0;
        foreach ($arrMasterDataName as $val) {
            foreach ($ignores as $ignore) {
                if ($val == $ignore) {
                    unset($arrMasterDataName[$i]);
                }
            }
            $i++;
        }

        return $arrMasterDataName;
    }

    /**
     * ID の値がユニークかチェックする.
     *
     * 重複した値が存在する場合はエラーメッセージを表示する.
     *
     * @access private
     * @return void|string エラーが発生した場合はエラーメッセージを返す.
     */
    public function checkUniqueID(&$arrParams)
    {
        $arrId = $arrParams['id'];
        for ($i = 0; $i < count($arrId); $i++) {
            $id = $arrId[$i];
            // 空の値は無視
            if ($arrId[$i] != '') {
                for ($j = $i + 1; $j < count($arrId); $j++) {
                    if ($id == $arrId[$j]) {
                        return $id . ' が重複しているため登録できません.';
                    }
                }
            }
        }
    }

    /**
     * マスターデータの登録.
     *
     * @access private{
     * @param  array  $arrParams        $_POST値
     * @param  MasterData $masterData       MasterData()
     * @param  string $master_data_name 登録対象のマスターデータのテーブル名
     * @return void
     */
    public function registMasterData($arrParams, &$masterData, $master_data_name)
    {
        $arrTmp = array();
        foreach ($arrParams['id'] as $key => $val) {
            // ID が空のデータは生成しない
            if ($val != '') {
                $arrTmp[$val] = $arrParams['name'][$key];
            }
        }

        // マスターデータを更新
        $masterData->objQuery = Application::alias('eccube.query');
        $masterData->objQuery->begin();
        $masterData->deleteMasterData($master_data_name, false);
        // TODO カラム名はメタデータから取得した方が良い
        $masterData->registMasterData($master_data_name, array('id', 'name', 'rank'), $arrTmp, false);
        $masterData->objQuery->commit();
    }
}
