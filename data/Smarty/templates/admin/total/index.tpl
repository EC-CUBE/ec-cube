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

<div id="total" class="contents-main">
    <!--{* 検索条件設定テーブルここから *}-->
    <table summary="検索条件設定テーブル" class="input-form form">
        <tr>
            <th>月度集計</th>
            <td>
                <form name="search_form1" id="search_form1" method="post" action="?">
                    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                    <input type="hidden" name="mode" value="search" />
                    <input type="hidden" name="form" value="1" />
                    <input type="hidden" name="page" value="<!--{$arrForm.page.value|h}-->" />
                    <input type="hidden" name="type" value="<!--{$smarty.post.type|h}-->" />
                    <!--{if $arrErr.search_startyear_m || $arrErr.search_endyear_m}-->
                        <span class="attention"><!--{$arrErr.search_startyear_m}--></span>
                        <span class="attention"><!--{$arrErr.search_endyear_m}--></span>
                    <!--{/if}-->
                    <select name="search_startyear_m"    style="<!--{$arrErr.search_startyear_m|sfGetErrorColor}-->">
                        <!--{html_options options=$arrYear selected=$arrForm.search_startyear_m.value}-->
                    </select>年
                    <select name="search_startmonth_m" style="<!--{$arrErr.search_startyear_m|sfGetErrorColor}-->">
                        <!--{html_options options=$arrMonth selected=$arrForm.search_startmonth_m.value}-->
                    </select>月度 (<!--{if $smarty.const.CLOSE_DAY == 31}-->末<!--{else}--><!--{$smarty.const.CLOSE_DAY}--><!--{/if}-->日締め)
                    <a class="btn-normal" href="javascript:;" onclick="fnFormModeSubmit('search_form1', 'search', '', ''); return false;" name="subm">月度で集計する</a>
                </form>
            </td>
        </tr>
        <tr>
            <th>期間集計</th>
            <td>
                <form name="search_form2" id="search_form2" method="post" action="?">
                    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                    <input type="hidden" name="mode" value="search" />
                    <input type="hidden" name="form" value="2" />
                    <input type="hidden" name="page" value="<!--{$arrForm.page.value|h}-->" />
                    <input type="hidden" name="type" value="<!--{$smarty.post.type|h}-->" />
                    <!--{if $arrErr.search_startyear || $arrErr.search_endyear}-->
                        <span class="attention"><!--{$arrErr.search_startyear}--></span>
                        <span class="attention"><!--{$arrErr.search_endyear}--></span>
                    <!--{/if}-->
                    <select name="search_startyear"    style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrYear selected=$arrForm.search_startyear.value|h}-->
                    </select>年
                    <select name="search_startmonth" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.search_startmonth.value|h}-->
                    </select>月
                    <select name="search_startday" style="<!--{$arrErr.search_startyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.search_startday.value|h}-->
                    </select>日～
                    <select name="search_endyear" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrYear selected=$arrForm.search_endyear.value|h}-->
                    </select>年
                    <select name="search_endmonth" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.search_endmonth.value|h}-->
                    </select>月
                    <select name="search_endday" style="<!--{$arrErr.search_endyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.search_endday.value|h}-->
                    </select>日
                    <a class="btn-normal" href="javascript:;" onclick="fnFormModeSubmit('search_form2', 'search', '', ''); return false;" name="subm">期間で集計する</a>
                </form>
            </td>
        </tr>
    </table>
    <!--{* 検索条件設定テーブルここまで *}-->


    <!--{* 検索結果一覧ここから *}-->
    <!--{if count($arrResults) > 0}-->
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="hidden" name="type" value="<!--{$arrForm.type.value|h}-->" />
        <input type="hidden" name="page" value="<!--{$arrForm.page.value|h}-->" />
        <!--{foreach key=key item=item from=$arrHidden}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
        <!--{/foreach}-->

            <!--検索結果表示テーブル-->
            <h2><!--{include file=$tpl_graphsubtitle}--></h2>

            <div class="btn">
                <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;"><span>CSVダウンロード</span></a>
            </div>

            <!--{* グラフ表示 *}-->
                <!--{if $install_GD}-->
                <div id="graph-image">
                    <!--{* <img src="<!--{$tpl_image}-->?<!--{$cashtime}-->" alt="グラフ"> *}-->
                    <img src="?draw_image=true&amp;type=<!--{$smarty.post.type|h}-->&amp;mode=search&amp;page=<!--{$smarty.post.page|h}-->&amp;search_startyear_m=<!--{$smarty.post.search_startyear_m|h}-->&amp;search_startmonth_m=<!--{$smarty.post.search_startmonth_m|h}-->&amp;search_startyear=<!--{$smarty.post.search_startyear|h}-->&amp;search_startmonth=<!--{$smarty.post.search_startmonth|h}-->&amp;search_startday=<!--{$smarty.post.search_startday|h}-->&amp;search_endyear=<!--{$smarty.post.search_endyear|h}-->&amp;search_endmonth=<!--{$smarty.post.search_endmonth|h}-->&amp;search_endday=<!--{$smarty.post.search_endday|h}-->" alt="グラフ" />
                </div>
                <!--{/if}-->
            <!--{* グラフ表示 *}-->

            <!--{* ▼検索結果テーブルここから *}-->
            <!--{include file=$tpl_page_type}-->
            <!--{* ▲検索結果テーブルここまで *}-->
            <!--検索結果表示テーブル-->
        </form>
    <!--{else}-->
        <!--{if $smarty.post.mode == 'search'}-->
            <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="search" />
            <input type="hidden" name="type" value="<!--{$arrForm.type.value|h}-->" />
            <input type="hidden" name="page" value="<!--{$arrForm.page.value|h}-->" />
            <!--{foreach key=key item=item from=$arrHidden}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
            <!--{/foreach}-->
            <!--検索結果表示テーブル-->
            <h2><!--{include file=$tpl_graphsubtitle}--></h2>
            <div class="message">
                該当するデータはありません。
            </div>
            <!--検索結果表示テーブル-->
            </form>
        <!--{/if}-->
    <!--{/if}-->
    <!--{* 検索結果一覧ここまで *}-->
</div>
