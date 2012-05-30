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
    protected $operation_description = '商品詳細情報を取得します。';
    protected $default_auth_types = self::API_AUTH_TYPE_OPEN;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

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
                $this->addError('ItemLookup.Error', '※ 要求された情報は見つかりませんでした。');
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
            $objErr->doFunc(array('指定ID', 'ItemId', INT_LEN), array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $this->addError($objErr->arrErr);
            break;
        }
    }

    protected function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('商品コンディション', 'Condition', STEXT_LEN, 'a', array('ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ID種別', 'IdType', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('指定ID', 'ItemId', STEXT_LEN, 'a', array('EXIST_CHECK', 'GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('関連商品数', 'RelatedItemsPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('関連商品種別', 'RelationshipType', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('レビューページ番号', 'ReviewPage', INT_LEN, 'N', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('レビューページソート', 'ReviewSort', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('関連タグページ', 'TagPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('関連タグページ数', 'TagsPerPage', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('関連タグソート', 'TagSort', STEXT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName() {
        return 'Item';
    }
}
