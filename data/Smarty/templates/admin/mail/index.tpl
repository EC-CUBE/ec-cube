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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 */
*}-->
<div id="mail" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="mode" value="search" />
    <h2>配信先検索条件設定</h2>

    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th>顧客名</th>
            <td>
                <!--{if $arrErr.name}--><span class="attention"><!--{$arrErr.name}--></span><br /><!--{/if}-->
                <input type="text" name="name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.name|escape}-->" size="30" class="box30" style="<!--{$arrErr.name|sfGetErrorColor}-->" />
            </td>
            <th>顧客名(カナ)</th>
            <td>
                <!--{if $arrErr.kana}--><span class="attention"><!--{$arrErr.kana}--></span><br /><!--{/if}-->
                <input type="text" name="kana" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.kana|escape}-->" size="30" class="box30" style="<!--{$arrErr.kana|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th>都道府県</th>
            <td>
                <!--{if $arrErr.pref}--><span class="attention"><!--{$arrErr.pref}--></span><br /><!--{/if}-->
                <select name="pref">
                    <option value="" selected="selected" style="<!--{$arrErr.pref|sfGetErrorColor}-->">都道府県を選択</option>
                    <!--{html_options options=$arrPref selected=$list_data.pref}-->
                </select>
            </td>
            <th>TEL</th>
            <td>
                <!--{if $arrErr.tel}--><span class="attention"><!--{$arrErr.tel}--></span><br /><!--{/if}-->
                <input type="text" name="tel" maxlength="<!--{$smarty.const.TEL_LEN}-->" value="<!--{$list_data.tel|escape}-->" size="30" class="box30" style="<!--{$arrErr.tel|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th>性別</th>
            <td>
                <!--{html_checkboxes_ex name="sex" options=$arrSex separator="&nbsp;" selected=$list_data.sex}-->
            </td>
            <th>誕生月</th>
            <td>
                <!--{if $arrErr.birth_month}--><span class="attention"><!--{$arrErr.birth_month}--></span><br /><!--{/if}-->
                <select name="birth_month" style="<!--{$arrErr.birth_month|sfGetErrorColor}-->" >
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getMonth() selected=$list_data.birth_month|escape}-->
                </select>月
            </td>
        </tr>
        <tr>
            <th>配信形式</th>
            <td>
                <!--{if $arrErr.htmlmail}--><span class="attention"><!--{$arrErr.htmlmail}--></span><br /><!--{/if}-->
                <!--{html_radios name="htmlmail" options=$arrHtmlmail separator="&nbsp;" selected=$list_data.htmlmail}-->
            </td>
            <th>購入商品コード</th>
            <td>
                <!--{if $arrErr.buy_product_code}--><span class="attention"><!--{$arrErr.buy_product_code}--></span><!--{/if}-->
                <input type="text" name="buy_product_code" value="<!--{$list_data.buy_product_code}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" style="<!--{$arrErr.buy_product_code|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th>購入回数</th>
            <td>
                <!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><span class="attention"><!--{$arrErr.buy_times_from}--><!--{$arrErr.buy_times_to}--></span><br /><!--{/if}-->
                <input type="text" name="buy_times_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_times_from|escape}-->" size="6" class="box6" style="<!--{$arrErr.buy_times_from|sfGetErrorColor}-->" /> 回 ～
                <input type="text" name="buy_times_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_times_to|escape}-->" size="6" class="box6" style="<!--{$arrErr.buy_times_to|sfGetErrorColor}-->" /> 回
            </td>
            <th>購入金額</th>
            <td>
                <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}-->
                    <span class="attention"><!--{$arrErr.buy_total_from}--><!--{$arrErr.buy_total_to}--></span><br />
                <!--{/if}-->
                <input type="text" name="buy_total_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_total_from|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円 ～
                <input type="text" name="buy_total_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$list_data.buy_total_to|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td colspan="3">
                <!--{if $arrErr.email}--><span class="attention"><!--{$arrErr.email}--></span><!--{/if}-->
                <span style="<!--{$arrErr.email|sfGetErrorColor}-->">
                <input type="text" name="email" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.email|escape}-->" size="60" class="box60" style="<!--{$arrErr.email|sfGetErrorColor}-->"/>
                </span>
            </td>
        </tr>

        <tr>
            <th>携帯メールアドレス</th>
            <td colspan="3">
                <!--{if $arrErr.email_mobile}--><span class="attention"><!--{$arrErr.email_mobile}--></span><!--{/if}-->
                <span style="<!--{$arrErr.email_mobile|sfGetErrorColor}-->">
                <input type="text" name="email_mobile" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.email_mobile|escape}-->" size="60" class="box60" style="<!--{$arrErr.email_mobile|sfGetErrorColor}-->"/>
                </span>
            </td>
        </tr>

        <tr>
            <th>配信メールアドレス種別</th>
            <td colspan="3">
                <!--{html_radios name="mail_type" options=$arrMailType separator="<br />" selected=$list_data.mail_type}-->
            </td>
        </tr>

        <tr>
            <th>職業</th>
            <td colspan="3">
                <!--{if $arrErr.job}--><span class="attention"><!--{$arrErr.job}--></span><!--{/if}-->
                <!--{html_checkboxes_ex name="job" options=$arrJob separator="&nbsp;" selected=$list_data.job}-->
            </td>
        </tr>

        <tr>
            <th>生年月日</th>
            <td colspan="3">
                <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><span class="attention"><!--{$arrErr.b_start_year}--><!--{$arrErr.b_end_year}--></span><br /><!--{/if}-->
                <select name="b_start_year" style="<!--{$arrErr.b_start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$objDate->getYear($smarty.const.BIRTH_YEAR) selected=$list_data.b_start_year}-->
                </select>年
                <select name="b_start_month" style="<!--{$arrErr.b_start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getMonth() selected=$list_data.b_start_month}-->
                </select>月
                <select name="b_start_day" style="<!--{$arrErr.b_start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getDay() selected=$list_data.b_start_day}-->
                </select>日&nbsp;～&nbsp;
                <select name="b_end_year" style="<!--{$arrErr.b_end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$objDate->getYear($smarty.const.BIRTH_YEAR) selected=$list_data.b_end_year}-->
                </select>年
                <select name="b_end_month" style="<!--{$arrErr.b_end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getMonth() selected=$list_data.b_end_month}-->
                </select>月
                <select name="b_end_day" style="<!--{$arrErr.b_end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getDay() selected=$list_data.b_end_day}-->
                </select>日
            </td>
        </tr>
        <tr>
            <th>登録日</th>
            <td colspan="3">
                <!--{if $arrErr.start_year || $arrErr.end_year}--><span class="attention"><!--{$arrErr.start_year}--><!--{$arrErr.end_year}--></span><br /><!--{/if}-->
                <select name="start_year" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR) selected=$list_data.start_year}-->
                </select>年
                <select name="start_month" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getMonth() selected=$list_data.start_month}-->
                </select>月
                <select name="start_day" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getDay() selected=$list_data.start_day}-->
                </select>日&nbsp;～&nbsp;
                <select name="end_year" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR) selected=$list_data.end_year}-->
                </select>年
                <select name="end_month" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getMonth() selected=$list_data.end_month}-->
                </select>月
                <select name="end_day" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getDay() selected=$list_data.end_day}-->
                </select>日
            </td>
        </tr>
        <tr>
            <th>最終購入日</th>
            <td colspan="3">
                <!--{if $arrErr.buy_start_year || $arrErr.buy_end_year}--><span class="attention"><!--{$arrErr.buy_start_year}--><!--{$arrErr.buy_end_year}--></span><br /><!--{/if}-->
                <select name="buy_start_year" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR) selected=$list_data.buy_start_year}-->
                </select>年
                <select name="buy_start_month" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getMonth() selected=$list_data.buy_start_month}-->
                </select>月
                <select name="buy_start_day" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getDay() selected=$list_data.buy_start_day}-->
                </select>日&nbsp;～&nbsp;
                <select name="buy_end_year" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR) selected=$list_data.buy_end_year}-->
                </select>年
                <select name="buy_end_month" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getMonth() selected=$list_data.buy_end_month}-->
                </select>月
                <select name="buy_end_day" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$objDate->getDay() selected=$list_data.buy_end_day}-->
                </select>日
            </td>
        </tr>

        <tr>
            <th>購入商品名</th>
            <td>
                <!--{if $arrErr.buy_product_name}--><span class="attention"><!--{$arrErr.buy_product_name}--></span><!--{/if}-->
                <span style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->">
                <input type="text" name="buy_product_name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$list_data.buy_product_name|escape}-->" size="30" class="box30" style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->" />
                </span>
            </td>
            <th>カテゴリ</th>
            <td>
                <select name="category_id" style="<!--{if $arrErr.category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrCatList selected=$list_data.category_id}-->
                </select>
            </td>
        </tr>
    </table>
    <!--{* 検索条件設定テーブルここまで *}-->

    <div class="btn">
        <a class="btn-normal" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', '');"><span>この条件で検索する</span></a>
    </div>
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'back') }-->

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" />
<input type="hidden" name="result_email" value="" />
<!--{foreach key=key item=val from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$val|escape}-->" />
<!--{/foreach}-->

    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','','');"><span>検索結果をすべて削除</span></a>
        <!--{/if}-->
        <!--{if $tpl_linemax > 0}-->
            <a class="btn-normal" href="javascript:;" onclick="document.form1['mode'].value='input';"><span>配信内容を設定する</span></a>
        <!--{/if}-->
    </div>
    <!--{include file=$tpl_pager}-->

    <!--{if count($arrResults) > 0}-->

    <!--検索結果表示テーブル-->
    <table class="list">
        <tr>
            <th>#</th>
            <th>会員番号</th>
            <th>注文番号</th>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>希望配信</th>
            <th>登録日</th>
            <th>削除</th>
        </tr>
        <!--{section name=i loop=$arrResults}-->
        <tr>
            <td class="center"><!--{$smarty.section.i.iteration}--></td>
            <td class="center"><!--{$arrResults[i].customer_id|default:"非会員"}--></td>

            <!--{assign var=key value="`$arrResults[i].customer_id`"}-->
            <td class="center">
                <!--{foreach key=key item=val from=$arrCustomerOrderId[$key]}-->
                <a href="#" onclick="fnOpenWindow('../order/edit.php?order_id=<!--{$val}-->','order_disp','800','900'); return false;" ><!--{$val}--></a><br />
                <!--{foreachelse}-->
                -
                <!--{/foreach}-->
            </td>

            <td><!--{$arrResults[i].name01|escape}--> <!--{$arrResults[i].name02|escape}--></td>
            <td><!--{$arrResults[i].email|escape}--></td>
            <!--{assign var="key" value="`$arrResults[i].mailmaga_flg`"}-->
            <td class="center"><!--{$arrMAILMAGATYPE[$key]}--></td>
            <td><!--{$arrResults[i].create_date|sfDispDBDate}--></td>
            <!--{if $arrResults[i].customer_id != ""}-->
            <td class="center">-</td>
            <!--{else}-->
            <td class="center"><a href="?" onclick="fnFormModeSubmit('form1','delete','result_email','<!--{$arrResults[i].email|escape}-->'); return false;">削除</a></td>
            <!--{/if}-->
        </tr>
        <!--{/section}-->
    </table>
    <!--検索結果表示テーブル-->
    <!--{/if}-->

</form>

<!--{/if}-->
</div>
