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

namespace Eccube\Page\Admin\Mail;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\Date;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\CustomerHelper;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;

/**
 * メルマガ管理 のページクラス.
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
        $this->tpl_mainpage = 'mail/index.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = '配信内容設定';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData('mtb_job');
        $this->arrJob['不明'] = '不明';
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrPageRows = $masterData->getMasterData('mtb_page_max');
        $this->arrHtmlmail = array('' => 'HTML+TEXT',  1 => 'HTML', 2 => 'TEXT', 99 => '全員（メルマガ拒否している会員も含む）');
        $this->arrMailType = $masterData->getMasterData('mtb_mail_type');

        // 日付プルダウン設定
        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date', BIRTH_YEAR);
        $this->arrBirthYear = $objDate->getYear();
        $this->arrRegistYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        $this->objDate = $objDate;

        // カテゴリ一覧設定
        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $this->arrCatList = $objDb->getCategoryList();

        // テンプレート一覧設定
        $this->arrTemplate = $this->lfGetMailTemplateList(Application::alias('eccube.helper.mail')->sfGetMailmagaTemplate());

        $this->httpCacheControl('nocache');
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
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        $this->lfInitParamSearchCustomer($objFormParam);
        $objFormParam->setParam($_POST);

        // パラメーター読み込み
        $this->arrHidden = $objFormParam->getSearchArray();

        // 入力パラメーターチェック
        $this->arrErr = Application::alias('eccube.helper.customer')->sfCheckErrorSearchParam($objFormParam);
        $this->arrForm = $objFormParam->getFormParamList();
        if (!Utils::isBlank($this->arrErr)) return;

        // モードによる処理切り替え
        switch ($this->getMode()) {
            // 配信先検索
            case 'search':
            case 'back':
                list($this->tpl_linemax, $this->arrResults, $this->objNavi) = Application::alias('eccube.helper.customer')->sfGetSearchData($objFormParam->getHashArray());
                $this->arrPagenavi = $this->objNavi->arrPagenavi;
                break;
            // input:検索結果画面「配信内容を設定する」押下後
            case 'input':
                $this->tpl_mainpage = 'mail/input.tpl';
                break;
            // template:テンプレート選択時
            case 'template':
            case 'regist_back':
                $this->tpl_mainpage = 'mail/input.tpl';
                if (Utils::sfIsInt($_POST['template_id']) === true) {
                    $this->lfAddParamSelectTemplate($objFormParam);
                    $this->lfGetTemplateData($objFormParam, $_POST['template_id']);
                    // regist_back時、subject,bodyにはテンプレートを読み込むのではなく、入力内容で上書き
                    if ($this->getMode()=='regist_back') {
                        $objFormParam->setParam($_POST);
                    }
                }
                break;
            case 'regist_confirm':
                $this->tpl_mainpage = 'mail/input.tpl';
                $this->lfAddParamSelectTemplate($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) $this->tpl_mainpage = 'mail/input_confirm.tpl';
                break;
            case 'regist_complete':
                $this->tpl_mainpage = 'mail/input.tpl';
                $this->lfAddParamSelectTemplate($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $objFormParam->checkError();
                if (Utils::isBlank($this->arrErr)) {
                    $this->tpl_mainpage = 'mail/index.tpl';
                    Application::alias('eccube.helper.mail')->sfSendMailmagazine($this->lfRegisterData($objFormParam));  // DB登録・送信

                    Application::alias('eccube.response')->sendRedirect('./history.php');
                }
                break;
            // query:配信履歴から「確認」
            case 'query':
                if (Utils::sfIsInt($_GET['send_id'])) {
                    $this->arrSearchData = $this->lfGetMailQuery($_GET['send_id']);
                }
                $this->setTemplate('mail/query.tpl');
                break;
            // query:配信履歴から「再送信」
            case 'retry':
                if (Utils::sfIsInt($_GET['send_id'])) {
                    Application::alias('eccube.helper.mail')->sfSendMailmagazine($_GET['send_id']);  // DB登録・送信

                    Application::alias('eccube.response')->sendRedirect('./history.php');
                } else {
                    $this->tpl_onload = "window.alert('メール送信IDが正しくありません');";
                }
                break;
            default:
                break;
        }
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * パラメーター情報の初期化（初期会員検索時）
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return void
     */
    public function lfInitParamSearchCustomer(&$objFormParam)
    {
        Application::alias('eccube.helper.customer')->sfSetSearchParam($objFormParam);
        $objFormParam->addParam('配信形式', 'search_htmlmail', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('配信メールアドレス種別', 'search_mail_type', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
    }

    /**
     * パラメーター情報の追加（テンプレート選択）
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @return void
     */
    public function lfAddParamSelectTemplate(&$objFormParam)
    {
        $objFormParam->addParam('メール形式', 'mail_method', INT_LEN, 'n', array('EXIST_CHECK','ALNUM_CHECK'));
        $objFormParam->addParam('Subject', 'subject', STEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('本文', 'body', LLTEXT_LEN, 'KVCa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('テンプレートID', 'template_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
    }

    /**
     * メルマガテンプレート一覧情報の取得
     *
     * @param  array $arrTemplate MailHelper::sfGetMailmagaTemplate()の戻り値
     * @return array key:template_id value:サブジェクト【配信形式】
     */
    public function lfGetMailTemplateList($arrTemplate)
    {
        if (is_array($arrTemplate)) {
            foreach ($arrTemplate as $line) {
                $return[$line['template_id']] = '【' . $this->arrHtmlmail[$line['mail_method']] . '】' . $line['subject'];
            }
        }

        return $return;
    }

    /**
     * テンプレートIDから情報の取得して$objFormParamにset_paramする
     *
     * @param  FormParam $objFormParam フォームパラメータークラス
     * @param  array $template_id  テンプレートID
     * @return void
     */
    public function lfGetTemplateData(&$objFormParam, $template_id)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->setOrder('template_id DESC');
        $where = 'template_id = ?';
        $arrResults = $objQuery->getRow('*', 'dtb_mailmaga_template', $where, array($template_id));
        $objFormParam->setParam($arrResults);
    }

    /**
     * 配信内容と配信リストを書き込む
     *
     * @param FormParam $objFormParam
     * @return integer 登録した行の dtb_send_history.send_id の値
     */
    public function lfRegisterData(&$objFormParam)
    {
        $objQuery = Application::alias('eccube.query');

        list($linemax, $arrSendCustomer, $objNavi) = Application::alias('eccube.helper.customer')->sfGetSearchData($objFormParam->getHashArray(), 'All');
        $send_customer_cnt = count($arrSendCustomer);

        $send_id = $objQuery->nextVal('dtb_send_history_send_id');
        $dtb_send_history = array();
        $dtb_send_history['mail_method'] = $objFormParam->getValue('mail_method');
        $dtb_send_history['subject'] = $objFormParam->getValue('subject');
        $dtb_send_history['body'] = $objFormParam->getValue('body');
        $dtb_send_history['start_date'] = 'CURRENT_TIMESTAMP';
        $dtb_send_history['creator_id'] = $_SESSION['member_id'];
        $dtb_send_history['send_count'] = $send_customer_cnt;
        $dtb_send_history['search_data'] = serialize($objFormParam->getSearchArray());
        $dtb_send_history['update_date'] = 'CURRENT_TIMESTAMP';
        $dtb_send_history['create_date'] = 'CURRENT_TIMESTAMP';
        $dtb_send_history['send_id'] = $send_id;
        $objQuery->insert('dtb_send_history', $dtb_send_history);
        // 「配信メールアドレス種別」に携帯メールアドレスが指定されている場合は、携帯メールアドレスに配信
        $emailtype='email';
        $searchmailtype = $objFormParam->getValue('search_mail_type');
        if ($searchmailtype==2 || $searchmailtype==4) {
            $emailtype='email_mobile';
        }
        if (is_array($arrSendCustomer)) {
            foreach ($arrSendCustomer as $line) {
                $dtb_send_customer = array();
                $dtb_send_customer['customer_id'] = $line['customer_id'];
                $dtb_send_customer['send_id'] = $send_id;
                $dtb_send_customer['email'] = $line[$emailtype];
                $dtb_send_customer['name'] = $line['name01'] . ' ' . $line['name02'];
                $objQuery->insert('dtb_send_customer', $dtb_send_customer);
            }
        }

        return $send_id;
    }

    /**
     * 配信履歴から条件を取得する
     *
     * @return array
     */
    public function lfGetMailQuery($send_id)
    {
        $objQuery = Application::alias('eccube.query');

        // 送信履歴より、送信条件確認画面
        $sql = 'SELECT search_data FROM dtb_send_history WHERE send_id = ?';
        $searchData = $objQuery->getOne($sql, array($send_id));

        return unserialize($searchData);
    }
}
