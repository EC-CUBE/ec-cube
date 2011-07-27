<!--{*
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
 *}-->
<!--{*<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(完了ページ)"}-->*}-->

  <section id="windowcolumn">
  <div data-role="header">
    <div class="title_box clearfix">
      <h2>パスワードを忘れた方</h2><a href="#" data-role="button" data-rel="back" data-icon="delete" data-iconpos="notext" class="ui-btn-right" data-theme="d"><span class="ui-btn-text">close</span></a>
       </div>
        </div>
   <div class="intro">
     <p>パスワードの発行が完了いたしました。ログインには下記のパスワードをご利用ください。</p>
      </div>
    <form action="?" method="post" name="form1">
         <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

     <div class="window_area clearfix">
       <!--{if $smarty.const.FORGOT_MAIL != 1}-->
      <p id="completebox"><!--{$arrForm.new_password}--></p>
       <!--{else}-->
          <p  class="attention">ご登録メールアドレスに送付致しました。</p>
       <!--{/if}-->
         <hr />
       <p>※パスワードは、MYページの「会員登録内容変更」よりご変更いただけます。</p>
     </div>

     <div class="btn_area">
      <p><a href="javascript:window.close()" class="btn_sub btn_close">閉じる</a></p>
       </div>
     </form>    
  </section>

<!--{*<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->*}-->
