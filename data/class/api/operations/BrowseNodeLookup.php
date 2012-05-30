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

class API_BrowseNodeLookup extends SC_Api_Abstract_Ex {

    protected $operation_name = 'BrowseNodeLookup';
    protected $operation_description = 'カテゴリ取得';
    protected $default_auth_types = self::API_AUTH_TYPE_OPEN;
    protected $default_enable = '1';
    protected $default_is_log = '0';
    protected $default_sub_data = '';

    public function doAction($arrParam) {
        $arrRequest = $this->doInitParam($arrParam);
        if (!$this->isParamError()) {
            $category_id = $arrRequest['BrowseNodeId'];
            if ($category_id
                 && !SC_Helper_DB_Ex::sfIsRecord('dtb_category', 'category_id', (array)$category_id, 'del_flg = 0')) {
                $category_id = '0';
            } else if (SC_Utils_Ex::isBlank($category_id)) {
                $category_id = '0';
            }
            // LC_Page_Products_CategoryList::lfGetCategories() と相当類似しているので共通化したい
            $arrCategory = null;    // 選択されたカテゴリ
            $arrChildren = array(); // 子カテゴリ

            $arrAll = SC_Helper_DB_Ex::sfGetCatTree($category_id, true);
            foreach ($arrAll as $category) {
                if ($category_id != 0 && $category['category_id'] == $category_id) {
                    $arrCategory = $category;
                    continue;
                }
                if ($category['parent_category_id'] != $category_id) {
                    continue;
                }

                $arrGrandchildrenID = SC_Utils_Ex::sfGetUnderChildrenArray($arrAll, 'parent_category_id', 'category_id', $category['category_id']);
                $category['has_children'] = count($arrGrandchildrenID) > 0;
                $arrChildren[] = $category;
            }

            if (!SC_Utils_Ex::isBlank($arrCategory)) {
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

            if (!SC_Utils_Ex::isBlank($arrChildren)) {
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

    protected function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('対象カテゴリID', 'BrowseNodeId', INT_LEN, 'a', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('返答種別', 'ResponseGroup', INT_LEN, 'a', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
    }

    public function getResponseGroupName() {
        return 'BrowseNodes';
    }
}
