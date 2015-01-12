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

namespace Eccube\Page\Bloc;

use Eccube\Application;
use Eccube\Framework\Query;
use Eccube\Framework\Display;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\Helper\CategoryHelper;
use Eccube\Framework\Helper\DbHelper;

/**
 * カテゴリ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Category extends AbstractBloc
{
    public $arrParentID;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
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
        // モバイル判定
        switch (Application::alias('eccube.display')->detectDevice()) {
            case DEVICE_TYPE_MOBILE:
                // メインカテゴリの取得
                $this->arrCat = $this->lfGetMainCat(true);
                break;
            default:
                // 選択中のカテゴリID
                $this->tpl_category_id = $this->lfGetSelectedCategoryId($_GET);
                // カテゴリツリーの取得
                $this->arrTree = $this->lfGetCatTree($this->tpl_category_id, true);
                break;
        }

    }

    /**
     * 選択中のカテゴリIDを取得する.
     *
     * @param  array $arrRequest リクエスト配列
     * @return array $arrCategoryId 選択中のカテゴリID
     */
    public function lfGetSelectedCategoryId($arrRequest)
    {
            // 商品ID取得
        $product_id = '';
        if (isset($arrRequest['product_id']) && $arrRequest['product_id'] != '' && is_numeric($arrRequest['product_id'])) {
            $product_id = $arrRequest['product_id'];
        }
        // カテゴリID取得
        $category_id = '';
        if (isset($arrRequest['category_id']) && $arrRequest['category_id'] != '' && is_numeric($arrRequest['category_id'])) {
            $category_id = $arrRequest['category_id'];
        }
        // 選択中のカテゴリIDを判定する
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrCategoryId = $objDb->getCategoryId($product_id, $category_id);
        if (empty($arrCategoryId)) {
            $arrCategoryId = array(0);
        }

        return $arrCategoryId;
    }

    /**
     * カテゴリツリーの取得.
     *
     * @param  array   $arrParentCategoryId 親カテゴリの配列
     * @param  boolean $count_check         登録商品数をチェックする場合はtrue
     * @return array   $arrRet カテゴリツリーの配列を返す
     */
    public function lfGetCatTree($arrParentCategoryId, $count_check = false)
    {
        /* @var $objCategory CategoryHelper */
        $objCategory = Application::alias('eccube.helper.category', $count_check);
        $arrTree = $objCategory->getTree();

        $this->arrParentID = array();
        foreach ($arrParentCategoryId as $category_id) {
            $arrParentID = $objCategory->getTreeTrail($category_id);
            $this->arrParentID = array_merge($this->arrParentID, $arrParentID);
            $this->root_parent_id[] = $arrParentID[0];
        }

        return $arrTree;
    }

    /**
     * メインカテゴリの取得.
     *
     * @param  boolean $count_check 登録商品数をチェックする場合はtrue
     * @return array   $arrMainCat メインカテゴリの配列を返す
     */
    public function lfGetMainCat($count_check = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = '*';
        $from = 'dtb_category left join dtb_category_total_count ON dtb_category.category_id = dtb_category_total_count.category_id';
        // メインカテゴリとその直下のカテゴリを取得する。
        $where = 'level <= 2 AND del_flg = 0';
        // 登録商品数のチェック
        if ($count_check) {
            $where .= ' AND product_count > 0';
        }
        $objQuery->setOption('ORDER BY rank DESC');
        $arrRet = $objQuery->select($col, $from, $where);
        // メインカテゴリを抽出する。
        $arrMainCat = array();
        foreach ($arrRet as $cat) {
            if ($cat['level'] != 1) {
                continue;
            }
            // 子カテゴリを持つかどうかを調べる。
            $arrChildrenID = Utils::sfGetUnderChildrenArray(
                $arrRet,
                'parent_category_id',
                'category_id',
                $cat['category_id']
            );
            $cat['has_children'] = count($arrChildrenID) > 0;
            $arrMainCat[] = $cat;
        }

        return $arrMainCat;
    }
}
