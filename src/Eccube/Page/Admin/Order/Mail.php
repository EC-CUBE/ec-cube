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

namespace Eccube\Page\Admin\Order;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\Response;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;

/**
 * 受注メール管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Mail extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'order/mail.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'index';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '受注管理';

        $masterData = Application::alias('eccube.db.master_data');
        $this->arrMAILTEMPLATE = $masterData->getMasterData('mtb_mail_template');
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
        $post = $_POST;
        //一括送信用の処理
        if (array_key_exists('mail_order_id', $post) and $post['mode'] == 'mail_select') {
            $post['order_id_array'] = implode(',', $post['mail_order_id']);
        } elseif (!array_key_exists('order_id_array', $post)) {
            $post['order_id_array'] = $post['order_id'];
        }

        //一括送信処理変数チェック(ここですべきかは課題)
        if (preg_match("/^[0-9|\,]*$/", $post['order_id_array'])) {
            $this->order_id_array = $post['order_id_array'];
        } else {
            //エラーで元に戻す
            Application::alias('eccube.response')->sendRedirect(ADMIN_ORDER_URLPATH);
            Application::alias('eccube.response')->actionExit();
        }

        //メール本文の確認例は初めの1受注とする
        if (!Utils::isBlank($this->order_id_array)) {
            $order_id_array = split(',', $this->order_id_array);
            $post['order_id'] = intval($order_id_array[0]);
            $this->order_id_count = count($order_id_array);
        }

        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // POST値の取得
        $objFormParam->setParam($post);
        $objFormParam->convParam();
        $this->tpl_order_id = $objFormParam->getValue('order_id');

        // 検索パラメーターの引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        // 履歴を読み込むか
        $load_history = Utils::sfIsInt($this->tpl_order_id);

        switch ($this->getMode()) {
            case 'confirm':
                $status = $this->confirm($objFormParam);
                if ($status === true) {
                    $load_history = false;
                } else {
                    $this->arrErr = $status;
                }
                break;

            case 'send':
                $sendStatus = $this->doSend($objFormParam);
                if ($sendStatus === true) {
                    Application::alias('eccube.response')->sendRedirect(ADMIN_ORDER_URLPATH);
                    Application::alias('eccube.response')->actionExit();
                }
                $this->arrErr = $sendStatus;
                break;

            case 'change':
                $objFormParam =  $this->changeData($objFormParam);
                break;

            case 'pre_edit':
            case 'mail_select':
            case 'return':
            default:
                break;
        }

        // 入力内容の引き継ぎ
        $this->arrForm = $objFormParam->getFormParamList();

        if ($load_history) {
            $this->arrMailHistory = $this->getMailHistory($this->tpl_order_id);
        }
    }

    /**
     * 指定された注文番号のメール履歴を取得する。
     * @var int order_id
     */
    public function getMailHistory($order_id)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'send_date, subject, template_id, send_id';
        $where = 'order_id = ?';
        $objQuery->setOrder('send_date DESC');

        return $objQuery->select($col, 'dtb_mail_history', $where, array($order_id));
    }

    /**
     *
     * メールを送る。
     * @param FormParam $objFormParam
     */
    public function doSend(&$objFormParam)
    {
        $arrErr = $objFormParam->checkerror();

        // メールの送信
        if (count($arrErr) == 0) {
            // 注文受付メール(複数受注ID対応)
            $order_id_array = explode(',', $this->order_id_array);
            foreach ($order_id_array as $order_id) {
                /* @var $objMail MailHelper */
                $objMail = Application::alias('eccube.helper.mail');
                $objSendMail = $objMail->sfSendOrderMail($order_id,
                    $objFormParam->getValue('template_id'),
                    $objFormParam->getValue('subject'),
                    $objFormParam->getValue('header'),
                    $objFormParam->getValue('footer')
                );
            }
            // TODO $SendMail から送信がちゃんと出来たか確認できたら素敵。
            return true;
        }

        return $arrErr;
    }

    /**
     * 確認画面を表示する為の準備
     * @param FormParam $objFormParam
     */
    public function confirm(&$objFormParam)
    {
        $arrErr = $objFormParam->checkerror();
        // メールの送信
        if (count($arrErr) == 0) {
            // 注文受付メール(送信なし)
            /* @var $objMail MailHelper */
            $objMail = Application::alias('eccube.helper.mail');
            $objSendMail = $objMail->sfSendOrderMail(
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'), false);

            $this->tpl_subject = $objFormParam->getValue('subject');
            $this->tpl_body = mb_convert_encoding($objSendMail->body, CHAR_CODE, 'auto');
            $this->tpl_to = $objSendMail->tpl_to;
            $this->tpl_mainpage = 'order/mail_confirm.tpl';

            return true;
        }

        return $arrErr;
    }

    /**
     *
     * テンプレートの文言をフォームに入れる。
     * @param FormParam $objFormParam
     */
    public function changeData(&$objFormParam)
    {
        $template_id = $objFormParam->getValue('template_id');

        // 未選択時
        if (strlen($template_id) === 0) {
            $mailTemplates = null;
        // 有効選択時
        } elseif (Utils::sfIsInt($template_id)) {
            /* @var $objMailtemplate MailtemplateHelper */
            $objMailtemplate = Application::alias('eccube.helper.mailtemplate');
            $mailTemplates = $objMailtemplate->get($template_id);
        // 不正選択時
        } else {
            trigger_error('テンプレートの指定が不正。', E_USER_ERROR);
        }

        if (empty($mailTemplates)) {
            foreach (array('subject', 'header', 'footer') as $key) {
                $objFormParam->setValue($key, '');
            }
        } else {
            $objFormParam->setParam($mailTemplates);
        }

        return $objFormParam;
    }

    /**
     * パラメーター情報の初期化
     * @param FormParam $objFormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        // 検索条件のパラメーターを初期化
        parent::lfInitParam($objFormParam);
        $objFormParam->addParam('テンプレート', 'template_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('メールタイトル', 'subject', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam('ヘッダー', 'header', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam('フッター', 'footer', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
    }
}
