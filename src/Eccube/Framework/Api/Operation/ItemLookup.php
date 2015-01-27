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

namespace Eccube\Framework\Api\Operation;

use Eccube\Application;
use Eccube\Framework\CheckError;
use Eccube\Framework\Product;

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */
class ItemLookup extends Base
{
    protected $operation_name = 'ItemLookup';
    protected $operation_description = '商品詳細情報を取得します。';
    protected $default_auth_types = self::API_AUTH_TYPE_OPEN;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam)
    {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {
            /* @var $objProduct Product */
            $objProduct = Application::alias('eccube.product');

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

    protected function checkErrorExtended($arrParam)
    {
        switch ($arrParam['IdType']) {
        case 'product_code':
            break;
        case 'product_id':
        case 'product_class_id':
        default:
            /* @var $objErr CheckError */
            $objErr = Application::alias('eccube.check_error', $arrParam);
            $objErr->doFunc(array('指定ID', 'ItemId', INT_LEN), array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $this->addError($objErr->arrErr);
            break;
        }
    }

    protected function lfInitParam(&$objFormParam)
    {
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

    public function getResponseGroupName()
    {
        return 'Item';
    }
}
