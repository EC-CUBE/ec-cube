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
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\NewsHelper;
use Eccube\Framework\Util\Utils;

/**
 * コンテンツ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'contents/index.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'contents';
        $this->arrForm = array(
            'year' => date('Y'),
            'month' => date('n'),
            'day' => date('j'),
        );
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = '新着情報管理';
        //---- 日付プルダウン設定
        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date', ADMIN_NEWS_STARTYEAR);
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
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
        /* @var $objNews NewsHelper */
        $objNews = Application::alias('eccube.helper.news');

        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $news_id = $objFormParam->getValue('news_id');

        //---- 新規登録/編集登録
        switch ($this->getMode()) {
            case 'edit':
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (isset($this->arrErr['news_id']) && !Utils::isBlank($this->arrErr['news_id'])) {
                    trigger_error('', E_USER_ERROR);

                    return;
                }

                if (count($this->arrErr) <= 0) {
                    // POST値の引き継ぎ
                    $arrParam = $objFormParam->getHashArray();
                    // 登録実行
                    $res_news_id = $this->doRegist($news_id, $arrParam, $objNews);
                    if ($res_news_id !== FALSE) {
                        // 完了メッセージ
                        $news_id = $res_news_id;
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                }
                // POSTデータを引き継ぐ
                $this->tpl_news_id = $news_id;
                break;

            case 'pre_edit':
                $news = $objNews->getNews($news_id);
                list($news['year'],$news['month'],$news['day']) = $this->splitNewsDate($news['cast_news_date']);
                $objFormParam->setParam($news);

                // POSTデータを引き継ぐ
                $this->tpl_news_id = $news_id;
                break;

            case 'delete':
            //----　データ削除
                $objNews->deleteNews($news_id);
                //自分にリダイレクト（再読込による誤動作防止）
                Application::alias('eccube.response')->reload();
                break;

            //----　表示順位移動
            case 'up':
                $objNews->rankUp($news_id);

                // リロード
                Application::alias('eccube.response')->reload();
                break;

            case 'down':
                $objNews->rankDown($news_id);

                // リロード
                Application::alias('eccube.response')->reload();
                break;

            case 'moveRankSet':
            //----　指定表示順位移動
                $input_pos = $this->getPostRank($news_id);
                if (Utils::sfIsInt($input_pos)) {
                    $objNews->moveRank($news_id, $input_pos);
                }
                Application::alias('eccube.response')->reload();
                break;

            default:
                break;
        }

        $this->arrNews = $objNews->getList();
        $this->line_max = count($this->arrNews);

        $this->arrForm = $objFormParam->getFormParamList();
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
        $objErr->doFunc(array('日付', 'year', 'month', 'day'), array('CHECK_DATE'));

        return $objErr->arrErr;
    }

    /**
     * パラメーターの初期化を行う
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('news_id', 'news_id');
        $objFormParam->addParam('日付(年)', 'year', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('日付(月)', 'month', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('日付(日)', 'day', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('タイトル', 'news_title', MTEXT_LEN, 'KVa', array('EXIST_CHECK','MAX_LENGTH_CHECK','SPTAB_CHECK'));
        $objFormParam->addParam('URL', 'news_url', URL_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('本文', 'news_comment', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('別ウィンドウで開く', 'link_method', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 登録処理を実行.
     *
     * @param  integer  $news_id
     * @param  array    $sqlval
     * @param  NewsHelper   $objNews
     * @return multiple
     */
    public function doRegist($news_id, $sqlval, NewsHelper $objNews)
    {
        $sqlval['news_id'] = $news_id;
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['link_method'] = $this->checkLinkMethod($sqlval['link_method']);
        $sqlval['news_date'] = $this->getRegistDate($sqlval);
        unset($sqlval['year'], $sqlval['month'], $sqlval['day']);

        return $objNews->saveNews($sqlval);
    }

    /**
     * データの登録日を返す。
     * @param  Array  $arrPost POSTのグローバル変数
     * @return string 登録日を示す文字列
     */
    public function getRegistDate($arrPost)
    {
        $registDate = $arrPost['year'] .'/'. $arrPost['month'] .'/'. $arrPost['day'];

        return $registDate;
    }

    /**
     * チェックボックスの値が空の時は無効な値として1を格納する
     * @param  int $link_method
     * @return int
     */
    public function checkLinkMethod($link_method)
    {
        if (strlen($link_method) == 0) {
            $link_method = 1;
        }

        return $link_method;
    }

    /**
     * ニュースの日付の値をフロントでの表示形式に合わせるために分割
     * @param String $news_date
     */
    public function splitNewsDate($news_date)
    {
        return explode('-', $news_date);
    }

    /**
     * POSTされたランクの値を取得する
     * @param Integer $news_id
     */
    public function getPostRank($news_id)
    {
        if (strlen($news_id) > 0 && is_numeric($news_id) == true) {
            $key = 'pos-' . $news_id;
            $input_pos = $_POST[$key];

            return $input_pos;
        }
    }
}
