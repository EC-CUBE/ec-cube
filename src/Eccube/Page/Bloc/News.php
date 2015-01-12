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
use Eccube\Framework\Display;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\NewsHelper;
use Eccube\Framework\Util\Utils;

/**
 * 新着情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class News extends AbstractBloc
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
        $objNews = Application::alias('eccube.helper.news');
        $objFormParam = Application::alias('eccube.form_param');
        switch ($this->getMode()) {
            case 'getList':
                $this->lfInitNewsParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->arrErr = $objFormParam->checkError(false);
                if (empty($this->arrErr)) {
                    $arrData = $objFormParam->getHashArray();
                    $json = $this->lfGetNewsForJson($arrData, $objNews);
                    echo $json;
                    Application::alias('eccube.response')->actionExit();
                } else {
                    echo $this->lfGetErrors($this->arrErr);
                    Application::alias('eccube.response')->actionExit();
                }
                break;
            case 'getDetail':
                $this->lfInitNewsParam($objFormParam);
                $objFormParam->setParam($_GET);
                $objFormParam->convParam();
                $this->arrErr = $objFormParam->checkError(false);
                if (empty($this->arrErr)) {
                    $arrData = $objFormParam->getHashArray();
                    $json = $this->lfGetNewsDetailForJson($arrData);
                    echo $json;
                    Application::alias('eccube.response')->actionExit();
                } else {
                    echo $this->lfGetErrors($this->arrErr);
                    Application::alias('eccube.response')->actionExit();
                }
                break;
            default:
                $this->arrNews = $objNews->getList();
                $this->newsCount = $objNews->getCount();
                break;
        }

    }

    /**
     * 新着情報パラメーター初期化
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return void
     */
    public function lfInitNewsParam(FormParam &$objFormParam)
    {
        $objFormParam->addParam('現在ページ', 'pageno', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('表示件数', 'disp_number', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('新着ID', 'news_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
    }

    /**
     * 新着情報を取得する.
     *
     * @return array $arrNewsList 新着情報の配列を返す
     */
    public function lfGetNews($dispNumber, $pageNo, NewsHelper $objNews)
    {
        $arrNewsList = $objNews->getList($dispNumber, $pageNo);

        // モバイルサイトのセッション保持 (#797)
        if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
            foreach ($arrNewsList as $key => $value) {
                $arrRow =& $arrNewsList[$key];
                if (Utils::isAppInnerUrl($arrRow['news_url'])) {
                    $netUrl = new \Net_URL($arrRow['news_url']);
                    $netUrl->addQueryString(session_name(), session_id());
                    $arrRow['news_url'] = $netUrl->getURL();
                }
            }
        }

        return $arrNewsList;
    }

    /**
     * 新着情報をJSON形式で取得する
     * (ページと表示件数を指定)
     *
     * @param  array  $arrData フォーム入力値
     * @param  NewsHelper $objNews
     * @return String $json 新着情報のJSONを返す
     */
    public function lfGetNewsForJson($arrData, NewsHelper $objNews)
    {
        $dispNumber = $arrData['disp_number'];
        $pageNo = $arrData['pageno'];
        $arrNewsList = $this->lfGetNews($dispNumber, $pageNo, $objNews);

        //新着情報の最大ページ数をセット
        $newsCount = $objNews->getCount();
        $arrNewsList['news_page_count'] = ceil($newsCount / 3);

        $json =  Utils::jsonEncode($arrNewsList);    //JSON形式

        return $json;
    }

    /**
     * 新着情報1件分をJSON形式で取得する
     * (news_idを指定)
     *
     * @param  array  $arrData フォーム入力値
     * @return String $json 新着情報1件分のJSONを返す
     */
    public function lfGetNewsDetailForJson($arrData)
    {
        $arrNewsList = Application::alias('eccube.helper.news')->getNews($arrData['news_id']);
        $json =  Utils::jsonEncode($arrNewsList);    //JSON形式

        return $json;
    }

    /**
     * エラーメッセージを整形し, JSON 形式で返す.
     *
     * @param  array  $arrErr エラーメッセージの配列
     * @return string JSON 形式のエラーメッセージ
     */
    public function lfGetErrors($arrErr)
    {
        $messages = '';
        foreach ($arrErr as $val) {
            $messages .= $val . "\n";
        }

        return Utils::jsonEncode(array('error' => $messages));
    }
}
