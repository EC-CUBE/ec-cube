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
<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="お客様の声書き込み（完了ページ）"}-->

  <div id="windowarea">
    <h2><img src="<!--{$TPL_DIR}-->img/products/review_title.jpg" width="500" height="40" alt="お客様の声書き込み" /></h2>
    <div id="completebox">
      <p>登録が完了しました。ご利用ありがとうございました。<br />
       弊社にて登録内容を確認後、ホームページに反映させていただきます。<br />
       今しばらくお待ちくださいませ。</p>
    </div>
    <div class="btn">
      <a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_close_on.gif','b_close');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_close.gif','b_close');"><img src="<!--{$TPL_DIR}-->img/common/b_close.gif" width="150" height="30" alt="閉じる" border="0" name="b_close" /></a>
    </div>
  </div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
