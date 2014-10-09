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
 * 支払方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Payment extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/payment.tpl';
        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'payment';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '支払方法設定';
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
        $objPayment = new SC_Helper_Payment_Ex();

        if (!empty($_POST)) {
            $objFormParam = new SC_FormParam_Ex();
            $objFormParam->addParam('支払方法ID', 'payment_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            $this->arrErr = $objFormParam->checkError();
            if (!empty($this->arrErr['payment_id'])) {
                trigger_error('', E_USER_ERROR);

                return;
            }
            $post = $objFormParam->getHashArray();
        }

        switch ($this->getMode()) {
            case 'delete':
                // ランク付きレコードの削除
                $objPayment->delete($post['payment_id']);

                // 再表示
                $this->objDisplay->reload();
                break;
            case 'up':
                $objPayment->rankUp($post['payment_id']);

                // 再表示
                $this->objDisplay->reload();
                break;
            case 'down':
                $objPayment->rankDown($post['payment_id']);

                // 再表示
                $this->objDisplay->reload();
                break;
        }
        $this->arrPaymentListFree = $objPayment->getList();
    }
}
