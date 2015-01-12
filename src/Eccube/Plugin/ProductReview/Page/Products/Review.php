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

namespace Eccube\Plugin\ProductReview\Page\Products;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\CheckError;
use Eccube\Framework\Customer;
use Eccube\Framework\FormParam;
use Eccube\Framework\Product;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\Db\MasterData;
use Eccube\Framework\Util\Utils;
use Eccube\Plugin\ProductReview\Helper\ReviewHelper;

/**
 * お客様の声投稿のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Review extends AbstractPage
{
    /** おすすめレベル */
    public $arrRECOMMEND;

    /** 性別 */
    public $arrSex;

    /** 入力禁止URL */
    public $arrReviewDenyURL;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        /* @var $masterData MasterData */
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrRECOMMEND = $masterData->getMasterData('mtb_recommend');
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrReviewDenyURL = $masterData->getMasterData('mtb_review_deny_url');
        $this->tpl_mainpage = 'products/review.tpl';
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     */
    public function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        /* @var $objFormParam FormParam */
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch ($this->getMode()) {
            case 'confirm':
                $this->arrErr = $this->lfCheckError($objFormParam);

                //エラーチェック
                if (empty($this->arrErr)) {
                    $this->tpl_mainpage = 'products/review_confirm.tpl';
                }
                break;

            case 'return':
                break;

            case 'complete':
                $this->arrErr = $this->lfCheckError($objFormParam);
                //エラーチェック
                if (empty($this->arrErr)) {
                    //登録実行
                    $this->lfRegistRecommendData($objFormParam);

                    //レビュー書き込み完了ページへ
                    Application::alias('eccube.response')->sendRedirect('review_complete.php');
                    Application::alias('eccube.response')->actionExit();
                }
                break;

            default:
                // 最初のproduct_idは、$_GETで渡ってくる。
                $objFormParam->setParam($_GET);
                break;
        }

        $this->arrForm = $objFormParam->getHashArray();

        //商品名の取得
        $this->arrForm['name'] = $this->lfGetProductName($this->arrForm['product_id']);
        if (empty($this->arrForm['name'])) {
            Utils::sfDispSiteError(PRODUCT_NOT_FOUND);
        }

        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(FormParam &$objFormParam)
    {
        $objFormParam->addParam('レビューID', 'review_id', INT_LEN, 'aKV');
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('NUM_CHECK','EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('投稿者名', 'reviewer_name', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('投稿者URL', 'reviewer_url', MTEXT_LEN, 'a', array('NO_SPTAB', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'URL_CHECK'));
        $objFormParam->addParam('性別', 'sex', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('おすすめレベル', 'recommend_level', INT_LEN, 'n', array('EXIST_CHECK', 'SELECT_CHECK'));
        $objFormParam->addParam('タイトル', 'title', STEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('コメント', 'comment', LTEXT_LEN, 'aKV', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return array        エラーメッセージの配列
     */
    public function lfCheckError(FormParam &$objFormParam)
    {
        $arrErr = $objFormParam->checkError();

        if (REVIEW_ALLOW_URL == false) {
            /* @var $objErr CheckError */
            $objErr = Application::alias('eccube.check_error', $objFormParam->getHashArray());
            // コメント欄へのURLの入力を禁止
            $objErr->doFunc(array('URL', 'comment', $this->arrReviewDenyURL), array('PROHIBITED_STR_CHECK'));
            $arrErr += $objErr->arrErr;
        }

        return $arrErr;
    }

    /**
     * 商品名を取得
     *
     * @param  integer $product_id 商品ID
     * @return string  $product_name 商品名
     */
    public function lfGetProductName($product_id)
    {
        /* @var $objQuery Query */
        $objQuery = Application::alias('eccube.query');

        return $objQuery->get('name', 'dtb_products', 'product_id = ? AND ' . Application::alias('eccube.product')->getProductDispConditions(), array($product_id));
    }

    //登録実行
    public function lfRegistRecommendData(FormParam &$objFormParam)
    {
        $arrRegist = $objFormParam->getDbArray();

        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        if ($objCustomer->isLoginSuccess(true)) {
            $arrRegist['customer_id'] = $objCustomer->getValue('customer_id');
        }

        //-- 登録実行
        /* @var $objReview ReviewHelper */
        $objReview = Application::alias('eccube.helper.review');
        $objReview->save($arrRegist);
    }
}
