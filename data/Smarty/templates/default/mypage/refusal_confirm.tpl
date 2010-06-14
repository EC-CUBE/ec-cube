<!--{*
/*
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
 */
*}-->
<!--▼CONTENTS-->
<div id="mypagecolumn">
  <h2 class="title"><img src="<!--{$TPL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ" /></h2>
  <!--{include file=$tpl_navi}-->
  <form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
  <input type="hidden" name="mode" value="complete" />
  <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
  <div id="mycontentsarea">
    <h3><img src="<!--{$TPL_DIR}-->img/mypage/subtitle04.gif" width="515" height="32" alt="退会手続き" /></h3>
    <div id="completetext">
      <p>退会手続きを実行してもよろしいでしょうか？</p>
      <div class="tblareabtn">
        <a href="./refusal.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/mypage/b_no_on.gif','refusal_no');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/mypage/b_no.gif','refusal_no');"><img src="<!--{$TPL_DIR}-->img/mypage/b_no.gif" width="180" height="30" alt="いいえ、退会しません" name="refusal_no" id="refusal_no" /></a>&nbsp;
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/mypage/b_yes_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/mypage/b_yes.gif',this);" src="<!--{$TPL_DIR}-->img/mypage/b_yes.gif" class="box180" alt="はい、はい、退会します" name="refusal_yes" id="refusal_yes" />
      </div>

      <p class="mini"><em>※退会手続きが完了した時点で、現在保存されている購入履歴や、お届け先等の情報はすべてなくなりますのでご注意ください。</em></p>
    </div>
  </div>
  </form>
</div>
<!--▲CONTENTS-->
