<!--{*
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
 *}-->
<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="パスワードを忘れた方(完了ページ)"}-->

  <div id="windowarea">
    <h2><img src="<!--{$TPL_DIR}-->img/forget/title.jpg" width="500" height="40" alt="パスワードを忘れた方" /></h2>
    <p>パスワードの発行が完了いたしました。ログインには下記のパスワードをご利用ください。<br />
    ※下記パスワードは、MYページの「会員登録内容変更」よりご変更いただけます。</p>
    <form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post" name="form1">
      <div id="completebox">
        <p><em><!--{$temp_password}--></em></p>
      </div>
      <div class="btn">
        <a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_close_on.gif','close');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_close.gif','close');"><img src="<!--{$TPL_DIR}-->img/common/b_close.gif" width="150" height="30" alt="閉じる" name="close" id="close" /></a>
      </div>
    </form>
  </div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
