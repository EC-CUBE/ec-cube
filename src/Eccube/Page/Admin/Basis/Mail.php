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

namespace Eccube\Page\Admin\Basis;

use Eccube\Application;
use Eccube\Page\Admin\AbstractAdminPage;
use Eccube\Framework\FormParam;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\MailtemplateHelper;

/**
 * メール設定 のページクラス.
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
        $this->tpl_mainpage = 'basis/mail.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'mail';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = 'メール設定';
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
        $masterData = Application::alias('eccube.db.master_data');
        /* @var $objMailtemplate MailtemplateHelper */
        $objMailtemplate = Application::alias('eccube.helper.mailtemplate');

        $mode = $this->getMode();

        if (!empty($_POST)) {
            $objFormParam = Application::alias('eccube.form_param');
            $this->lfInitParam($mode, $objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            $this->arrErr = $objFormParam->checkError();
            $post = $objFormParam->getHashArray();
        }

        $this->arrMailTEMPLATE = $masterData->getMasterData('mtb_mail_template');

        switch ($mode) {
            case 'id_set':
                    $mailtemplate = $objMailtemplate->get($post['template_id']);
                    if ($mailtemplate) {
                        $this->arrForm = $mailtemplate;
                    } else {
                        $this->arrForm['template_id'] = $post['template_id'];
                    }
                break;
            case 'regist':

                    $this->arrForm = $post;
                    if ($this->arrErr) {
                        // エラーメッセージ
                        $this->tpl_msg = 'エラーが発生しました';
                    } else {
                        // 正常
                        $this->lfRegistMailTemplate($this->arrForm, $_SESSION['member_id'], $objMailtemplate);

                        // 完了メッセージ
                        $this->tpl_onload = "window.alert('メール設定が完了しました。テンプレートを選択して内容をご確認ください。');";
                        unset($this->arrForm);
                    }
                break;
            default:
                break;
        }

    }

    public function lfRegistMailTemplate($post, $member_id, MailtemplateHelper $objMailtemplate)
    {
        $post['creator_id'] = $member_id;
        $objMailtemplate->save($post);
    }

    /**
     * @param string|null $mode
     * @param FormParam $objFormParam
     */
    public function lfInitParam($mode, &$objFormParam)
    {
        switch ($mode) {
            case 'regist':
                $objFormParam->addParam('メールタイトル', 'subject', MTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('ヘッダー', 'header', LTEXT_LEN, 'KVa', array('SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('フッター', 'footer', LTEXT_LEN, 'KVa', array('SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam('テンプレート', 'template_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
            case 'id_set':
                $objFormParam->addParam('テンプレート', 'template_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
            default:
                break;
        }
    }
}
