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
  <h2 class="title"><!--{$tpl_title|h}--></h2>
  <!--{include file=$tpl_navi}-->
  <form name="form1" method="post" action="?">
  <input type="hidden" name="mode" value="complete" />
  <div id="mycontentsarea">
    <h3><!--{$tpl_subtitle|h}--></h3>
    <div id="completetext">
      <p>退会手続きを実行してもよろしいでしょうか？</p>
      <div class="tblareabtn">
        <a href="./refusal.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_refuse_not_on.gif','refuse_not');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_refuse_not.gif','refuse_not');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_refuse_not.gif" width="180" height="30" alt="いいえ、退会しません" name="refuse_not" id="refuse_not" /></a>&nbsp;
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_refuse_do_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_refuse_do.gif',this);" src="<!--{$TPL_URLPATH}-->img/button/btn_refuse_do.gif" class="box180" alt="はい、退会します" name="refuse_do" id="refuse_do" />
      </div>

      <p class="mini"><em>※退会手続きが完了した時点で、現在保存されている購入履歴や、お届け先等の情報はすべてなくなりますのでご注意ください。</em></p>
    </div>
  </div>
  </form>
</div>
<!--▲CONTENTS-->
