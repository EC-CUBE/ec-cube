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
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />
<input type="hidden" name="down_key" value="">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">

  <table>
    <tr>
      <th>商品名</th>
      <td><!--{$arrForm.product_name|h}--></td>
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
  <div class="btn-area">
    <ul>
      <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;" ><span class="btn-prev">検索結果へ戻る</span></a></li>
      <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('disp','',''); return false;"><span class="btn-next">表示する</span></a></li>
    <!--{if count($arrClassCat) > 0}-->
      <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('delete','',''); return false;"><span class="btn-next">削除する</span></a></li>
    <!--{/if}-->
    </ul>
  </div>

  <!--{if count($arrClassCat) > 0}-->

  <!--{foreach item=item name=i from=$arrClassCat}-->
    <!--{if $smarty.foreach.i.first}-->
      <!--{assign var=cnt value=$smarty.foreach.i.total}-->
    <!--{/if}-->
  <!--{/foreach}-->

  <div class="list-info clearfix">
    <div class="btn"><a class="btn-normal" href="javascript:;" onclick="fnCopyValue('<!--{$cnt}-->', '<!--{$smarty.const.DISABLED_RGB}-->'); return false;"><span>一行目のデータをコピーする</span></a></div>
    <p><span class="bold">アップロード可能な拡張子：</span><!--{$smarty.const.DOWNLOAD_EXTENSION}-->(パラメータ DOWNLOAD_EXTENSION)</p>
  </div>

  <!--{assign var=class_id1 value=$arrForm.class_id1}-->
  <!--{assign var=class_id2 value=$arrForm.class_id2}-->
  <input type="hidden" name="class_id1" value="<!--{$class_id1}-->" />
  <input type="hidden" name="class_id2" value="<!--{$class_id2}-->" />
  <table class="list" width="900">
    <colgroup width="5%">
    <colgroup width="9%">
    <colgroup width="9%">
    <colgroup width="9%">
    <colgroup width="10%">
    <colgroup width="10%">
    <colgroup width="10%">
    <colgroup width="10%">
    <colgroup width="8%">
    <colgroup width="8%">
    <colgroup width="8%">
    <tr>
      <th><input type="checkbox" onclick="fnAllCheck(this, 'input[name^=check]')" id="allCheck" /> <label for="allCheck"><br>登録</label></th>
      <th>規格1<br>(<!--{$arrClass[$class_id1]|default:"未選択"}-->)</th>
      <th>規格2<br>(<!--{$arrClass[$class_id2]|default:"未選択"}-->)</th>
      <th>商品コード</th>
      <th>在庫数<span class="attention">*</span></th>
      <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(円)</th>
      <th><!--{$smarty.const.SALE_PRICE_TITLE}-->(円)<span class="attention">*</span></th>
      <th>商品種別<span class="attention">*</span></th>
      <th>ダウンロード<br>ファイル名<span class="red"><br>上限<!--{$smarty.const.STEXT_LEN}-->文字</span></th>
      <th>ダウンロード商品用<br>ファイル</th>
    </tr>
    <!--{section name=cnt loop=$arrClassCat}-->
    <!--{assign var=key value="error:`$smarty.section.cnt.iteration`"}-->
    <!--{if $arrErr[$key] != ""}-->
    <tr>
      <td colspan="10"><span class="attention"><!--{$arrErr[$key]}--></span></td>
    </tr>
    <!--{/if}-->
    <tr >
      <!--{assign var=key value="check:`$smarty.section.cnt.iteration`"}-->
      <td class="center" >
				<!--{assign var=id value="product_class_id:`$smarty.section.cnt.iteration`"}-->
        <input type="hidden" name="classcategory_id1:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].classcategory_id1}-->" />
        <input type="hidden" name="classcategory_id2:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].classcategory_id2}-->" />
        <input type="hidden" name="name1:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].name1}-->" />
        <input type="hidden" name="name2:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrClassCat[cnt].name2}-->" />
        <input type="hidden" name="product_class_id:<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrForm[$id]}-->" />
        <input type="checkbox" name="check:<!--{$smarty.section.cnt.iteration}-->" value="1" <!--{if $arrForm[$key] == 1}-->checked="checked"<!--{/if}-->>
      </td>
      <td class="center"><!--{$arrClassCat[cnt].name1}--></td>
      <td class="center"><!--{$arrClassCat[cnt].name2}--></td>
      <!--{assign var=key value="product_code:`$smarty.section.cnt.iteration`"}-->
      <td class="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.STEXT_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
      <!--{assign var=key value="stock:`$smarty.section.cnt.iteration`"}-->
      <!--{assign var=chkkey value="stock_unlimited:`$smarty.section.cnt.iteration`"}-->
      <td class="center">
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
        <!--{assign var=key value="stock_unlimited:`$smarty.section.cnt.iteration`"}--><br />
        <input type="checkbox" name="<!--{$key}-->" value="1" <!--{if $arrForm[$key] == "1"}-->checked<!--{/if}--> onClick="fnCheckStockNoLimit('<!--{$smarty.section.cnt.iteration}-->','<!--{$smarty.const.DISABLED_RGB}-->');"/>無制限
      </td>
      <!--{assign var=key value="price01:`$smarty.section.cnt.iteration`"}-->
      <td class="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
      <!--{assign var=key value="price02:`$smarty.section.cnt.iteration`"}-->
      <td class="center"><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
      <td class="class-product-type">
        <!--{assign var=key value="product_type_id:`$smarty.section.cnt.iteration`"}-->
        <!--{html_radios name=$key options=$arrProductType selected=$arrForm[$key] separator='<br />'}-->
      </td>
      <td class="center">
        <!--{assign var=key value="down_filename:`$smarty.section.cnt.iteration`"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="10" />
      </td>
      <!--{assign var=key value="down_realfilename:`$smarty.section.cnt.iteration`"}-->
      <td><span class="attention"><!--{$arrErr[$key]}--></span>
          <!--{if $arrForm[$key] != ""}-->
            <!--{$arrForm[$key]|h}--><input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->">
            <a href="" onclick="fnFormModeSubmit('form1', 'delete_down', 'down_key', '<!--{$key}-->'); return false;">[ファイルの取り消し]</a><br>
          <!--{/if}-->
          <input type="file" name="<!--{$key}-->" size="10" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
          <a class="btn-normal" href="javascript:;" name="btn" onclick="fnFormModeSubmit('form1', 'upload_down', 'down_key', '<!--{$key}-->'); return false;">アップロード</a>
      </td>
    </tr>
    <!--{/section}-->
  </table>

  <div class="btn-area">
    <ul>
      <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">確認ページへ</span></a></li>
    </ul>
  </div>

  <!--{/if}-->

</div>
</form>
