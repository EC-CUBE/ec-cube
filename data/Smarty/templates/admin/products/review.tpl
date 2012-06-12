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

<div id="products" class="contents-main">
<form name="search_form" method="post" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th>投稿者名</th>
            <td><input type="text" name="search_reviewer_name" value="<!--{$arrForm.search_reviewer_name|h}-->" size="30" class="box30" /></td>
            <th>投稿者URL</th>
            <td><input type="text" name="search_reviewer_url" value="<!--{$arrForm.search_reviewer_url|h}-->" size="30" class="box30" /></td>
        </tr>
        <tr>
            <th>商品名</th>
            <td><input type="text" name="search_name" value="<!--{$arrForm.search_name|h}-->" size="30" class="box30" /></td>
            <th>商品コード</th>
            <td><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|h}-->" size="30" class="box30" /></td>
        </tr>
        <tr>
            <th>性別</th>
            <!--{assign var=key value=search_sex}-->
            <td><!--{html_checkboxes name="$key" options=$arrSex selected=$arrForm[$key]}--></td>
            <th>おすすめレベル</th>
            <td>
                <!--{assign var=key value=search_recommend_level}-->
                <select name="<!--{$key}-->">
                    <option value="" selected="selected">選択してください</option>
                    <!--{html_options options=$arrRECOMMEND selected=$arrForm[$key].value|h}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>投稿日</th>
            <td colspan="3">
                <!--{if $arrErr.search_startyear || $arrErr.search_endyear}-->
                    <span class="attention"><!--{$arrErr.search_startyear}--></span>
                    <span class="attention"><!--{$arrErr.search_endyear}--></span>
                <!--{/if}-->
                <select name="search_startyear" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrStartYear selected=$arrForm.search_startyear|h}-->
                </select>年
                <select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrStartMonth selected=$arrForm.search_startmonth|h}-->
                </select>月
                <select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrStartDay selected=$arrForm.search_startday|h}-->
                </select>日～
                <select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrEndYear selected=$arrForm.search_endyear|h}-->
                </select>年
                <select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrEndMonth selected=$arrForm.search_endmonth|h}-->
                </select>月
                <select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrEndDay selected=$arrForm.search_endday|h}-->
                </select>日
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
        <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max|h}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li>
                    <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="review_id" value="" />
    <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno|h}-->" />
    <!--{foreach key=key item=item from=$arrHidden}-->
        <!--{if $key ne "search_pageno"}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->
    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax|h}-->件</span>&nbsp;が該当しました。
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;"><span>検索結果をすべて削除</span></a>
        <!--{/if}-->
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;"><span>CSV ダウンロード</span></a>
    </div>
    <!--{if $arrReview > 0 & $tpl_linemax > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table id="products-review-result" class="list">
            <tr>
                <th>投稿日</th>
                <th>投稿者名</th>
                <th>商品名</th>
                <th>おすすめレベル</th>
                <th>表示・非表示</th>
                <th class="edit">編集</th>
                <th class="delete">削除</th>
            </tr>

            <!--{section name=cnt loop=$arrReview}-->
                <tr>
                    <td><!--{$arrReview[cnt].create_date|h|sfDispDBDate}--></td>
                    <td><!--{$arrReview[cnt].reviewer_name|h}--></td>
                    <td><!--{$arrReview[cnt].name|h}--></td>
                    <!--{assign var=key value="`$arrReview[cnt].recommend_level`"}-->
                    <td><!--{$arrRECOMMEND[$key]|h}--></td>
                    <td class="menu"><!--{if $arrReview[cnt].status eq 1}-->表示<!--{elseif $arrReview[cnt].status eq 2}-->非表示<!--{/if}--></td>
                    <td class="menu"><a href="javascript:;" onclick="fnChangeAction('./review_edit.php'); fnModeSubmit('','review_id','<!--{$arrReview[cnt].review_id}-->'); return false;">編集</a></td>
                    <td class="menu"><a href="javascript:;" onclick="fnModeSubmit('delete','review_id','<!--{$arrReview[cnt].review_id}-->'); return false;">削除</a></td>
                </tr>
            <!--{/section}-->
        </table>
        <!--検索結果表示テーブル-->
    <!--{/if}-->
</form>
<!--{/if}-->
<!--★★検索結果一覧★★-->
</div>
