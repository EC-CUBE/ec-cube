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

namespace Eccube\Page\Admin\Products;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * 規格管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ClassList extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'products/class.tpl';
        $this->tpl_subno = 'class';
        $this->tpl_subtitle = '規格管理';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_mainno = 'products';
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

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $class_id = $objFormParam->getValue('class_id');

        // 要求判定
        switch ($this->getMode()) {
            // 編集処理
        case 'edit':
            //パラメーターの取得
            $this->arrForm  = $objFormParam->getHashArray();
            // 入力パラメーターチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            if (Utils::isBlank($this->arrErr)) {
                //新規規格追加かどうかを判定する
                $is_insert = $this->lfCheckInsert($this->arrForm);
                if ($is_insert) {
                    $this->lfInsertClass($this->arrForm); // 新規作成
                } else {
                    $this->lfUpdateClass($this->arrForm); // 既存編集
                }

                // 再表示
                Application::alias('eccube.response')->reload();
            }
            break;
            // 削除
        case 'delete':
            //規格データの削除処理
            $this->lfDeleteClass($class_id);

            // 再表示
            Application::alias('eccube.response')->reload();
            break;
            // 編集前処理
        case 'pre_edit':
            // 規格名を取得する。
            $class_name = $this->lfGetClassName($class_id);
            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['name'] = $class_name;
            break;
        case 'down':
            $this->lfDownRank($class_id);

            // 再表示
            Application::alias('eccube.response')->reload();
            break;
        case 'up':
            $this->lfUpRank($class_id);

            // 再表示
            Application::alias('eccube.response')->reload();
            break;
        default:
            break;
        }
        // 規格の読込
        $this->arrClass = $this->lfGetClass();
        $this->arrClassCatCount = Utils::sfGetClassCatCount();
        // POSTデータを引き継ぐ
        $this->tpl_class_id = $class_id;
    }

    /**
     * パラメーターの初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('規格名', 'name', STEXT_LEN, 'KVa', array('EXIST_CHECK' ,'SPTAB_CHECK' ,'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('規格ID', 'class_id', INT_LEN, 'n', array('NUM_CHECK'));
    }

    /**
     * 有効な規格情報の取得
     *
     * @return array 規格情報
     */
    public function lfGetClass()
    {
        $objQuery = Application::alias('eccube.query');

        $where = 'del_flg <> 1';
        $objQuery->setOrder('rank DESC');
        $arrClass = $objQuery->select('name, class_id', 'dtb_class', $where);

        return $arrClass;
    }

    /**
     * 規格名を取得する
     *
     * @param  integer $class_id 規格ID
     * @return string  規格名
     */
    public function lfGetClassName($class_id)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'class_id = ?';
        $class_name = $objQuery->get('name', 'dtb_class', $where, array($class_id));

        return $class_name;
    }

    /**
     * 規格情報を新規登録
     *
     * @param  array   $arrForm フォームパラメータークラス
     * @return integer 更新件数
     */
    public function lfInsertClass($arrForm)
    {
        $objQuery = Application::alias('eccube.query');
        // INSERTする値を作成する。
        $sqlval['name'] = $arrForm['name'];
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['rank'] = $objQuery->max('rank', 'dtb_class') + 1;
        $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        // INSERTの実行
        $sqlval['class_id'] = $objQuery->nextVal('dtb_class_class_id');
        $ret = $objQuery->insert('dtb_class', $sqlval);

        return $ret;
    }

    /**
     * 規格情報を更新
     *
     * @param  array   $arrForm フォームパラメータークラス
     * @return integer 更新件数
     */
    public function lfUpdateClass($arrForm)
    {
        $objQuery = Application::alias('eccube.query');
        // UPDATEする値を作成する。
        $sqlval['name'] = $arrForm['name'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'class_id = ?';
        // UPDATEの実行
        $ret = $objQuery->update('dtb_class', $sqlval, $where, array($arrForm['class_id']));

        return $ret;
    }

    /**
     * 規格情報を削除する.
     *
     * @param  integer      $class_id 規格ID
     * @return integer      削除件数
     */
    public function lfDeleteClass($class_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objQuery = Application::alias('eccube.query');

        $ret = $objDb->deleteRankRecord('dtb_class', 'class_id', $class_id, '', true);
        $where= 'class_id = ?';
        $objQuery->delete('dtb_classcategory', $where, array($class_id));

        return $ret;
    }

    /**
     * エラーチェック
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    public function lfCheckError(&$objFormParam)
    {
        $objQuery = Application::alias('eccube.query');
        $arrForm = $objFormParam->getHashArray();
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        if (!Utils::isBlank($arrErr)) {
            return $arrErr;
        } else {
            $arrForm = $objFormParam->getHashArray();
        }

        $where = 'del_flg = 0 AND name = ?';
        $arrClass = $objQuery->select('class_id, name', 'dtb_class', $where, array($arrForm['name']));
        // 編集中のレコード以外に同じ名称が存在する場合
        if ($arrClass[0]['class_id'] != $arrForm['class_id'] && $arrClass[0]['name'] == $arrForm['name']) {
            $arrErr['name'] = '※ 既に同じ内容の登録が存在します。<br>';
        }

        return $arrErr;
    }

    /**
     * 新規規格追加かどうかを判定する.
     *
     * @param  string  $arrForm フォームの入力値
     * @return boolean 新規商品追加の場合 true
     */
    public function lfCheckInsert($arrForm)
    {
        //class_id のあるなしで新規商品かどうかを判定
        if (empty($arrForm['class_id'])) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 並び順を上げる
     *
     * @param  integer $class_id 規格ID
     * @return void
     */
    public function lfUpRank($class_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankUp('dtb_class', 'class_id', $class_id);
    }
    /**
     * 並び順を下げる
     *
     * @param  integer $class_id 規格ID
     * @return void
     */
    public function lfDownRank($class_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $objDb->rankDown('dtb_class', 'class_id', $class_id);
    }
}
