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

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
require_once CLASS_EX_REALDIR . 'api_extends/SC_Api_Abstract_Ex.php';

class API_ItemLookup extends SC_Api_Abstract_Ex {

    protected $operation_name = 'ItemLookup';
    protected $operation_description = '';
    protected $default_auth_types = self::API_AUTH_TYPE_OPEN;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function __construct() {
        parent::__construct();
        $this->operation_description = t('c_Product details information will be retrieved_01');
    }

    public function doAction($arrParam) {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {
            $objProduct = new SC_Product_Ex();

            switch ($arrRequest['IdType']) {
            case 'product_code':
                $search_column = 'product_code';
                break;
            case 'product_class_id':
                $arrProduct = $objProduct->getDetailAndProductsClass($arrRequest['ItemId']);
                break;
            case 'product_id':
            default:
                $arrProduct = $objProduct->getDetail($arrRequest['ItemId']);
                break;
            }

            $objProduct->setProductsClassByProductIds(array($arrProduct['product_id']));

            if ($arrProduct['del_flg'] == '0' && $arrProduct['status'] == '1') {
                unset($arrProduct['note']);
                $this->setResponse('product_id', $arrProduct['product_id']);
                $this->setResponse('DetailPageURL', HTTP_URL . 'products/detail.php?product_id=' . $arrProduct['product_id']);
                $this->setResponse('Title', $arrProduct['name']);
                $this->setResponse('ItemAttributes', $arrProduct);
                return true;
            } else {
                $this->addError('ItemLookup.Error', t('c_* The requested information was not found._01'));
            }
        }

        return false;
    }

    protected function checkErrorExtended($arrParam) {
        switch ($arrParam['IdType']) {
        case 'product_code':
            break;
        case 'product_id':
        case 'product_class_id':
        default:
            $objErr = new SC_CheckError_Ex($arrParam);
            $objErr->doFunc(array(t('c_Designated ID_01'), 'ItemId', INT_LEN), array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $this->addError($objErr->arrErr);
            break;
        }
    }

    protected function lfInitParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Product condition_01'), 'Condition', STEXT_LEN, 'a', array('ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Product ID type_01'), 'IdType', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Designated ID_01'), 'ItemId', STEXT_LEN, 'a', array('EXIST_CHECK', 'GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Related product quantity_01'), 'RelatedItemsPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Related product type_01'), 'RelationshipType', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Review page number_01'), 'ReviewPage', INT_LEN, 'N', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Review page sort_01'), 'ReviewSort', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Related tag pages_01'), 'TagPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Number of related tag pages_01'), 'TagsPerPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_ Sort related tags_01'), 'TagSort', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName() {
        return 'Item';
    }
}
