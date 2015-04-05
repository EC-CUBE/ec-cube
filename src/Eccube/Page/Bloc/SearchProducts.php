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
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * 検索ブロック のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class SearchProducts extends AbstractBloc
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
        // 商品ID取得
        $product_id = $this -> lfGetProductId();
        // カテゴリID取得
        $category_id = $this -> lfGetCategoryId();
        // メーカーID取得
        $maker_id = $this -> lfGetMakerId();
        // 選択中のカテゴリIDを判定する
        $this->category_id = $this->lfGetSelectedCategoryId($product_id, $category_id);
        // カテゴリ検索用選択リスト
        $this->arrCatList = $this->lfGetCategoryList();
        // 選択中のメーカーIDを判定する
        $this->maker_id = $this->lfGetSelectedMakerId($product_id, $maker_id);
        // メーカー検索用選択リスト
        $this->arrMakerList = $this->lfGetMakerList();
    }

    /**
     * 商品IDを取得する.
     *
     * @return string $product_id 商品ID
     */
    public function lfGetProductId()
    {
        $product_id = '';
        if (isset($_GET['product_id']) && $_GET['product_id'] != '' && is_numeric($_GET['product_id'])) {
            $product_id = $_GET['product_id'];
        }

        return $product_id;
    }

    /**
     * カテゴリIDを取得する.
     *
     * @return string $category_id カテゴリID
     */
    public function lfGetCategoryId()
    {
        $category_id = '';
        if (isset($_GET['category_id']) && $_GET['category_id'] != '' && is_numeric($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
        }

        return $category_id;
    }

    /**
     * メーカーIDを取得する.
     *
     * @return string $maker_id メーカーID
     */
    public function lfGetMakerId()
    {
        $maker_id = '';
        if (isset($_GET['maker_id']) && $_GET['maker_id'] != '' && is_numeric($_GET['maker_id'])) {
            $maker_id = $_GET['maker_id'];
        }

        return $maker_id;
    }

    /**
     * 選択中のカテゴリIDを取得する
     *
     * @param string $product_id
     * @param string $category_id
     * @return array $arrCategoryId 選択中のカテゴリID
     */
    public function lfGetSelectedCategoryId($product_id, $category_id)
    {
        // 選択中のカテゴリIDを判定する
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrCategoryId = $objDb->getCategoryId($product_id, $category_id);

        return $arrCategoryId;
    }

    /**
     * 選択中のメーカーIDを取得する
     *
     * @param string $product_id
     * @param string $maker_id
     * @return array $arrMakerId 選択中のメーカーID
     */
    public function lfGetSelectedMakerId($product_id, $maker_id)
    {
        // 選択中のメーカーIDを判定する
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrMakerId = $objDb->getMakerId($product_id, $maker_id);

        return $arrMakerId;
    }

    /**
     * カテゴリ検索用選択リストを取得する
     *
     * @return array $arrCategoryList カテゴリ検索用選択リスト
     */
    public function lfGetCategoryList()
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // カテゴリ検索用選択リスト
        $arrCategoryList = $objDb->getCategoryList('', true, '　');
        if (is_array($arrCategoryList)) {
            // 文字サイズを制限する
            foreach ($arrCategoryList as $key => $val) {
                $truncate_str = Utils::sfCutString($val, SEARCH_CATEGORY_LEN, false);
                $arrCategoryList[$key] = preg_replace('/　/u', '&nbsp;&nbsp;', $truncate_str);
            }
        }

        return $arrCategoryList;
    }

    /**
     * メーカー検索用選択リストを取得する
     *
     * @return array $arrMakerList メーカー検索用選択リスト
     */
    public function lfGetMakerList()
    {
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        // メーカー検索用選択リスト
        $arrMakerList = $objDb->getMakerList('', true);
        if (is_array($arrMakerList)) {
            // 文字サイズを制限する
            foreach ($arrMakerList as $key => $val) {
                $arrMakerList[$key] = Utils::sfCutString($val, SEARCH_CATEGORY_LEN, false);
            }
        }

        return $arrMakerList;
    }
}
