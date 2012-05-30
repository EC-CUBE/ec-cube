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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<div id="mail" class="contents-main">
    <h2>配信条件</h2>

    <table class="form">
        <tr>
            <th>会員ID</th>
            <td><!--{$arrSearchData.search_customer_id|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>都道府県</th>
            <td>
            <!--{if $arrSearchData.search_pref}-->
                <!--{$arrPref[$arrSearchData.search_pref]|h}-->　
            <!--{else}-->(未指定)<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>お名前</th>
            <td><!--{$arrSearchData.search_name|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>お名前(フリガナ)</th>
            <td><!--{$arrSearchData.search_kana|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>性別</th>
            <td>
            <!--{assign var=key value="search_sex"}-->
            <!--{if is_array($arrSearchData[$key])}-->
                <!--{foreach item=item from=$arrSearchData[$key]}-->
                    <!--{$arrSex[$item]|h}-->　
                <!--{/foreach}-->
            <!--{else}-->(未指定)<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>誕生月</th>
            <td><!--{if $arrSearchData.search_birth_month}--><!--{$arrSearchData.search_birth_month|h}-->月<!--{else}-->(未指定)<!--{/if}--></td>
        </tr>
        <tr>
            <th>誕生日</th>
            <td>
            <!--{if $arrSearchData.search_b_start_year}-->
                <!--{$arrSearchData.search_b_start_year}-->年<!--{$arrSearchData.search_b_start_month}-->月<!--{$arrSearchData.search_b_start_day}-->日&nbsp;～
                <!--{if $arrSearchData.search_b_end_year}-->&nbsp;<!--{$arrSearchData.search_b_end_year}-->年<!--{$arrSearchData.search_b_end_month}-->月<!--{$arrSearchData.search_b_end_day}-->日<!--{/if}-->
            <!--{else}-->(未指定)<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><!--{$arrSearchData.search_email|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>携帯メールアドレス</th>
            <td><!--{$arrSearchData.search_email_mobile|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td><!--{$arrSearchData.search_tel|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>職業</th>
            <td>
            <!--{assign var=key value="search_job"}-->
            <!--{if is_array($arrSearchData[$key])}-->
                <!--{foreach item=item from=$arrSearchData[$key]}-->
                    <!--{$arrJob[$item]|h}-->　
                <!--{/foreach}-->
            <!--{else}-->(未指定)<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>購入金額</th>
            <td>
                <!--{if $arrSearchData.search_buy_total_from == null}-->(未指定)<!--{else}--><!--{$arrSearchData.search_buy_total_from|h}-->円<!--{/if}--> ～
                <!--{if $arrSearchData.search_buy_total_to == null}-->(未指定)<!--{else}--><!--{$arrSearchData.search_buy_total_to|h}-->円<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>購入回数</th>
            <td>
                <!--{if $arrSearchData.search_buy_times_from == null}-->(未指定)<!--{else}--><!--{$arrSearchData.search_buy_times_from|h}-->回<!--{/if}--> ～
                <!--{if $arrSearchData.search_buy_times_to == null}-->(未指定)<!--{else}--><!--{$arrSearchData.search_buy_times_to|h}-->回<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>登録・更新日</th>
            <td>
            <!--{if $arrSearchData.search_start_year}-->
                <!--{$arrSearchData.search_start_year}-->年<!--{$arrSearchData.search_start_month}-->月<!--{$arrSearchData.search_start_day}-->日&nbsp;～
                <!--{if $arrSearchData.search_end_year}-->&nbsp;<!--{$arrSearchData.search_end_year}-->年<!--{$arrSearchData.search_end_month}-->月<!--{$arrSearchData.search_end_day}-->日<!--{/if}-->
            <!--{else}-->(未指定)<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>最終購入日</th>
            <td>
            <!--{if $arrSearchData.search_buy_start_year}-->
                <!--{$arrSearchData.search_buy_start_year}-->年<!--{$arrSearchData.search_buy_start_month}-->月<!--{$arrSearchData.search_buy_start_day}-->日&nbsp;～
                <!--{if $arrSearchData.search_buy_end_year}-->&nbsp;<!--{$arrSearchData.search_buy_end_year}-->年<!--{$arrSearchData.search_buy_end_month}-->月<!--{$arrSearchData.search_buy_end_day}-->日<!--{/if}-->
            <!--{else}-->(未指定)<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>購入商品名</th>
            <td><!--{$arrSearchData.search_buy_product_name|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>購入商品コード</th>
            <td><!--{$arrSearchData.search_buy_product_code|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>カテゴリ</th>
            <td>
            <!--{if $arrSearchData.search_category_id}-->
                <!--{if $arrCatList[$arrSearchData.search_category_id]}-->
                    <!--{$arrCatList[$arrSearchData.search_category_id]|h}-->
                <!--{else}-->(削除済みカテゴリ)<!--{/if}-->
            <!--{else}-->(未指定)<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>配信形式</th>
            <td><!--{$arrHtmlmail[$arrSearchData.search_htmlmail]|default:"(未指定)"|h}--></td>
        </tr>
        <tr>
            <th>配信メールアドレス種別</th>
            <td><!--{$arrMailType[$arrSearchData.search_mail_type]|default:"(未指定)"|h}--></td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="window.close(); return false;"><span class="btn-next">ウインドウを閉じる</span></a></li>
        </ul>
    </div>
</div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
