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

namespace Eccube\Page\Products;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * カテゴリ一覧 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class CategoryList extends AbstractPage
{
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
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction
     * @return void
     */
    public function action()
    {
        $objFormParam = $this->lfInitParam($_REQUEST);

        // カテゴリIDの正当性チェック
        $category_id = $this->lfCheckCategoryId($objFormParam->getValue('category_id'));
        if ($category_id == 0) {
            Utils::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        // カテゴリ情報を取得する。
        $arrCategoryData = $this->lfGetCategories($category_id, true);
        $this->arrCategory = $arrCategoryData['arrCategory'];
        $this->arrChildren = $arrCategoryData['arrChildren'];
        $this->tpl_subtitle = $this->arrCategory['category_name'];
    }

    /* カテゴリIDの正当性チェック */

    /**
     * @return string
     */
    public function lfCheckCategoryId($category_id)
    {
        if ($category_id && !Application::alias('eccube.helper.db')->isRecord('dtb_category', 'category_id', (array) $category_id, 'del_flg = 0')) {
            return 0;
        }

        return $category_id;
    }

    /**
     * 選択されたカテゴリとその子カテゴリの情報を取得し、
     * ページオブジェクトに格納する。
     *
     * @param  string  $category_id カテゴリID
     * @param  boolean $count_check 有効な商品がないカテゴリを除くかどうか
     * @return void
     */
    public function lfGetCategories($category_id, $count_check = false)
    {
        $arrCategory = null;    // 選択されたカテゴリ
        $arrChildren = array(); // 子カテゴリ

        $arrAll = Application::alias('eccube.helper.db')->getCatTree($category_id, $count_check);
        foreach ($arrAll as $category) {
            // 選択されたカテゴリの場合
            if ($category['category_id'] == $category_id) {
                $arrCategory = $category;
                continue;
            }

            // 関係のないカテゴリはスキップする。
            if ($category['parent_category_id'] != $category_id) {
                continue;
            }

            // 子カテゴリの場合は、孫カテゴリが存在するかどうかを調べる。
            $arrGrandchildrenID = Utils::sfGetUnderChildrenArray($arrAll, 'parent_category_id', 'category_id', $category['category_id']);
            $category['has_children'] = count($arrGrandchildrenID) > 0;
            $arrChildren[] = $category;
        }

        if (!isset($arrCategory)) {
            Utils::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        // 子カテゴリの商品数を合計する。
        $children_product_count = 0;
        foreach ($arrChildren as $category) {
            $children_product_count += $category['product_count'];
        }

        // 選択されたカテゴリに直属の商品がある場合は、子カテゴリの先頭に追加する。
        if ($arrCategory['product_count'] > $children_product_count) {
            $arrCategory['product_count'] -= $children_product_count; // 子カテゴリの商品数を除く。
            $arrCategory['has_children'] = false; // 商品一覧ページに遷移させるため。
            array_unshift($arrChildren, $arrCategory);
        }

        return array('arrChildren'=>$arrChildren, 'arrCategory'=>$arrCategory);
    }

    /**
     * ユーザ入力値の処理
     *
     * @return FormParam
     */
    public function lfInitParam($arrRequest)
    {
        $objFormParam = Application::alias('eccube.form_param');
        $objFormParam->addParam('カテゴリID', 'category_id', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        // 値の取得
        $objFormParam->setParam($arrRequest);
        // 入力値の変換
        $objFormParam->convParam();

        return $objFormParam;
    }
}
