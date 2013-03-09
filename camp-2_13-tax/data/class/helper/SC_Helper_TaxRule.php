<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
 * 会員規約を管理するヘルパークラス.
 *
 * @package Helper
 * @author AMUAMU
 * @version $Id:$
 */
class SC_Helper_TaxRule
{

    /**
     * 税金情報に基づいて税金額を返す
     *
     * @param integer $price 計算対象の金額
     * @return integer 税金額
     */
    function sfTax($price, $product_id = 0, $product_class_id = 0, $pref_id =0, $country_id = 0)
    {
        // 条件に基づいて税情報を取得
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_tax_rule';
        $where = '(product_id = 0 OR product_id = ?)'
                    . ' AND (product_class_id = 0 OR product_class_id = ?)'
                    . ' AND (pref_id = 0 OR pref_id = ?)'
                    . ' AND (country_id = 0 OR country_id = ?)';

        $arrVal = array($product_id, $product_class_id, $pref_id, $country_id);
        $order = 'apply_date DESC';
        $objQuery->setOrder($order);
        $arrData = $objQuery->select('*', $table, $where, $arrVal);
        return $arrData[0]; //

    }

}
