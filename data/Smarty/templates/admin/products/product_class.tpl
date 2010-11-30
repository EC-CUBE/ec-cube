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
<h2>商品規格登録</h2>
<form name="form1" id="form1" method="post" action="" enctype="multipart/form-data">
<!--{foreach key=key item=item from=$arrSearchHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/foreach}-->
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />
<input type="hidden" name="down_key" value="">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">

  <table>
    <tr>
      <th>商品名</th>
      <td><!--{$arrForm.product_name|escape}--></td>
    </tr>
    <tr>
      <th>規格1<span class="attention">*</span></th>
      <td>
        <!--{if $arrErr.select_class_id1}-->
        <span class="attention"><!--{$arrErr.select_class_id1}--></span>
        <!--{/if}-->
        <select name="select_class_id1">
          <option value="">選択してください</option>
          <!--{html_options options=$arrClass selected=$arrForm.select_class_id1}-->
        </select>
      </td>
    </tr>
    <tr>
      <th>規格2</th>
      <td>
        <!--{if $arrErr.select_class_id2}-->
        <span class="attention"><!--{$arrErr.select_class_id2}--></span>
        <!--{/if}-->
        <select name="select_class_id2">
          <option value="">選択してください</option>
          <!--{html_options options=$arrClass selected=$arrForm.select_class_id2}-->
        </select>
      </td>
    </tr>
  </table>
  <div class="btn">
    <button type="button" onclick="fnChangeAction('<!--{$smarty.const.URL_SEARCH_TOP}-->'); fnModeSubmit('search','',''); return false;" ><span>検索結果へ戻る</span></button>
    <button type="button" onclick="fnModeSubmit('disp','','')"><span>表示する</span></button>
    <!--{if count($arrClassCat) > 0}-->
    <button type="button" onclick="fnModeSubmit('delete','','');"><span>削除する</span></button>
    <!--{/if}-->
  </div>

  <!--{if count($arrClassCat) > 0}-->

  <!--{foreach item=item name=i from=$arrClassCat}-->
    <!--{if $smarty.foreach.i.first}-->
      <!--{assign var=cnt value=$smarty.foreach.i.total}-->
    <!--{/if}-->
  <!--{/foreach}-->

  <div class="btn">
    <button type="button" onclick="fnAllCheck(); return false;"><span>全選択</span></button>
    <button type="button" onclick="fnAllUnCheck(); return false;"><span>全解除</span></button>
    <button type="button" onclick="fnCopyValue('<!--{$cnt}-->', '<!--{$smarty.const.DISABLED_RGB}-->'); return false;"><span>一行目のデータをコピーする</span></button>
  </div>

  <!--{assign var=class_id1 value=$arrForm.class_id1}-->
  <!--{assign var=class_id2 value=$arrForm.class_id2}-->
  <input type="hidden" name="class_id1" value="<!--{$class_id1}-->" />
  <input type="hidden" name="class_id2" value="<!--{$class_id2}-->" />
  <table class="list">
    <tr>
      <th>登録</th>
      <th>規格1(<!--{$arrClass[$class_id1]|default:"未選択"}-->)</th>
      <th>規格2(<!--{$arrClass[$class_id2]|default:"未選択"}-->)</th>
      <th>商品コード</th>
      <th>在庫数<span class="attention">*</span></th>
      <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(円)</th>
      <th><!--{$smarty.const.SALE_PRICE_TITLE}-->(円)<span class="attention">*</span></th>
      <th>商品種別<span class="attention"> *</span></th>
      <th>支払方法<span class="attention"> *</span></th>
      <th>ダウンロードファイル名<BR><span class="red"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span></th>
      <th>ダウンロード商品用ファイルアップロード<BR>登録可能拡張子：<!--{$smarty.const.DOWNLOAD_EXTENSION}-->　(パラメータ DOWNLOAD_EXTENSION)</th>
    </tr>
    <!--{section name=cnt loop=$arrClassCat}-->
    <!--{assign var=key value="error:`$smarty.section.cnt.iteration`"}-->
    <!--{if $arrErr[$key] != ""}-->
    <tr>
      <td colspan="8"><span class="attention"><!--{$arrErr[$key]}--></span></td>
    </tr>
    <!--{/if}-->
    <tr >
      <!--{assign var=key value="check:`$smarty.section.cnt.iteration`"}-->
      <td align="center">
				<!--{assign var=id value="product_class_id:`$smarty.section.cnt.iteration`"}-->
        <input type="hidden" name="classcategory_id1:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].classcategory_id1}-->" />
        <input type="hidden" name="classcategory_id2:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].classcategory_id2}-->" />
        <input type="hidden" name="name1:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].name1}-->" />
        <input type="hidden" name="name2:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].name2}-->" />
        <input type="hidden" name="product_class_id:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrForm[$id]}-->" />
        <input type="checkbox" name="check:<!--{$smarty.section.cnt.iteration}-->" value="1" <!--{if $arrForm[$key] == 1}-->checked="checked"<!--{/if}-->>
      </td>
      <td><!--{$arrClassCat[cnt].name1}--></td>
      <td><!--{$arrClassCat[cnt].name2}--></td>
      <!--{assign var=key value="product_code:`$smarty.section.cnt.iteration`"}-->
      <td align="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.STEXT_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
      <!--{assign var=key value="stock:`$smarty.section.cnt.iteration`"}-->
      <!--{assign var=chkkey value="stock_unlimited:`$smarty.section.cnt.iteration`"}-->
      <td align="center">
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
        <!--{assign var=key value="stock_unlimited:`$smarty.section.cnt.iteration`"}-->
        <input type="checkbox" name="<!--{$key}-->" value="1" <!--{if $arrForm[$key] == "1"}-->checked<!--{/if}--> onClick="fnCheckStockNoLimit('<!--{$smarty.section.cnt.iteration}-->','<!--{$smarty.const.DISABLED_RGB}-->');"/>無制限
      </td>
      <!--{assign var=key value="price01:`$smarty.section.cnt.iteration`"}-->
      <td align="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
      <!--{assign var=key value="price02:`$smarty.section.cnt.iteration`"}-->
      <td align="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
      <td>
        <!--{assign var=key value="product_type_id:`$smarty.section.cnt.iteration`"}-->
        <input type="radio" name="<!--{$key}-->" value="<!--{$smarty.const.PRODUCT_TYPE_NORMAL}-->" <!--{if $arrForm[$key] == "1"}-->checked<!--{/if}-->/>通常商品　
        <input type="radio" name="<!--{$key}-->" value="<!--{$smarty.const.PRODUCT_TYPE_DOWNLOAD}-->" <!--{if $arrForm[$key] == "2"}-->checked<!--{/if}--> />ダウンロード商品
      </td>
      <td>
        <!--{assign var=key value="payment_ids:`$smarty.section.cnt.iteration`"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <!--{html_checkboxes name=$key options=$arrPayments selected=$arrForm[$key]}-->
      </td>
      <td>
        <!--{assign var=key value="down_filename:`$smarty.section.cnt.iteration`"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="30" class="box30" />
      </td>
      <!--{assign var=key value="down_realfilename:`$smarty.section.cnt.iteration`"}-->
      <td>
        <span class="attention"><!--{$arrErr[$key]}--></span>
          <!--{if $arrForm[$key] != ""}-->
            <!--{$arrForm[$key]|escape}--><input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key]|escape}-->">
            <a href="" onclick="fnModeSubmit('delete_down', 'down_key', '<!--{$key}-->'); return false;">[ファイルの取り消し]</a><br>
          <!--{/if}-->
          <input type="file" name="<!--{$key}-->" size="50" class="box50" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
          <input type="button" name="btn" onclick="fnModeSubmit('upload_down', 'down_key', '<!--{$key}-->')" value="アップロード">
      </td>
    </tr>
    <!--{/section}-->
  </table>

  <div class="btn"><button type="submit"><span>確認ページへ</span></button></div>

  <!--{/if}-->

</div>
</form>
