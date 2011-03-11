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
<script type="text/javascript">//<![CDATA[
$(function() {
    $('input[id^=stock_unlimited_]').each(function() {
        var index = $(this).attr('id').replace(/^stock_unlimited_/ig, '');
        var checked = $(this).attr('checked');

        if (checked) {
            $('#stock_' + index)
                .attr('readonly', true)
                .css('background-color', '<!--{$smarty.const.DISABLED_RGB}-->');
        }
    });

    $('input[id^=stock_unlimited_]').change(function() {
        var index = $(this).attr('id').replace(/^stock_unlimited_/ig, '');
        var checked = $(this).attr('checked');

        if (checked) {
            $('#stock_' + index)
                .attr('readonly', true)
                .css('background-color', '<!--{$smarty.const.DISABLED_RGB}-->');
        } else {
            $('#stock_' + index)
                .attr('readonly', false)
                .css('background-color', '');
        }
    });
});
//]]>
</script>
<h2>商品規格登録</h2>
<form name="form1" id="form1" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="product_id" value="<!--{$arrForm.product_id.value|h}-->" />
<input type="hidden" name="down_key" value="">
<!--{* foreach key=name item=item from=$arrHidden *}-->
<input type="hidden" name="<!--{$name}-->" value="<!--{$item|h}-->" />
<!--{* /foreach *}-->
<div id="products" class="contents-main">

    <table>
        <tr>
            <th>商品名</th>
            <td><!--{$arrForm.product_name.value|h}--></td>
        </tr>
        <tr>
            <th>規格1<span class="attention">*</span></th>
            <td>
                <!--{assign var=key value="select_class_id1"}-->
                <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->

                <select name="<!--{$key}-->">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrClass selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>規格2</th>
            <td>
                <!--{assign var=key value="select_class_id2"}-->
                <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <select name="<!--{$key}-->">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrClass selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;" ><span class="btn-prev">検索結果へ戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('disp','',''); return false;"><span class="btn-next">表示する</span></a></li>
        <!--{if $arrForm.total.value > 0}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('delete','',''); return false;"><span class="btn-next">削除する</span></a></li>
        <!--{/if}-->
        </ul>
    </div>

    <!--{if $arrForm.total.value > 0}-->

    <!--{foreach item=item name=i from=$arrClassCat}-->
        <!--{if $smarty.foreach.i.first}-->
            <!--{assign var=cnt value=$smarty.foreach.i.total}-->
        <!--{/if}-->
    <!--{/foreach}-->

    <div class="list-info clearfix">
        <div class="btn"><a class="btn-normal" href="javascript:;" onclick="fnCopyValue('<!--{$cnt}-->', '<!--{$smarty.const.DISABLED_RGB}-->'); return false;"><span>一行目のデータをコピーする</span></a></div>
        <p><span class="bold">アップロード可能な拡張子：</span><!--{$smarty.const.DOWNLOAD_EXTENSION}-->(パラメータ DOWNLOAD_EXTENSION)</p>
    </div>

    <!--{assign var=key1 value="class_id1"}-->
    <!--{assign var=key2 value="class_id2"}-->
    <!--{assign var=class_id1 value=$arrForm[$key1].value|h}-->
    <!--{assign var=class_id2 value=$arrForm[$key2].value|h}-->
    <input type="hidden" name="<!--{$key1}-->" value="<!--{$class_id1}-->" />
    <input type="hidden" name="<!--{$key2}-->" value="<!--{$class_id2}-->" />
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
            <th>規格1<br>(<!--{$arrClass[$class_id1]|default:"未選択"|h}-->)</th>
            <th>規格2<br>(<!--{$arrClass[$class_id2]|default:"未選択"|h}-->)</th>
            <th>商品コード</th>
            <th>在庫数<span class="attention">*</span></th>
            <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(円)</th>
            <th><!--{$smarty.const.SALE_PRICE_TITLE}-->(円)<span class="attention">*</span></th>
            <th>商品種別<span class="attention">*</span></th>
            <th>ダウンロード<br>ファイル名<span class="red"><br>上限<!--{$smarty.const.STEXT_LEN}-->文字</span></th>
            <th>ダウンロード商品用<br>ファイル</th>
        </tr>
        <!--{section name=cnt loop=$arrForm.total.value}-->
            <!--{assign var=index value=$smarty.section.cnt.index}-->

            <!--{* TODO *}-->
            <!--{assign var=key value="error:`$smarty.section.cnt.iteration`"}-->
            <!--{if $arrErr[$key] != ""}-->
            <tr>
                <td colspan="10"><span class="attention"><!--{$arrErr[$key]}--></span></td>
            </tr>
            <!--{/if}-->
            <tr >

                <td class="center" >
                    <!--{assign var=key value="classcategory_id1"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                    <!--{assign var=key value="classcategory_id2"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                    <!--{assign var=key value="product_class_id"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                    <!--{assign var=key value="check"}-->
                    <input type="checkbox" name="<!--{$key}-->[<!--{$index}-->]" value="1" <!--{if $arrForm[$key].value[$index] == 1}-->checked="checked"<!--{/if}--> />
                </td>
                <td class="center">
                    <!--{assign var=key value="classcategory_name1"}-->
                    <!--{$arrForm[$key].value[$index]|h}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                </td>
                <td class="center">
                    <!--{assign var=key value="classcategory_name2"}-->
                    <!--{$arrForm[$key].value[$index]|h}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                </td>
                <td class="center">
                    <!--{assign var=key value="product_code"}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
                <td class="center">
                    <!--{assign var=key value="stock"}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> id="<!--{$key}-->_<!--{$index}-->" />
                    <!--{assign var=key value="stock_unlimited"}--><br />
                    <input type="checkbox" name="<!--{$key}-->[<!--{$index}-->]" value="1" <!--{if $arrForm[$key].value[$index] == "1"}-->checked="checked"<!--{/if}--> onClick="fnCheckStockNoLimit('<!--{$index}-->','<!--{$smarty.const.DISABLED_RGB}-->');" id="<!--{$key}-->_<!--{$index}-->" /><label for="<!--{$key}-->_<!--{$index}-->">無制限</label>
                </td>
                <td class="center">
                    <!--{assign var=key value="price01"}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
                <td class="center">
                    <!--{assign var=key value="price02"}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
                <td class="class-product-type">
                    <!--{assign var=key value="product_type_id"}-->
                    <!--{html_radios name="`$key`[`$index`]" options=$arrProductType selected=$arrForm[$key].value[$index]|h separator='<br />'}-->
                </td>
                <td class="center">
                    <!--{assign var=key value="down_filename}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="10" />
                </td>
                <td>
                    <!--{assign var=key value="down_realfilename"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <!--{if $arrForm[$key].value[$index] != ""}-->
                        <!--{$arrForm[$key].value[$index]|h}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <a href="?" onclick="fnFormModeSubmit('form1', 'delete_down', 'down_key[<!--{$index}-->]', '<!--{$key}-->'); return false;">[ファイルの取り消し]</a><br />
                    <!--{/if}-->
                    <input type="file" name="<!--{$key}-->[<!--{$index}-->]" size="10" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
                    <a class="btn-normal" href="javascript:;" name="btn" onclick="fnFormModeSubmit('form1', 'upload_down', 'down_key', '<!--{$key}-->[<!--{$index}-->]'); return false;">アップロード</a>
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
