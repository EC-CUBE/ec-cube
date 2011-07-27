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
<!--▼CONTENTS-->
<section id="mypagecolumn">
        <h2 class="title"><!--{$tpl_title|h}--></h2>
        <!--{include file=$tpl_navi}-->

        <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>
        <!--★インフォメーション★-->
          <div class="information">
             <p><span class="attention">※</span>は必須入力項目です。<p>
             <p>最大<span class="attention"><!--{$smarty.const.DELIV_ADDR_MAX|h}-->件</span>まで登録できます。</p>  
          </div>
        <!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
        <!--{* 退会時非表示 *}-->
        <!--{if $tpl_login}-->

        <!--★ボタン★-->
          <div class="btn_area_top">
              <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" class="btn_sub addbtn" rel="external">新しいお届け先を追加</a>
          </div>
        <!--{/if}-->
        <!--{/if}-->

    <div class="form_area">
        <!--{if $tpl_linemax > 0}-->
           <form name="form1" method="post" action="?" >
               <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
               <input type="hidden" name="mode" value="" />
               <input type="hidden" name="other_deliv_id" value="" />
               <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />

       <!--▼フォームボックスここから -->
       <div class="formBox">

             <!--▼お届け先 -->
       <div class="delivBox">
                   <!--{section name=cnt loop=$arrOtherDeliv}-->
          <!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}-->
             <p><em>お届け先住所<!--{$smarty.section.cnt.iteration}--></em>：<br />
                                〒<!--{$arrOtherDeliv[cnt].zip01}-->-<!--{$arrOtherDeliv[cnt].zip02}--><br />
                      <!--{$arrPref[$OtherPref]|h}--><!--{$arrOtherDeliv[cnt].addr01|h}--><!--{$arrOtherDeliv[cnt].addr02|h}--><br />
                      <!--{$arrOtherDeliv[cnt].name01|h}-->&nbsp;<!--{$arrOtherDeliv[cnt].name02|h}--></p>

            <ul class="edit">
              <li><a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','600','640'); return false;" class="b_edit" rel="external">編集</a></li>
              <li><a href="#" onclick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->'); return false;" rel="external">削除</a></li>
            </ul>
          </div>
       <!--▲お届け先-->
         <!--{/section}-->

      </div><!--▲formBox -->
          </form>
      <!--{else}-->
          <p class="delivempty"><strong>新しいお届け先はありません。</strong></p>
      <!--{/if}-->

          <p><a href="#" class="btn_more" rel="external">もっとみる</a></p>

   </div><!--▲form_area -->

</section>
<!--▲CONTENTS -->
