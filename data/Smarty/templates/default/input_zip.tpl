<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
 *}-->
<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="住所検索" disable_wincol=1}-->

  <div id="zipsearchcolumn">
    <h2><img src="<!--{$TPL_DIR}-->img/common/zip_title.jpg" width="460" height="40" alt="住所検索" /></h2>
    <div id="zipsearcharea">
      <form name="form1" id="form1" method="post" action="<!--{$smarty.const.PHP_SELF|escape}-->">
        <input type="hidden" name="state" value="<!--{$tpl_state}-->" />
        <input type="hidden" name="city" value="<!--{$tpl_city}-->" />
        <input type="hidden" name="town" value="<!--{$tpl_town}-->" />
        <div id="completebox">
          <p><!--{$tpl_message}--></p>
        </div>
      </form>
    </div>
    <div class="btn"><a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_close_on.gif','b_close');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_close.gif','b_close');"><img src="<!--{$TPL_DIR}-->img/common/b_close.gif" width="140" height="30" alt="閉じる" border="0" name="b_close" /></a></div>
  </div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
