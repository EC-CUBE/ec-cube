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


namespace Eccube\Page\Mypage;

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\FormParam;
use Eccube\Framework\Response;
use Eccube\Framework\Helper\AddressHelper;
use Eccube\Framework\DB\MasterData;
use Eccube\Framework\Util\Utils;

/**
 * お届け先編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Delivery extends AbstractMypage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_subtitle = 'お届け先追加･変更';
        $this->tpl_mypageno = 'delivery';
        $masterData         = Application::alias('eccube.db.master_data');
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->arrCountry   = $masterData->getMasterData('mtb_country');
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        /* @var $objCustomer Customer */
        $objCustomer = Application::alias('eccube.customer');
        $customer_id    = $objCustomer->getValue('customer_id');
        /* @var $objAddress AddressHelper */
        $objAddress = Application::alias('eccube.helper.address');
        /* @var $objFormParam FormParam */
        $objFormParam   = Application::alias('eccube.form_param');

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch ($this->getMode()) {
            // お届け先の削除
            case 'delete':
                if ($objFormParam->checkError()) {
                    Utils::sfDispSiteError(CUSTOMER_ERROR);
                    Application::alias('eccube.response')->actionExit();
                }

                if (!$objAddress->deleteAddress($objFormParam->getValue('other_deliv_id'), $customer_id)) {
                    Utils::sfDispSiteError(FREE_ERROR_MSG, '', false, '別のお届け先を削除できませんでした。');
                    Application::alias('eccube.response')->actionExit();
                }
                break;

            // スマートフォン版のもっと見るボタン用
            case 'getList':
                $arrData = $objFormParam->getHashArray();
                //別のお届け先情報
                $arrOtherDeliv = $objAddress->getList($customer_id, (($arrData['pageno'] - 1) * SEARCH_PMAX));
                //県名をセット
                $arrOtherDeliv = $this->setPref($arrOtherDeliv, $this->arrPref);
                $arrOtherDeliv['delivCount'] = count($arrOtherDeliv);
                $this->arrOtherDeliv = $arrOtherDeliv;

                echo Utils::jsonEncode($this->arrOtherDeliv);
                Application::alias('eccube.response')->actionExit();
                break;

            // お届け先の表示
            default:
                break;
        }

        //別のお届け先情報
        $this->arrOtherDeliv = $objAddress->getList($customer_id);

        //お届け先登録数
        $this->tpl_linemax = count($this->arrOtherDeliv);

        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
    }

    /**
     * フォームパラメータの初期化
     *
     * @param FormParam $objFormParam
     * @return FormParam
     */
    public function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('お届け先ID', 'other_deliv_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('現在ページ', 'pageno', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
    }

    /**
     * 県名をセット
     *
     * @param array $arrOtherDeliv
     * @param array $arrPref
     * return array
     */
    public function setPref($arrOtherDeliv, $arrPref)
    {
        if (is_array($arrOtherDeliv)) {
            foreach ($arrOtherDeliv as $key => $arrDeliv) {
                $arrOtherDeliv[$key]['prefname'] = $arrPref[$arrDeliv['pref']];
            }
        }

        return $arrOtherDeliv;
    }
}
