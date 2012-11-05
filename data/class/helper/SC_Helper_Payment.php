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
 * 支払方法を管理するヘルパークラス.
 *
 * @package Helper
 * @author pineray
 * @version $Id:$
 */
class SC_Helper_Payment
{
    /**
     * 決済モジュールを使用するかどうか.
     *
     * dtb_payment.memo03 に値が入っている場合は決済モジュールと見なす.
     *
     * @param integer $payment_id 支払い方法ID
     * @return boolean 決済モジュールを使用する支払い方法の場合 true
     */
    public static function useModule($payment_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $memo03 = $objQuery->get('memo03', 'dtb_payment', 'payment_id = ?', array($payment_id));
        return !SC_Utils_Ex::isBlank($memo03);
    }
}
