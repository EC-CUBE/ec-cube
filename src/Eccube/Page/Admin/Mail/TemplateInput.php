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
use Eccube\Framework\FormParam;
use Eccube\Framework\Query;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Helper\MailHelper;
use Eccube\Framework\Util\Utils;

/**
 * テンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class TemplateInput extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'mail/template_input.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = 'テンプレート設定';
        $this->tpl_subno = 'template';
        $this->mode = 'regist';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrMagazineType = $masterData->getMasterData('mtb_magazine_type');
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
        /* @var $objMailHelper MailHelper */
        $objMailHelper = Application::alias('eccube.helper.mail');

        switch ($this->getMode()) {
            case 'edit':
                // 編集
                if (Utils::sfIsInt($_GET['template_id'])===true) {
                    $arrMail = $objMailHelper->sfGetMailmagaTemplate($_GET['template_id']);
                    $this->arrForm = $arrMail[0];
                }
                break;
            case 'regist':
                // 新規登録
                $objFormParam = Application::alias('eccube.form_param');

                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $objFormParam->checkError();
                $this->arrForm = $objFormParam->getHashArray();

                if (Utils::isBlank($this->arrErr)) {
                    // エラーが無いときは登録・編集
                    $this->lfRegistData($objFormParam, $objFormParam->getValue('template_id'));

                    // 自分を再読込して、完了画面へ遷移
                    $this->objDisplay->reload(array('mode' => 'complete'));
                } else {
                    $this->arrForm['template_id'] = $objFormParam->getValue('template_id');
                }
                break;
            case 'complete':
                // 完了画面表示
                $this->tpl_mainpage = 'mail/template_complete.tpl';
                break;
            default:
                break;
        }

    }

    /**
     * メルマガテンプレートデータの登録・更新を行う
     *
     * @param FormParam $objFormParam FormParam インスタンス
     * @param integer template_id 更新時は指定
     * @return void
     */
    public function lfRegistData(&$objFormParam, $template_id = null)
    {
        $objQuery = Application::alias('eccube.query');
        $sqlval = $objFormParam->getDbArray();

        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';

        if (Utils::sfIsInt($template_id)) {
            // 更新時
            $objQuery->update('dtb_mailmaga_template',
                              $sqlval,
                              'template_id = ?',
                              array($template_id));
        } else {
            // 新規登録時
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            $sqlval['template_id'] = $objQuery->nextVal('dtb_mailmaga_template_template_id');
            $objQuery->insert('dtb_mailmaga_template', $sqlval);
        }
    }

    /**
     * お問い合わせ入力時のパラメーター情報の初期化を行う.
     *
     * @param  FormParam $objFormParam FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('メール形式', 'mail_method', INT_LEN, 'n', array('EXIST_CHECK','ALNUM_CHECK'));
        $objFormParam->addParam('Subject', 'subject', STEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('本文', 'body', LLTEXT_LEN, 'KVCa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('テンプレートID', 'template_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
    }
}
