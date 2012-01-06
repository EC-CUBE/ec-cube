<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
// {{{ requires
require_once CLASS_REALDIR . 'module/SC_Module.php';

/**
 * 決済モジュール用のモジュールデータ管理クラス.
 * 各モジュールに固有のデータへのアクセスを担当する.
 *
 *
 * @package module
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Module_Payment extends SC_Module {
    /** 支払方法 */
    var $paymethod = array();
}
/*
 * Local variables:
 * coding: utf-8
 * End:
 */
?>
