<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メール設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Mail extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/mail.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'mail';
        $this->tpl_maintitle = t('c_Basic information_01');
        $this->tpl_subtitle = t('c_E-mail settings_01');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        $masterData = new SC_DB_MasterData_Ex();

        $mode = $this->getMode();

        if (!empty($_POST)) {
            $objFormParam = new SC_FormParam_Ex();
            $this->lfInitParam($mode, $objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            $this->arrErr = $objFormParam->checkError();
            $post = $objFormParam->getHashArray();
        }

        $this->arrMailTEMPLATE = $masterData->getMasterData('mtb_mail_template');

        switch ($mode) {
            case 'id_set':
                    $result = $this->lfGetMailTemplateByTemplateID($post['template_id']);
                    if ($result) {
                        $this->arrForm = $result[0];
                    } else {
                        $this->arrForm['template_id'] = $post['template_id'];
                    }
                break;
            case 'regist':

                    $this->arrForm = $post;
                    if ($this->arrErr) {
                        // エラーメッセージ
                        $this->tpl_msg = t('c_An error has occurred_01');

                    } else {
                        // 正常
                        $this->lfRegistMailTemplate($this->arrForm, $_SESSION['member_id']);

                        // 完了メッセージ
                        $this->tpl_onload = "window.alert('" .t('c_E-mail settings are complete. Select a template and check the contents._01'). "');";
                        unset($this->arrForm);
                    }
                break;
            default:
                break;
        }

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfGetMailTemplateByTemplateID($template_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = 'SELECT * FROM dtb_mailtemplate WHERE template_id = ?';
        return $objQuery->getAll($sql, array($template_id));
    }

    function lfRegistMailTemplate($post, $member_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $post['creator_id'] = $member_id;
        $post['update_date'] = 'CURRENT_TIMESTAMP';

        $sql = 'SELECT * FROM dtb_mailtemplate WHERE template_id = ?';
        $template_data = $objQuery->getAll($sql, array($post['template_id']));
        if ($template_data) {
            $sql_where = 'template_id = ?';
            $objQuery->update('dtb_mailtemplate', $post, $sql_where, array(addslashes($post['template_id'])));
        } else {
            $objQuery->insert('dtb_mailtemplate', $post);
        }

    }

    function lfInitParam($mode, &$objFormParam) {
        switch ($mode) {
            case 'regist':
                $objFormParam->addParam(t('c_E-mail title_01'), 'subject', MTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Header_01'), 'header', LTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Footer_01'), 'footer', LTEXT_LEN, 'KVa', array('EXIST_CHECK','SPTAB_CHECK','MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Template_01'), 'template_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
            case 'id_set':
                $objFormParam->addParam(t('c_Template_01'), 'template_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;
            default:
                break;
        }
    }
}
