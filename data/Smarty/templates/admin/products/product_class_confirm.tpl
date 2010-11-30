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
<h2>確認</h2>
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<!--{foreach key=key item=item from=$arrForm}-->
  <!--{if '/payment_ids:/'|preg_match:$key}-->
    <!--{foreach item=paymentsVal from=$item}-->
      <input type="hidden" name="<!--{$key}-->[]" value="<!--{$paymentsVal|escape}-->" />
    <!--{/foreach}-->
  <!--{else}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
  <!--{/if}-->
<!--{/foreach}-->

<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">

  <!--{if $tpl_check > 0}-->
  <table class="list">
    <!--{assign var=class_id1 value=$arrForm.class_id1}-->
    <!--{assign var=class_id2 value=$arrForm.class_id2}-->
    <tr>
      <th>規格1(<!--{$arrClass[$class_id1]|default:"未選択"}-->)</th>
      <th>規格2(<!--{$arrClass[$class_id2]|default:"未選択"}-->)</th>
      <th>商品コード</th>
      <th>在庫数</th>
      <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(円)</th>
      <th><!--{$smarty.const.SALE_PRICE_TITLE}-->(円)</th>
      <th>商品種別</th>
      <th>支払方法</th>
      <th>ダウンロードファイル名</th>
      <th>ダウンロード商品用ファイルアップロード</th>
    </tr>
    <!--{section name=cnt loop=$tpl_count}-->
    <!--{assign var=key value="check:`$smarty.section.cnt.iteration`"}-->
    <!--{if $arrForm[$key] == 1}-->
    <tr>
      <!--{assign var=key value="name1:`$smarty.section.cnt.iteration`"}-->
      <td><!--{$arrForm[$key]}--></td>
      <!--{assign var=key value="name2:`$smarty.section.cnt.iteration`"}-->
      <td><!--{$arrForm[$key]}--></td>
      <!--{assign var=key value="product_code:`$smarty.section.cnt.iteration`"}-->
      <td><!--{$arrForm[$key]}--></td>
      <!--{assign var=key1 value="stock:`$smarty.section.cnt.iteration`"}-->
      <!--{assign var=key2 value="stock_unlimited:`$smarty.section.cnt.iteration`"}-->
      <td class="right">
      <!--{if $arrForm[$key2] == 1}-->
        無制限
      <!--{else}-->
        <!--{$arrForm[$key1]}-->
      <!--{/if}-->
      </td>
      <!--{assign var=key value="price01:`$smarty.section.cnt.iteration`"}-->
      <td class="right"><!--{$arrForm[$key]}--></td>
      <!--{assign var=key value="price02:`$smarty.section.cnt.iteration`"}-->
      <td class="right"><!--{$arrForm[$key]}--></td>
      <!--{assign var=key value="product_type_id:`$smarty.section.cnt.iteration`"}-->
      <!--{assign var=inkey value="`$arrForm[$key]`"}-->
      <td class="right"><!--{$arrDown[$inkey]}--></td>
      <!--{assign var=key value="payment_ids:`$smarty.section.cnt.iteration`"}-->
      <td>
      <!--{foreach from=$arrForm[$key] item=payment_id}-->
        <!--{$arrPayments[$payment_id]|escape}-->&nbsp;
      <!--{/foreach}-->
      </td>
      <!--{assign var=key value="down_filename:`$smarty.section.cnt.iteration`"}-->
      <td class="right"><!--{$arrForm[$key]}--></td>
      <!--{assign var=key value="down_realfilename:`$smarty.section.cnt.iteration`"}-->
      <td class="right"><!--{$arrForm[$key]}--></td>
    </tr>
    <!--{/if}-->
    <!--{/section}-->
  </table>
  <!--{else}-->
  <div class="message">規格が選択されていません。</div>
  <!--{/if}-->

  <div class="btn">
    <button type="button" onclick="fnModeSubmit('confirm_return','',''); return false"><span>前へ戻る</span></button>
    <!--{if $tpl_check > 0}-->
    <button type="submit"><span>この内容で登録する</span></button>
    <!--{/if}-->
  </div>
</div>
</form>
