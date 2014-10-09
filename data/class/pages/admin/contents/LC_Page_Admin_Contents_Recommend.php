<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
 * おすすめ商品管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_Recommend extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'contents/recommend.tpl';
        $this->tpl_mainno = 'contents';
        $this->tpl_subno = 'recommend';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'おすすめ商品管理';
        //最大登録数の表示
        $this->tpl_disp_max = RECOMMEND_NUM;
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
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $arrPost = $objFormParam->getHashArray();

        $objRecommend = new SC_Helper_BestProducts_Ex();

        switch ($this->getMode()) {
            case 'down': //商品の並び替えをする。
                $objRecommend->rankDown($arrPost['best_id']);
                $arrItems = $this->getRecommendProducts($objRecommend);
                break;

            case 'up': //商品の並び替えをする。
                $objRecommend->rankUp($arrPost['best_id']);
                $arrItems = $this->getRecommendProducts($objRecommend);
                break;

            case 'regist': // 商品を登録する。
                $this->arrErr[$arrPost['rank']] = $this->lfCheckError($objFormParam);
                // 登録処理にエラーがあった場合は商品選択の時と同じ処理を行う。
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $member_id = $_SESSION['member_id'];
                    $this->insertRecommendProduct($arrPost, $member_id, $objRecommend);
                    $arrItems = $this->getRecommendProducts($objRecommend);
                    $this->tpl_onload = "window.alert('編集が完了しました');";
                } else {
                    $arrItems = $this->getRecommendProducts($objRecommend);
                    $rank = $arrPost['rank'];
                    $arrItems[$rank]['comment'] = $arrPost['comment'];;
                    if ($arrPost['best_id']) {
                    } else {
                        $arrItems = $this->setProducts($arrPost, $arrItems);
                        $this->checkRank = $arrPost['rank'];
                    }
                }
                break;
            case 'delete': // 商品を削除する。
                if ($arrPost['best_id']) {
                    $this->deleteProduct($arrPost, $objRecommend);
                }
                $arrItems = $this->getRecommendProducts($objRecommend);
                $this->tpl_onload = "window.alert('削除しました');";
                break;
            case 'set_item': // 商品を選択する。
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr['rank']) && SC_Utils_Ex::isBlank($this->arrErr['product_id'])) {
                    $arrItems = $this->setProducts($arrPost, $this->getRecommendProducts($objRecommend));
                    $this->checkRank = $arrPost['rank'];
                }
                break;
            default:
                $arrItems = $this->getRecommendProducts($objRecommend);
                break;
        }

        $this->category_id = intval($arrPost['category_id']);
        $this->arrItems = $arrItems;

        // カテゴリ取得
        $objDb = new SC_Helper_DB_Ex();
        $this->arrCatList = $objDb->sfGetCategoryList('level = 1');
    }

    /**
     * パラメーターの初期化を行う
     * @param SC_FormParam_Ex $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('おすすめ商品ID', 'best_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('カテゴリID', 'category_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ランク', 'rank', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('コメント', 'comment', LTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 入力されたパラメーターのエラーチェックを行う。
     * @param  SC_FormParam_Ex $objFormParam
     * @return Array  エラー内容
     */
    public function lfCheckError(&$objFormParam)
    {
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        return $objErr->arrErr;
    }

    /**
     * 既に登録されている内容を取得する
     * @param  SC_Helper_BestProducts_Ex $objRecommend
     * @return Array  $arrReturnProducts データベースに登録されているおすすめ商品の配列
     */
    public function getRecommendProducts(SC_Helper_BestProducts_Ex &$objRecommend)
    {
        $arrList = $objRecommend->getList();
        // product_id の一覧を作成
        $product_ids = array();
        foreach ($arrList as $value) {
            $product_ids[] = $value['product_id'];
        }

        $objProduct = new SC_Product_Ex;
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrProducts = $objProduct->getListByProductIds($objQuery, $product_ids);

        $arrReturnProducts = array();
        foreach ($arrList as $data) {
            $data['main_list_image'] = $arrProducts[$data['product_id']]['main_list_image'];
            $data['name'] = $arrProducts[$data['product_id']]['name'];
            $arrReturnProducts[$data['rank']] = $data;
        }

        return $arrReturnProducts;
    }

    /**
     * おすすめ商品の新規登録を行う。
     * @param Array   $arrPost      POSTの値を格納した配列
     * @param Integer $member_id    登録した管理者を示すID
     * @param SC_Helper_BestProducts_Ex  $objRecommend
     */
    public function insertRecommendProduct($arrPost, $member_id, SC_Helper_BestProducts_Ex &$objRecommend)
    {
        $sqlval = array();
        $sqlval['best_id'] = $arrPost['best_id'];
        $sqlval['product_id'] = $arrPost['product_id'];
        $sqlval['category_id'] = $arrPost['category_id'];
        $sqlval['rank'] = $arrPost['rank'];
        $sqlval['comment'] = $arrPost['comment'];
        $sqlval['creator_id'] = $member_id;

        $objRecommend->saveBestProducts($sqlval);
    }

    /**
     * データを削除する
     * @param  Array  $arrPost      POSTの値を格納した配列
     * @param  SC_Helper_BestProducts_Ex $objRecommend
     * @return void
     */
    public function deleteProduct($arrPost, SC_Helper_BestProducts_Ex &$objRecommend)
    {
        if ($arrPost['best_id']) {
            $target = $arrPost['best_id'];
        } else {
            $recommend = $objRecommend->getByRank($arrPost['rank']);
            $target = $recommend['best_id'];
        }
        $objRecommend->deleteBestProducts($target);
    }

    /**
     * 商品情報を取得する
     * @param  Integer $product_id 商品ID
     * @return Array   $return 商品のデータを格納した配列
     */
    public function getProduct($product_id)
    {
        $objProduct = new SC_Product_Ex();
        $arrProduct = $objProduct->getDetail($product_id);
        $return = array(
            'product_id' => $arrProduct['product_id'],
            'main_list_image' => $arrProduct['main_list_image'],
            'name' => $arrProduct['name']
        );

        return $return;
    }

    /**
     * 商品のデータを表示用に処理する
     * @param Array $arrPost  POSTのデータを格納した配列
     * @param Array $arrItems フロントに表示される商品の情報を格納した配列
     */
    public function setProducts($arrPost, $arrItems)
    {
        $arrProduct = $this->getProduct($arrPost['product_id']);
        if (count($arrProduct) > 0) {
            $rank = $arrPost['rank'];
            foreach ($arrProduct as $key => $val) {
                $arrItems[$rank][$key] = $val;
            }
            $arrItems[$rank]['rank'] = $rank;
        }

        return $arrItems;
    }
}
