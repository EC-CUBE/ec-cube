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
<div id="undercolumn">
  <div id="undercolumn_entry">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <p><em>【重要】 会員登録をされる前に、下記ご利用規約をよくお読みください。</em><br />
      規約には、本サービスを使用するに当たってのあなたの権利と義務が規定されております。<br />
     「規約に同意して会員登録」ボタン をクリックすると、あなたが本規約の全ての条件に同意したことになります。</p>
    <form name="form1" id="form1" method="post" action="?">
       <textarea name="textfield" class="area470"  cols="80" rows="30"
                 readonly="readonly"><!--{$tpl_kiyaku_text}--></textarea>
       <div class="tblareabtn">
         <a href="<!--{$smarty.const.TOP_URLPATH}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/button/btn_entry_cannot_on.gif','b_noagree');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/btn_entry_cannot.gif','b_noagree');">
           <img src="<!--{$TPL_DIR}-->img/button/btn_entry_cannot.gif" width="180" height="30" alt="同意しない" border="0" name="b_noagree" /></a>&nbsp;
         <a href="<!--{$smarty.const.ENTRY_URL}-->" onmouseover="chgImg('<!--{$TPL_DIR}-->img/button/btn_entry_agree_on.gif','b_agree');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/btn_entry_agree.gif','b_agree');">
           <img src="<!--{$TPL_DIR}-->img/button/btn_entry_agree.gif" width="180" height="30" alt="規約に同意して会員登録" border="0" name="b_agree" /></a>
       </div>
     </form>
   </div>
 </div>
