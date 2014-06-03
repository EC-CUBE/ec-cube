<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * カテゴリ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Category extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'カテゴリ登録';
        $this->tpl_mainpage = 'products/category.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno  = 'category';
        $this->tpl_onload = " eccube.setFocus('category_name'); ";
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
        $objFormParam = new SC_FormParam_Ex();
        $objCategory = new SC_Helper_Category_Ex();

        // 入力パラメーター初期化
        $this->initParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch ($this->getMode()) {
            // カテゴリ登録/編集実行
            case 'edit':
                $this->doEdit($objFormParam);
                break;
            // 入力ボックスへ編集対象のカテゴリ名をセット
            case 'pre_edit':
                $this->doPreEdit($objFormParam);
                break;
            // カテゴリ削除
            case 'delete':
                $this->doDelete($objFormParam);
                break;
            // 表示順を上へ
            case 'up':
                $this->doUp($objFormParam);
                break;
            // 表示順を下へ
            case 'down':
                $this->doDown($objFormParam);
                break;
            // FIXME r19909 によってテンプレートが削除されている
            case 'moveByDnD':
                // DnDしたカテゴリと移動先のセットを分解する
                $keys = explode('-', $_POST['keySet']);
                if ($keys[0] && $keys[1]) {
                    $objQuery =& SC_Query_Ex::getSingletonInstance();
                    $objQuery->begin();

                    // 移動したデータのrank、level、parent_category_idを取得
                    $rank   = $objQuery->get('rank', 'dtb_category', 'category_id = ?', array($keys[0]));
                    $level  = $objQuery->get('level', 'dtb_category', 'category_id = ?', array($keys[0]));
                    $parent = $objQuery->get('parent_category_id', 'dtb_category', 'category_id = ?', array($keys[0]));

                    // 同一level内のrank配列を作成
                    $objQuery->setOption('ORDER BY rank DESC');
                    if ($level == 1) {
                        // 第1階層の時
                        $arrRet = $objQuery->select('rank', 'dtb_category', 'level = ?', array($level));
                    } else {
                        // 第2階層以下の時
                        $arrRet = $objQuery->select('rank', 'dtb_category', 'level = ? AND parent_category_id = ?', array($level, $parent));
                    }
                    for ($i = 0; $i < sizeof($arrRet); $i++) {
                        $rankAry[$i + 1] = $arrRet[$i]['rank'];
                    }

                    // 移動したデータのグループ内データ数
                    $my_count = $this->lfCountChilds($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $keys[0]);
                    if ($rankAry[$keys[1]] > $rank) {
                        // データが今の位置より上がった時
                        $up_count = $rankAry[$keys[1]] - $rank;
                        $decAry   = $objQuery->select('category_id', 'dtb_category', 'level = ? AND rank > ? AND rank <= ?', array($level, $rank, $rankAry[$keys[1]]));
                        foreach ($decAry as $value) {
                            // 上のグループから減算
                            $this->lfDownRankChilds($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $value['category_id'], $my_count);
                        }
                        // 自分のグループに加算
                        $this->lfUpRankChilds($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $keys[0], $up_count);
                    } elseif ($rankAry[$keys[1]] < $rank) {
                        // データが今の位置より下がった時
                        $down_count = 0;
                        $incAry     = $objQuery->select('category_id', 'dtb_category', 'level = ? AND rank < ? AND rank >= ?', array($level, $rank, $rankAry[$keys[1]]));
                        foreach ($incAry as $value) {
                            // 下のグループに加算
                            $this->lfUpRankChilds($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $value['category_id'], $my_count);
                            // 合計減算値
                            $down_count += $this->lfCountChilds($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $value['category_id']);
                        }
                        // 自分のグループから減算
                        $this->lfDownRankChilds($objQuery, 'dtb_category', 'parent_category_id', 'category_id', $keys[0], $down_count);
                    }
                    $objQuery->commit();
                }
                break;
            // カテゴリツリークリック時
            case 'tree':
                break;
            // CSVダウンロード
            case 'csv':
                // CSVを送信する
                $objCSV = new SC_Helper_CSV_Ex();

                $objCSV->sfDownloadCsv('5', '', array(), '', true);
                SC_Response_Ex::actionExit();
                break;
            default:
                break;
        }

        $parent_category_id = $objFormParam->getValue('parent_category_id');
        // 空の場合は親カテゴリを0にする
        if (empty($parent_category_id)) {
            $parent_category_id = 0;
        }
        // 親カテゴリIDの保持
        $this->arrForm['parent_category_id'] = $parent_category_id;
        // カテゴリツリーを取得
        $this->arrTree = $objCategory->getTree(true);
        $this->arrParentID = $objCategory->getTreeTrail($parent_category_id);
        // カテゴリ一覧を取得
        $this->arrList = $objCategory->getTreeBranch($parent_category_id);
        // ぱんくずの生成
        $arrBread = $objCategory->getTreeTrail($this->arrForm['parent_category_id'], FALSE);
        $this->tpl_bread_crumbs = SC_Utils_Ex::jsonEncode(array_reverse($arrBread));
    }

    /**
     * カテゴリの削除を実行する.
     *
     * 下記の場合は削除を実施せず、エラーメッセージを表示する.
     *
     * - 削除対象のカテゴリに、子カテゴリが1つ以上ある場合
     * - 削除対象のカテゴリを、登録商品が使用している場合
     *
     * カテゴリの削除は、物理削除で行う.
     *
     * @param  SC_FormParam $objFormParam
     * @return void
     */
    public function doDelete(&$objFormParam)
    {
        $objCategory = new SC_Helper_Category_Ex(false);
        $category_id = $objFormParam->getValue('category_id');

        // 子カテゴリのチェック
        $arrBranch = $objCategory->getTreeBranch($category_id);
        if (count($arrBranch) > 0) {
            $this->arrErr['category_name'] = '※ 子カテゴリが存在するため削除できません。<br/>';
            return;
        }
        // 登録商品のチェック
        $arrCategory = $objCategory->get($category_id);
        if ($arrCategory['product_count'] > 0) {
            $this->arrErr['category_name'] = '※ カテゴリ内に商品が存在するため削除できません。<br/>';
            return;
        }

        // ランク付きレコードの削除(※処理負荷を考慮してレコードごと削除する。)
        $objCategory->delete($category_id);
    }

    /**
     * 編集対象のカテゴリ名を, 入力ボックスへ表示する.
     *
     * @param  SC_FormParam $objFormParam
     * @return void
     */
    public function doPreEdit(&$objFormParam)
    {
        $category_id = $objFormParam->getValue('category_id');

        $objCategory = new SC_Helper_Category_Ex();
        $arrRes = $objCategory->get($category_id);

        $objFormParam->setParam($arrRes);

        $this->arrForm = $objFormParam->getHashArray();
    }

    /**
     * カテゴリの登録・編集を実行する.
     *
     * 下記の場合は, 登録・編集を実行せず、エラーメッセージを表示する
     *
     * - カテゴリ名がすでに使用されている場合
     * - 階層登録数の上限を超える場合 (登録時のみ評価)
     * - カテゴリ名がすでに使用されている場合 (登録時のみ評価)
     *
     * @param  SC_FormParam $objFormParam
     * @return void
     */
    public function doEdit(&$objFormParam)
    {
        // エラーチェック
        $this->arrErr = $this->checkError($objFormParam);

        // エラーがない場合、追加・更新処理
        if (empty($this->arrErr)) {
            $arrCategory = $objFormParam->getDbArray();
            $objCategory = new SC_Helper_Category_Ex();
            $objCategory->save($arrCategory);
        // エラーがある場合、入力値の再表示
        } else {
            $this->arrForm = $objFormParam->getHashArray();
        }
    }

    /**
     * エラーチェック
     *
     * @param  SC_FormParam $objFormParam
     * @return array
     */
    public function checkError(&$objFormParam)
    {
        $objCategory = new SC_Helper_Category_Ex();

        // 入力項目チェック
        $arrErr = $objFormParam->checkError();
        if (!empty($arrErr)) {
            return $arrErr;
        }

        $category_id = $objFormParam->getValue('category_id');
        $parent_category_id = $objFormParam->getValue('parent_category_id');
        $category_name = $objFormParam->getValue('category_name');

        // 追加の場合に固有のチェック
        if (!$category_id) {
            // 登録数上限チェック
            $count = count($objCategory->getList());
            if ($count >= CATEGORY_MAX) {
                $arrErr['category_name'] = '※ カテゴリの登録最大数を超えました。<br/>';

                return $arrErr;
            }

            // 階層上限チェック
            $arrParent = $objCategory->get($parent_category_id);
            if ($arrParent['level'] >= LEVEL_MAX) {
                $arrErr['category_name'] = '※ ' . LEVEL_MAX . '階層以上の登録はできません。<br/>';

                return $arrErr;
            }
        }

        // 重複チェック
        $exists = false;
        $arrBrother = $objCategory->getTreeBranch($parent_category_id);
        foreach ($arrBrother as $brother) {
            if ($brother['category_name'] == $category_name && $brother['category_id'] != $category_id) {
                $exists = true;
            }
        }
        if ($exists) {
            $arrErr['category_name'] = '※ 既に同じ内容の登録が存在します。<br/>';

            return $arrErr;
        }

        return $arrErr;
    }

    /**
     * カテゴリの表示順序を上へ移動する.
     *
     * @param  SC_FormParam $objFormParam
     * @return void
     */
    public function doUp(&$objFormParam)
    {
        $objCategory = new SC_Helper_Category_Ex(false);
        $category_id = $objFormParam->getValue('category_id');
        $objCategory->rankUp($category_id);
    }

    /**
     * カテゴリの表示順序を下へ移動する.
     *
     * @param  SC_FormParam $objFormParam
     * @return void
     */
    public function doDown(&$objFormParam)
    {
        $objCategory = new SC_Helper_Category_Ex(false);
        $category_id = $objFormParam->getValue('category_id');
        $objCategory->rankDown($category_id);
    }

    /**
     * パラメーターの初期化を行う
     *
     * @param  SC_FormParam $objFormParam
     * @return void
     */
    public function initParam(&$objFormParam)
    {
        $objFormParam->addParam('親カテゴリID', 'parent_category_id', null, null, array());
        $objFormParam->addParam('カテゴリID', 'category_id', null, null, array());
        $objFormParam->addParam('カテゴリ名', 'category_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function lfCountChilds($objQuery, $table, $pid_name, $id_name, $id)
    {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);

        return count($arrRet);
    }

    public function lfUpRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count)
    {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        $line = SC_Utils_Ex::sfGetCommaList($arrRet);
        $where = "$id_name IN ($line) AND del_flg = 0";
        $arrRawVal = array(
            'rank' => "(rank + $count)",
        );

        return $objQuery->update($table, array(), $where, array(), $arrRawVal);
    }

    public function lfDownRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count)
    {
        $objDb = new SC_Helper_DB_Ex();
        // 子ID一覧を取得
        $arrRet = $objDb->sfGetChildrenArray($table, $pid_name, $id_name, $id);
        $line = SC_Utils_Ex::sfGetCommaList($arrRet);
        $where = "$id_name IN ($line) AND del_flg = 0";
        $arrRawVal = array(
            'rank' => "(rank - $count)",
        );

        return $objQuery->update($table, array(), $where, array(), $arrRawVal);
    }
}
