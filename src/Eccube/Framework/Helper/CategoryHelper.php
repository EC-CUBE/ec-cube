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

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Helper\DbHelper;

/**
 * カテゴリーを管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 */
class CategoryHelper
{
    protected $count_check;

    /**
     * コンストラクター
     *
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     */
    public function __construct($count_check = false)
    {
        $this->count_check = $count_check;
    }

    /**
     * カテゴリーの情報を取得.
     *
     * @param  integer $category_id カテゴリーID
     * @return array
     */
    public function get($category_id)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'dtb_category.*, dtb_category_total_count.product_count';
        $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
        $where = 'dtb_category.category_id = ? AND del_flg = 0';
        // 登録商品数のチェック
        if ($this->count_check) {
            $where .= ' AND product_count > 0';
        }
        $arrRet = $objQuery->getRow($col, $from, $where, array($category_id));

        return $arrRet;
    }

    /**
     * カテゴリー一覧の取得.
     *
     * @param bool $cid_to_key 配列のキーをカテゴリーIDにする場合はtrue
     * @param bool $reset スタティック変数をリセットする場合はtrue
     * @return array   カテゴリー一覧の配列
     */
    public function getList($cid_to_key = FALSE, $reset = FALSE)
    {
        static $arrCategory = array(), $cidIsKey = array();

        if ($reset) {
            $arrCategory = array();
            $cidIsKey = array();
        }

        if (!isset($arrCategory[$this->count_check])) {
            $objQuery = Application::alias('eccube.query');
            $col = 'dtb_category.*, dtb_category_total_count.product_count';
            $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
            // 登録商品数のチェック
            if ($this->count_check) {
                $where = 'del_flg = 0 AND product_count > 0';
            } else {
                $where = 'del_flg = 0';
            }
            $objQuery->setOption('ORDER BY rank DESC');
            $arrTmp = $objQuery->select($col, $from, $where);

            $arrCategory[$this->count_check] = $arrTmp;
        }

        if ($cid_to_key) {
            if (!isset($cidIsKey[$this->count_check])) {
                // 配列のキーをカテゴリーIDに
                $cidIsKey[$this->count_check] = Utils::makeArrayIDToKey('category_id', $arrCategory[$this->count_check]);
            }

            return $cidIsKey[$this->count_check];
        }

        return $arrCategory[$this->count_check];
    }

    /**
     * カテゴリーツリーの取得.
     *
     * @param bool $reset スタティック変数をリセットする場合はtrue
     * @return array
     */
    public function getTree($reset = false)
    {
        static $arrTree = array();

        if ($reset) {
            $arrTree = array();
        }

        if (!isset($arrTree[$this->count_check])) {
            $arrList = $this->getList(false, $reset);
            $arrTree[$this->count_check] = Utils::buildTree('category_id', 'parent_category_id', LEVEL_MAX, $arrList);
        }

        return $arrTree[$this->count_check];
    }

    /**
     * 親カテゴリーIDの配列を取得.
     *
     * @param  integer $category_id 起点のカテゴリーID
     * @param  boolean $id_only     IDだけの配列を返す場合はtrue
     * @return array
     */
    public function getTreeTrail($category_id, $id_only = TRUE)
    {
        $arrCategory = $this->getList(TRUE);
        $arrTrailID = Utils::getTreeTrail($category_id, 'category_id', 'parent_category_id', $arrCategory, TRUE, 0, $id_only);

        return $arrTrailID;
    }

    /**
     * 指定カテゴリーの子孫カテゴリーを取得
     *
     * @param int $category_id カテゴリーID
     * @return array
     */
    public function getTreeBranch($category_id)
    {
        $arrTree = $this->getTree();
        $arrTrail = $this->getTreeTrail($category_id, true);

        // 指定カテゴリーがルートの場合は、ツリーをそのまま返す.
        if ($category_id == 0) {
            return $arrTree;
        } else {
            // ルートから指定カテゴリーまでたどる.
            foreach ($arrTrail as $parent_id) {
                $nextTree = array();
                foreach ($arrTree as $branch) {
                    if ($branch['category_id'] == $parent_id && isset($branch['children'])) {
                        $nextTree = $branch['children'];
                    }
                }
                $arrTree = $nextTree;
            }
            return $arrTree;
        }
    }

    /**
     * カテゴリーの登録.
     *
     * @param array $data
     * @return int|bool
     */
    public function save($data)
    {
        $objQuery = Application::alias('eccube.query');

        $category_id = $data['category_id'];
        // ミリ秒付きの時間文字列を取得. CSVへの対応.
        // トランザクション内のCURRENT_TIMESTAMPは全てcommit()時の時間に統一されてしまう為.
        $query = array('update_date' => Utils::getFormattedDateWithMicroSecond());
        $objQuery->begin();

        if ($category_id == '') {
            // 新規登録
            $parent_category_id = $data['parent_category_id'];
            $rank = null;
            if ($parent_category_id == 0) {
                // ROOT階層で最大のランクを取得する。
                $where = 'parent_category_id = ?';
                $rank = $objQuery->max('rank', 'dtb_category', $where, array($parent_category_id)) + 1;
            } else {
                // 親のランクを自分のランクとする。
                $where = 'category_id = ?';
                $rank = $objQuery->get('rank', 'dtb_category', $where, array($parent_category_id));
                // 追加レコードのランク以上のレコードを一つあげる。
                $where = 'rank >= ?';
                $arrRawSql = array(
                    'rank' => '(rank + 1)',
                );
                $objQuery->update('dtb_category', array(), $where, array($rank), $arrRawSql);
            }

            $where = 'category_id = ?';
            // 自分のレベルを取得する(親のレベル + 1)
            $level = $objQuery->get('level', 'dtb_category', $where, array($parent_category_id)) + 1;

            $query['category_id'] = $objQuery->nextVal('dtb_category_category_id');
            $query['category_name'] = $data['category_name'];
            $query['parent_category_id'] = $data['parent_category_id'];
            $query['create_date'] = $query['update_date'];
            $query['creator_id']  = $_SESSION['member_id'];
            $query['rank']        = $rank;
            $query['level']       = $level;

            $result = $objQuery->insert('dtb_category', $query);
        } else {
            // 既存編集
            $query['category_id'] = $category_id;
            $query['parent_category_id'] = $data['parent_category_id'];
            $query['category_name'] = $data['category_name'];
            $where = 'category_id = ?';
            $result = $objQuery->update('dtb_category', $query, $where, array($category_id));
        }

        $objQuery->commit();
        return ($result) ? $query['category_id'] : FALSE;
    }

    /**
     * カテゴリーの削除
     *
     * @param int $category_id カテゴリーID
     * @return void
     */
    public function delete($category_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // ランク付きレコードの削除(※処理負荷を考慮してレコードごと削除する。)
        $objDb->deleteRankRecord('dtb_category', 'category_id', $category_id, '', true);
    }

    /**
     * カテゴリーの表示順をひとつ上げる.
     *
     * @param int $category_id カテゴリーID
     * @return void
     */
    public function rankUp($category_id)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();
        $up_id = $this->getNeighborRankId('upper', $category_id);
        if ($up_id != '') {
            // 上のグループのrankから減算する数
            $my_count = $this->countAllBranches($category_id);
            // 自分のグループのrankに加算する数
            $up_count = $this->countAllBranches($up_id);
            if ($my_count > 0 && $up_count > 0) {
                // 自分のグループに加算
                $this->raiseBranchRank($objQuery, $category_id, $up_count);
                // 上のグループから減算
                $this->reduceBranchRank($objQuery, $up_id, $my_count);
            }
        }
        $objQuery->commit();
    }

    /**
     * カテゴリーの表示順をひとつ下げる.
     *
     * @param int $category_id カテゴリーID
     * @return void
     */
    public function rankDown($category_id)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->begin();
        $down_id = $this->getNeighborRankId('lower', $category_id);
        if ($down_id != '') {
            // 下のグループのrankに加算する数
            $my_count = $this->countAllBranches($category_id);
            // 自分のグループのrankから減算する数
            $down_count = $this->countAllBranches($down_id);
            if ($my_count > 0 && $down_count > 0) {
                // 自分のグループから減算
                $this->raiseBranchRank($objQuery, $down_id, $my_count);
                // 下のグループに加算
                $this->reduceBranchRank($objQuery, $category_id, $down_count);
            }
        }
        $objQuery->commit();
    }

    /**
     * 並びがとなりのIDを取得する。
     *
     * @param string $side 上 upper か下 down か
     * @param int $category_id カテゴリーID
     * @return int
     */
    private function getNeighborRankId($side, $category_id)
    {
        $arrCategory = $this->get($category_id);
        $parent_id = $arrCategory['parent_category_id'];

        if ($parent_id == 0) {
            $arrBrother = $this->getTree();
        } else {
            $arrBrother = $this->getTreeBranch($parent_id);
        }

        // 全ての子を取得する。
        $max = count($arrBrother);
        $upper_id = '';
        for ($cnt = 0; $cnt < $max; $cnt++) {
            if ($arrBrother[$cnt]['category_id'] == $category_id) {
                if ($side == 'upper') {
                    $index = $cnt - 1;
                } else {
                    $index = $cnt + 1;
                }
                $upper_id = $arrBrother[$index]['category_id'];
                break;
            }
        }

        return $upper_id;
    }

    /**
     * 指定カテゴリーを含めた子孫カテゴリーの数を取得する.
     *
     * @param int $category_id カテゴリーID
     * @return int
     */
    private function countAllBranches($category_id)
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // 子ID一覧を取得
        $arrRet = $objDb->getChildrenArray('dtb_category', 'parent_category_id', 'category_id', $category_id);

        return count($arrRet);
    }

    /**
     * 子孫カテゴリーの表示順を一括して上げる.
     *
     * @param Query $objQuery
     * @param int $category_id
     * @param int $count
     * @return array|bool
     */
    private function raiseBranchRank(Query $objQuery, $category_id, $count)
    {
        $table = 'dtb_category';
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // 子ID一覧を取得
        $arrRet = $objDb->getChildrenArray($table, 'parent_category_id', 'category_id', $category_id);
        $line = Utils::sfGetCommaList($arrRet);
        $where = "category_id IN ($line) AND del_flg = 0";
        $arrRawVal = array(
            'rank' => "(rank + $count)",
        );

        return $objQuery->update($table, array(), $where, array(), $arrRawVal);
    }

    /**
     * 子孫カテゴリーの表示順を一括して下げる.
     *
     * @param Query $objQuery
     * @param int $category_id
     * @param int $count
     * @return array|bool
     */
    private function reduceBranchRank(Query $objQuery, $category_id, $count)
    {
        $table = 'dtb_category';
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // 子ID一覧を取得
        $arrRet = $objDb->getChildrenArray($table, 'parent_category_id', 'category_id', $category_id);
        $line = Utils::sfGetCommaList($arrRet);
        $where = "category_id IN ($line) AND del_flg = 0";
        $arrRawVal = array(
            'rank' => "(rank - $count)",
        );

        return $objQuery->update($table, array(), $where, array(), $arrRawVal);
    }

    /**
     * 有効なカテゴリーIDかチェックする.
     *
     * @param int $category_id
     * @param bool $include_deleted
     * @return bool
     */
    public function isValidCategoryId($category_id, $include_deleted = false) {
        if ($include_deleted) {
            $where = '';
        } else {
            $where = 'del_flg = 0';
        }
        if (
            Utils::sfIsInt($category_id)
            && !Utils::sfIsZeroFilling($category_id)
            && Application::alias('eccube.helper.db')->isRecord('dtb_category', 'category_id', array($category_id), $where)
        ) {
            return true;
        }
        return false;
    }
}
