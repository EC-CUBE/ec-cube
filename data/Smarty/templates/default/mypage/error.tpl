<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="エラー"}-->

<div id="compbox">
<span class="red"><!--{$tpl_error}--></span><br />
</div>

<div class="button">
<a href="javascript:window.close()" onmouseOver="chgImg('<!--{$TPL_DIR}-->img/button/close_on.jpg','close');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/close.jpg','close');"><img src="<!--{$TPL_DIR}-->img/button/close.jpg" width="129" height="32" alt="閉じる" name="close" id="close" /></a>
</div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
