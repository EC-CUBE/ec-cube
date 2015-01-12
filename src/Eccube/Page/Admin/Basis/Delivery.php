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
use Eccube\Framework\Helper\DeliveryHelper;

/**
 * 配送方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Delivery extends AbstractAdminPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'basis/delivery.tpl';
        $this->tpl_subno = 'delivery';
        $this->tpl_mainno = 'basis';
        $masterData = Application::alias('eccube.db.master_data');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData('mtb_taxrule');
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '配送方法設定';
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
        /* @var $objDeliv DeliveryHelper */
        $objDeliv = Application::alias('eccube.helper.delivery');
        $mode = $this->getMode();

        if (!empty($_POST)) {
            $objFormParam = Application::alias('eccube.form_param');
            $objFormParam->setParam($_POST);

            $this->arrErr = $this->lfCheckError($mode, $objFormParam);
            if (!empty($this->arrErr['deliv_id'])) {
                trigger_error('', E_USER_ERROR);

                return;
            }
        }

        switch ($mode) {
            case 'delete':
                // ランク付きレコードの削除
                $objDeliv->delete($_POST['deliv_id']);

                $this->objDisplay->reload(); // PRG pattern
                break;
            case 'up':
                $objDeliv->rankUp($_POST['deliv_id']);

                $this->objDisplay->reload(); // PRG pattern
                break;
            case 'down':
                $objDeliv->rankDown($_POST['deliv_id']);

                $this->objDisplay->reload(); // PRG pattern
                break;
            default:
                break;
        }
        $this->arrDelivList = $objDeliv->getList();
    }

    /**
     * 入力エラーチェック
     *
     * @param  string $mode
     * @param FormParam $objFormParam
     * @return array
     */
    public function lfCheckError($mode, &$objFormParam)
    {
        $arrErr = array();
        switch ($mode) {
            case 'delete':
            case 'up':
            case 'down':
                $objFormParam->addParam('配送業者ID', 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));

                $objFormParam->convParam();

                $arrErr = $objFormParam->checkError();
                break;
            default:
                break;
        }

        return $arrErr;
    }
}
