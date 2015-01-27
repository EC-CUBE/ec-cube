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
use Eccube\Framework\Helper\PaymentHelper;

/**
 * 支払方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Payment extends AbstractAdminPage
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
        /* @var $objPayment PaymentHelper */
        $objPayment = Application::alias('eccube.helper.payment');

        if (!empty($_POST)) {
            $objFormParam = Application::alias('eccube.form_param');
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
