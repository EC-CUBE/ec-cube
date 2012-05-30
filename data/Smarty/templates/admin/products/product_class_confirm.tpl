<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
<input type="hidden" name="mode" value="" />
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=items from=$arrForm}-->
    <!--{if !array_key_exists($key, $arrSearchHidden)}-->
        <!--{if is_array($items.value)}-->
            <!--{foreach key=index item=item from=$items.value}-->
                <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$items.value|h}-->" />
        <!--{/if}-->
    <!--{/if}-->
<!--{/foreach}-->

<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->

<div id="products" class="contents-main">

    <!--{if count($arrForm.check.value) > 0}-->
        <!--{assign var=key1 value="class_id1"}-->
        <!--{assign var=key2 value="class_id2"}-->
        <!--{assign var=class_id1 value=$arrForm[$key1].value|h}-->
        <!--{assign var=class_id2 value=$arrForm[$key2].value|h}-->
        <table class="list">
                <tr>
                <th>規格1(<!--{$arrClass[$class_id1]|default:"未選択"|h}-->)</th>
                <th>規格2(<!--{$arrClass[$class_id2]|default:"未選択"|h}-->)</th>
                <th>商品コード</th>
                <th>在庫数</th>
                <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(円)</th>
                <th><!--{$smarty.const.SALE_PRICE_TITLE}-->(円)</th>
                <th>商品種別</th>
                <th>ダウンロードファイル名</th>
                <th>ダウンロード商品用ファイルアップロード</th>
            </tr>
            <!--{section name=cnt loop=$arrForm.total.value}-->
                <!--{assign var=index value=$smarty.section.cnt.index}-->

                <!--{if $arrForm.check.value[$index] == 1}-->
                    <tr>
                        <!--{assign var=key value="classcategory_name1"}-->
                        <td><!--{$arrForm[$key].value[$index]|h}--></td>
                        <!--{assign var=key value="classcategory_name2"}-->
                        <td><!--{$arrForm[$key].value[$index]|h}--></td>
                        <!--{assign var=key value="product_code"}-->
                        <td><!--{$arrForm[$key].value[$index]|h}--></td>
                        <!--{assign var=key1 value="stock"}-->
                        <!--{assign var=key2 value="stock_unlimited"}-->
                        <td class="right">
                            <!--{if $arrForm[$key2].value[$index] == 1}-->
                                無制限
                            <!--{else}-->
                                <!--{$arrForm[$key1].value[$index]|h}-->
                            <!--{/if}-->
                        </td>
                        <!--{assign var=key value="price01"}-->
                        <td class="right"><!--{$arrForm[$key].value[$index]|h}--></td>
                        <!--{assign var=key value="price02"}-->
                        <td class="right"><!--{$arrForm[$key].value[$index]|h}--></td>
                        <!--{assign var=key value="product_type_id"}-->
                        <td class="right">
                            <!--{foreach from=$arrForm[$key].value[$index] item=product_type_id}-->
                                <!--{$arrProductType[$product_type_id]|h}-->
                            <!--{/foreach}-->
                        </td>
                        <!--{assign var=key value="down_filename"}-->
                        <td class="right"><!--{$arrForm[$key].value[$index]}--></td>
                        <!--{assign var=key value="down_realfilename"}-->
                        <td class="right"><!--{$arrForm[$key].value[$index]}--></td>
                    </tr>
                <!--{/if}-->
            <!--{/section}-->
        </table>
    <!--{else}-->
        <div class="message">規格が選択されていません。</div>
    <!--{/if}-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'confirm_return','',''); return false"><span class="btn-prev">前へ戻る</span></a></li>
        <!--{if count($arrForm.check.value) > 0}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'complete','',''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        <!--{/if}-->
        </ul>
    </div>
</div>
</form>
