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
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Util\Utils;

/**
 * APIの基本クラス
 *
 * @package Api
 * @author LOCKON CO.,LTD.
 */
class BrowseNodeLookup extends Base
{
    protected $operation_name = 'BrowseNodeLookup';
    protected $operation_description = 'カテゴリ取得';
    protected $default_auth_types = self::API_AUTH_TYPE_OPEN;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam)
    {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {
            $category_id = $arrRequest['BrowseNodeId'];
            if ($category_id
                 && !Application::alias('eccube.helper.db')->isRecord('dtb_category', 'category_id', (array) $category_id, 'del_flg = 0')) {
                $category_id = '0';
            } elseif (Utils::isBlank($category_id)) {
                $category_id = '0';
            }
            // LC_Page_Products_CategoryList::lfGetCategories() と相当類似しているので共通化したい
            $arrCategory = null;    // 選択されたカテゴリ
            $arrChildren = array(); // 子カテゴリ

            $arrAll = Application::alias('eccube.helper.db')->getCatTree($category_id, true);
            foreach ($arrAll as $category) {
                if ($category_id != 0 && $category['category_id'] == $category_id) {
                    $arrCategory = $category;
                    continue;
                }
                if ($category['parent_category_id'] != $category_id) {
                    continue;
                }

                $arrGrandchildrenID = Utils::sfGetUnderChildrenArray($arrAll, 'parent_category_id', 'category_id', $category['category_id']);
                $category['has_children'] = count($arrGrandchildrenID) > 0;
                $arrChildren[] = $category;
            }

            if (!Utils::isBlank($arrCategory)) {
                $arrData = array(
                    'BrowseNodeId' => $category_id,
                    'Name' => $arrCategory['category_name'],
                    'PageURL' =>  HTTP_URL . 'products/list.php?category_id=' . $arr['category_id'],
                    'has_children' => count($arrChildren) > 0
                );
            } else {
                $arrData = array(
                    'BrowseNodeId' => $category_id,
                    'Name' => 'ホーム',
                    'PageURL' =>  HTTP_URL,
                    'has_children' => count($arrChildren) > 0
                );
            }

            if (!Utils::isBlank($arrChildren)) {
                $arrData['Children'] = array();
                foreach ($arrChildren as $category) {
                    $arrData['Children']['BrowseNode'][] = array(
                        'BrowseNodeId' => $category['category_id'],
                        'Name' => $category['category_name'],
                        'PageURL' => HTTP_URL . 'products/list.php?category_id=' . $category['category_id'],
                        'has_children' => $category['has_children']
                        );
                }
            }
            $this->setResponse('BrowseNode', $arrData);

            // TODO: Ancestors 親ノード
            return true;
        }

        return false;
    }

    protected function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('対象カテゴリID', 'BrowseNodeId', INT_LEN, 'a', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('返答種別', 'ResponseGroup', INT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName()
    {
        return 'BrowseNodes';
    }
}
