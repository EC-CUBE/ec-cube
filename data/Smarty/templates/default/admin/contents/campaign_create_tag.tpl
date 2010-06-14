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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_header.tpl"}-->

<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input name="mode" type="hidden" value="search" />
<input name="anchor_key" type="hidden" value="" />
<input name="search_pageno" type="hidden" value="" />
<input type="hidden" name="product_id" value="" />

<!--{if $tpl_create_tag != ""}-->
<h2>生成タグ</h2>
<textarea name="" cols="45" rows="18" class="area40" readonly><!--{$tpl_create_tag}--></textarea>
<div class="btn"><button type="button" onClick="history.back()"><span>検索結果へ戻る</span></button></div>

<!--{* タグ表示の時以下を表示しない *}-->
<!--{else}-->
<table class="form">
  <tr>
    <th>カテゴリ</th>
    <td>
      <select name="search_category_id">
        <option value="" selected="selected">選択してください</option>
        <!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
      </select>
    </td>
  </tr>
  <tr>
    <th>商品ID</th>
    <td><input type="text" name="search_product_id" value="<!--{$arrForm.search_product_id}-->" size="35" class="box35" /></td>
  </tr>
  <tr>
    <th>商品名</th>
    <td><input type="text" name="search_name" value="<!--{$arrForm.search_name}-->" size="35" class="box35" /></td>
  </tr>
</table>
<div class="btn"><button type="submit"><span>検索を開始</span></button></div>

<!--{* ▼検索結果表示 *}-->
<!--{if $tpl_linemax}-->
<p><!--{$tpl_linemax}-->件が該当しました。</p>
<!--{$tpl_strnavi}-->
    
<table class="list">
  <tr>
    <th>商品画像</th>
    <th>商品ID</th>
    <th>商品名</th>
    <th>決定</th>
  </tr>
  <!--{section name=cnt loop=$arrProducts}-->
  <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
  <tr>
    <td class="center">
      <img src="<!--{$smarty.const.URL_DIR}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|escape}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->" />
    </td>  
    <td><!--{$arrProducts[cnt].product_code|escape|default:"-"}--></td>
    <td><!--{$arrProducts[cnt].name|escape}--></td>
    <td class="center"><a href="#" onClick="fnFormModeSubmit('form1', 'view', 'product_id', '<!--{$arrProducts[cnt].product_id}-->'); return false;">決定</a></td>
  </tr>
  <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
  <!--{sectionelse}-->
  <tr>
    <td colspan="4">商品が登録されていません</td>
  </tr>  
  <!--{/section}-->
</table>
<!--{/if}-->
<!--{* ▲検索結果表示 *}-->

<!--{/if}-->
<!--{* タグ表示しないここまで *}-->

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_footer.tpl"}-->
