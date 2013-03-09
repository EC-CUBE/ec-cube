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

// {{{ requires
require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/ownersstore/LC_Page_Admin_OwnersStore_PluginHookPointList_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Admin_OwnersStore_PluginHookPointList_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
