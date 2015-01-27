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

namespace Eccube\Page\Admin\Contents;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\FormParam;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\Helper\BestProductsHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * おすすめ商品管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Recommend extends AbstractAdminPage
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
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $arrPost = $objFormParam->getHashArray();

        /* @var $objRecommend BestProductsHelper */
        $objRecommend = Application::alias('eccube.helper.best_products');

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
                if (Utils::isBlank($this->arrErr)) {
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
                if (Utils::isBlank($this->arrErr['rank']) && Utils::isBlank($this->arrErr['product_id'])) {
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
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $this->arrCatList = $objDb->getCategoryList('level = 1');
    }

    /**
     * パラメーターの初期化を行う
     * @param FormParam $objFormParam
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
     * @param  FormParam $objFormParam
     * @return Array  エラー内容
     */
    public function lfCheckError(&$objFormParam)
    {
        /* @var $objErr CheckError */
        $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        return $objErr->arrErr;
    }

    /**
     * 既に登録されている内容を取得する
     * @param  BestProductsHelper $objRecommend
     * @return Array  $arrReturnProducts データベースに登録されているおすすめ商品の配列
     */
    public function getRecommendProducts(BestProductsHelper &$objRecommend)
    {
        $arrList = $objRecommend->getList();
        // product_id の一覧を作成
        $product_ids = array();
        foreach ($arrList as $value) {
            $product_ids[] = $value['product_id'];
        }

        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
        $objQuery = $objQuery = Application::alias('eccube.query');
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
     * @param BestProductsHelper  $objRecommend
     */
    public function insertRecommendProduct($arrPost, $member_id, BestProductsHelper &$objRecommend)
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
     * @param  BestProductsHelper $objRecommend
     * @return void
     */
    public function deleteProduct($arrPost, BestProductsHelper &$objRecommend)
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
        /* @var $objProduct Product */
        $objProduct = Application::alias('eccube.product');
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
