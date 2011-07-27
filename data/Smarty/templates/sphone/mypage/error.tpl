<!--{*
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
*}-->
<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="エラー"}-->

<div data-role="page" data-theme="d" id="index frame_outer">
  <div id="errorBox">
      <p class="attention"><!--{$tpl_error}--></p>
  </div>

  <div class="btn_area">
      <p><a href="javascript:window.close()" onmouseOver="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close_on.gif','close');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close.gif','close');" class="btn_sub">閉じる</a></p>
  </div>
</div>

<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->
