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

<script type="text/javascript">
// URLの表示非表示切り替え
function lfnDispChange(){
    inner_id = 'switch';

    cnt = document.form1.item_cnt.value;

    if($('#disp_url1').css("display") == 'none'){
        for (i = 1; i <= cnt; i++) {
            disp_id = 'disp_url'+i;
            $('#' + disp_id).css("display", "");

            disp_id = 'disp_cat'+i;
            $('#' + disp_id).css("display", "none");

            $('#' + inner_id).html('    URL <a href="#" onClick="lfnDispChange();"> &gt;&gt; カテゴリ表示<\/a>');
        }
    }else{
        for (i = 1; i <= cnt; i++) {
            disp_id = 'disp_url'+i;
            $('#' + disp_id).css("display", "none");

            disp_id = 'disp_cat'+i;
            $('#' + disp_id).css("display", "");

            $('#' + inner_id).html('    カテゴリ <a href="#" onClick="lfnDispChange();"> &gt;&gt; URL表示<\/a>');
        }
    }

}

</script>


<div id="products" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th>商品ID</th>
            <td colspan="3">
                <!--{assign var=key value="search_product_id"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30"/>
            </td>
        </tr>
        <tr>
            <th>商品コード</th>
            <td>
                <!--{assign var=key value="search_product_code"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>商品名</th>
            <td>
                <!--{assign var=key value="search_name"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>カテゴリ</th>
            <td>
                <!--{assign var=key value="search_category_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrCatList selected=$arrForm[$key].value}-->
                </select>
            </td>
            <th>種別</th>
            <td>
                <!--{assign var=key value="search_status"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <!--{html_checkboxes name="$key" options=$arrDISP selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>登録・更新日</th>
            <td colspan="3">
                <!--{if $arrErr.search_startyear || $arrErr.search_endyear}-->
                    <span class="attention"><!--{$arrErr.search_startyear}--></span>
                    <span class="attention"><!--{$arrErr.search_endyear}--></span>
                <!--{/if}-->
                <select name="search_startyear" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrStartYear selected=$arrForm.search_startyear.value}-->
                </select>年
                <select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrStartMonth selected=$arrForm.search_startmonth.value}-->
                </select>月
                <select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrStartDay selected=$arrForm.search_startday.value}-->
                </select>日～
                <select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrEndYear selected=$arrForm.search_endyear.value}-->
                </select>年
                <select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrEndMonth selected=$arrForm.search_endmonth.value}-->
                </select>月
                <select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrEndDay selected=$arrForm.search_endday.value}-->
                </select>日
            </td>
        </tr>
        <tr>
            <th>商品ステータス</th>
            <td colspan="3">
            <!--{assign var=key value="search_product_statuses"}-->
            <span class="attention"><!--{$arrErr[$key]|h}--></span>
            <!--{html_checkboxes name="$key" options=$arrSTATUS selected=$arrForm[$key].value}-->
            </td>
        </tr>
    </table>
    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
        <!--{/if}-->
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max.value}-->
        </select> 件</p>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>

    </div>
    <!--検索条件設定テーブルここまで-->
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="product_id" value="" />
    <input type="hidden" name="category_id" value="" />
    <!--{foreach key=key item=item from=$arrHidden}-->
        <!--{if is_array($item)}-->
            <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->
    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        <!--検索結果-->
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;">検索結果をすべて削除</a>
        <!--{/if}-->
        <a class="btn-tool" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;">CSV ダウンロード</a>
        <a class="btn-tool" href="../contents/csv.php?tpl_subno_csv=product">CSV 出力項目設定</a>
    </div>
    <!--{if count($arrProducts) > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table class="list" id="products-search-result">
            <col width="8%" />
            <col width="9%" />
            <col width="9%" />
            <col width="8%" />
            <col width="25%" />
            <col width="8%" />
            <col width="8%" />
            <col width="5%" />
            <col width="5%" />
            <col width="5%" />
            <col width="5%" />
            <col width="5%" />
            <tr>
                <th rowspan="2">商品ID</th>
                <th rowspan="2">商品画像</th>
                <th rowspan="2">商品コード</th>
                <th rowspan="2">価格(円)</th>
                <th>商品名</th>
                <th rowspan="2">在庫</th>
                <th rowspan="2">種別</th>
                <th rowspan="2">編集</th>
                <th rowspan="2">確認</th>
                <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
                <th rowspan="2">規格</th>
                <!--{/if}-->
                <th rowspan="2">削除</th>
                <th rowspan="2">複製</th>
            </tr>
            <tr>
                <th nowrap><a href="#" onClick="lfnDispChange(); return false;">カテゴリ ⇔ URL</a></th>
            </tr>

            <!--{section name=cnt loop=$arrProducts}-->
                <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
                <!--{assign var=status value="`$arrProducts[cnt].status`"}-->
                <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
                    <td class="id" rowspan="2"><!--{$arrProducts[cnt].product_id}--></td>
                    <td class="thumbnail" rowspan="2">
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65">            </td>
                    <td rowspan="2"><!--{$arrProducts[cnt].product_code_min|h}-->
                        <!--{if $arrProducts[cnt].product_code_min != $arrProducts[cnt].product_code_max}-->
                            <br />～ <!--{$arrProducts[cnt].product_code_max|h}-->
                        <!--{/if}-->
                    </td>
                    <!--{* 価格 *}-->
                    <td rowspan="2" class="right">
                        <!--{$arrProducts[cnt].price02_min|number_format}-->
                        <!--{if $arrProducts[cnt].price02_min != $arrProducts[cnt].price02_max}-->
                            <br />～ <!--{$arrProducts[cnt].price02_max|number_format}-->
                        <!--{/if}-->            </td>
                    <td><!--{$arrProducts[cnt].name|h}--></td>
                    <!--{* 在庫 *}-->
                    <!--{* XXX 複数規格でかつ、全ての在庫数量が等しい場合は先頭に「各」と入れたれたら良いと思う。 *}-->
                    <td class="menu" rowspan="2">
                        <!--{if $arrProducts[cnt].stock_unlimited_min}-->無制限<!--{else}--><!--{$arrProducts[cnt].stock_min|number_format}--><!--{/if}-->
                        <!--{if $arrProducts[cnt].stock_unlimited_min != $arrProducts[cnt].stock_unlimited_max || $arrProducts[cnt].stock_min != $arrProducts[cnt].stock_max}-->
                            <br />～ <!--{if $arrProducts[cnt].stock_unlimited_max}-->無制限<!--{else}--><!--{$arrProducts[cnt].stock_max|number_format}--><!--{/if}-->
                        <!--{/if}-->            </td>
                    <!--{* 表示 *}-->
                    <!--{assign var=key value=$arrProducts[cnt].status}-->
                    <td class="menu" rowspan="2"><!--{$arrDISP[$key]}--></td>
                    <td class="menu" rowspan="2"><span class="icon_edit"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >編集</a></span></td>
                    <td class="menu" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.HTTP_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->&amp;admin=on" target="_blank">確認</a></span></td>
                    <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
                    <td class="menu" rowspan="2"><span class="icon_class"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >規格</a></span></td>
                    <!--{/if}-->
                    <td class="menu" rowspan="2"><span class="icon_delete"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnSetFormValue('category_id', '<!--{$arrProducts[cnt].category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;">削除</a></span></td>
                    <td class="menu" rowspan="2"><span class="icon_copy"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product.php'); fnModeSubmit('copy', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >複製</a></span></td>
                </tr>
                <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
                    <td>
                        <!--{* カテゴリ名 *}-->
                        <div id="disp_cat<!--{$smarty.section.cnt.iteration}-->" style="display:<!--{$cat_flg}-->">
                            <!--{foreach from=$arrProducts[cnt].categories item=category_id name=categories}-->
                                <!--{$arrCatList[$category_id]|sfTrim}-->
                                <!--{if !$smarty.foreach.categories.last}--><br /><!--{/if}-->
                            <!--{/foreach}-->
                        </div>

                        <!--{* URL *}-->
                        <div id="disp_url<!--{$smarty.section.cnt.iteration}-->" style="display:none">
                            <!--{$smarty.const.HTTP_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->
                        </div>
                    </td>
                </tr>
                <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="item_cnt" value="<!--{$arrProducts|@count}-->" />
        <!--検索結果表示テーブル-->
    <!--{/if}-->

</form>

<!--★★検索結果一覧★★-->
<!--{/if}-->
</div>
