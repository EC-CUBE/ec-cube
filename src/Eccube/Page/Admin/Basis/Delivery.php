<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
