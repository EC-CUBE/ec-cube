<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
*}-->
<body bgcolor="#ffffff" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af">
<!--{* Moba8リクエスト用 *}-->
<!--{if "sfRequestMoba8"|function_exists === TRUE}-->
<!--{include file=`$smarty.const.MODULE_PATH`mdl_moba8/request_moba8.tpl}-->
<!--{/if}-->

<!--▼MAIN-->
<!--{include file=$tpl_mainpage}-->
<!--▲MAIN-->
</body>
