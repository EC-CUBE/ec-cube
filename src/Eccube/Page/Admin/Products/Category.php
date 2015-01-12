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
use Eccube\Framework\Helper\CategoryHelper;
use Eccube\Framework\Helper\CsvHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * カテゴリ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Category extends AbstractAdminPage
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
        $objFormParam = Application::alias('eccube.form_param');
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category');

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
                    $objQuery = Application::alias('eccube.query');
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
                /* @var $objCSV CsvHelper */
                $objCSV = Application::alias('eccube.helper.csv');

                $objCSV->sfDownloadCsv('5', '', array(), '', true);
                Application::alias('eccube.response')->actionExit();
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
        $this->tpl_bread_crumbs = Utils::jsonEncode(array_reverse($arrBread));
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
     * @param  FormParam $objFormParam
     * @return void
     */
    public function doDelete(&$objFormParam)
    {
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category', false);
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
     * @param  FormParam $objFormParam
     * @return void
     */
    public function doPreEdit(&$objFormParam)
    {
        $category_id = $objFormParam->getValue('category_id');

        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category');
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
     * @param  FormParam $objFormParam
     * @return void
     */
    public function doEdit(&$objFormParam)
    {
        // エラーチェック
        $this->arrErr = $this->checkError($objFormParam);

        // エラーがない場合、追加・更新処理
        if (empty($this->arrErr)) {
            $arrCategory = $objFormParam->getDbArray();
            /* @var $objCategory CategoryHelper */
            $objCategory = Application::alias('eccube.helper.category');
            $objCategory->save($arrCategory);
        // エラーがある場合、入力値の再表示
        } else {
            $this->arrForm = $objFormParam->getHashArray();
        }
    }

    /**
     * エラーチェック
     *
     * @param  FormParam $objFormParam
     * @return array
     */
    public function checkError(&$objFormParam)
    {
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category');

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
     * @param  FormParam $objFormParam
     * @return void
     */
    public function doUp(&$objFormParam)
    {
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category', false);
        $category_id = $objFormParam->getValue('category_id');
        $objCategory->rankUp($category_id);
    }

    /**
     * カテゴリの表示順序を下へ移動する.
     *
     * @param  FormParam $objFormParam
     * @return void
     */
    public function doDown(&$objFormParam)
    {
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category', false);
        $category_id = $objFormParam->getValue('category_id');
        $objCategory->rankDown($category_id);
    }

    /**
     * パラメーターの初期化を行う
     *
     * @param  FormParam $objFormParam
     * @return void
     */
    public function initParam(&$objFormParam)
    {
        $objFormParam->addParam('親カテゴリID', 'parent_category_id', null, null, array());
        $objFormParam->addParam('カテゴリID', 'category_id', null, null, array());
        $objFormParam->addParam('カテゴリ名', 'category_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * @param Query $objQuery
     * @param string $table
     * @param string $pid_name
     * @param string $id_name
     */
    public function lfCountChilds($objQuery, $table, $pid_name, $id_name, $id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // 子ID一覧を取得
        $arrRet = $objDb->getChildrenArray($table, $pid_name, $id_name, $id);

        return count($arrRet);
    }

    /**
     * @param Query $objQuery
     * @param string $table
     * @param string $pid_name
     * @param string $id_name
     */
    public function lfUpRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // 子ID一覧を取得
        $arrRet = $objDb->getChildrenArray($table, $pid_name, $id_name, $id);
        $line = Utils::sfGetCommaList($arrRet);
        $where = "$id_name IN ($line) AND del_flg = 0";
        $arrRawVal = array(
            'rank' => "(rank + $count)",
        );

        return $objQuery->update($table, array(), $where, array(), $arrRawVal);
    }

    /**
     * @param Query $objQuery
     * @param string $table
     * @param string $pid_name
     * @param string $id_name
     * @param integer $count
     */
    public function lfDownRankChilds($objQuery, $table, $pid_name, $id_name, $id, $count)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // 子ID一覧を取得
        $arrRet = $objDb->getChildrenArray($table, $pid_name, $id_name, $id);
        $line = Utils::sfGetCommaList($arrRet);
        $where = "$id_name IN ($line) AND del_flg = 0";
        $arrRawVal = array(
            'rank' => "(rank - $count)",
        );

        return $objQuery->update($table, array(), $where, array(), $arrRawVal);
    }
}
