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
<script type="text/javascript">
<!--

    function fnReturn() {
        document.form_search.action = './<!--{$smarty.const.DIR_INDEX_URL}-->';
        document.form_search.submit();
        return false;
    }

    function fnOrderidSubmit(order_id, order_id_value) {
        if(order_id != "" && order_id_value != "") {
            document.form2[order_id].value = order_id_value;
        }
        document.form2.action = '../order/edit.php';
        document.form2.submit();
    }

//-->
</script>

<form name="form_search" method="post" action="">
    <input type="hidden" name="mode" value="search" />
    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "del_mode" && $key ne "edit_customer_id" && $key ne "del_customer_id" && $key ne "csv_mode" && $key ne "job" && $key ne "sex"}--><input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->"><!--{/if}-->
    <!--{/foreach}-->
    <!--{foreach from=$arrSearchData.job key="key" item="item"}-->
        <input type="hidden" name="job[]" value="<!--{$item}-->" />
    <!--{/foreach}-->
    <!--{foreach from=$arrSearchData.sex key="key" item="item"}-->
        <input type="hidden" name="sex[]" value="<!--{$item}-->" />
    <!--{/foreach}-->
</form>

<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="mode" value="confirm" />
    <input type="hidden" name="edit_email" value="<!--{$tpl_edit_email}-->" />
    <input type="hidden" name="customer_id" value="<!--{$list_data.customer_id|escape}-->" />

    <!-- 検索条件の保持 -->
    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "job" && $key ne "sex"}--><input type="hidden" name="search_data[<!--{$key|escape}-->]" value="<!--{$item|escape}-->"><!--{/if}-->
    <!--{/foreach}-->
    <!--{foreach from=$arrSearchData.job key="key" item="item"}-->
        <input type="hidden" name="search_data[job][]" value="<!--{$item}-->" />
    <!--{/foreach}-->
    <!--{foreach from=$arrSearchData.sex key="key" item="item"}-->
        <input type="hidden" name="search_data[sex][]" value="<!--{$item}-->" />
    <!--{/foreach}-->

    <div id="customer" class="contents-main">
        <h2>顧客編集</h2>
        <table class="form">
            <tr>
                <th>顧客ID<span class="attention"> *</span></th>
                <td><!--{$list_data.customer_id|escape}--></td>
            </tr>
            <tr>
                <th>会員状態<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.status}--></span>
                    <input type="radio" name="status"value=1 id="no_mem" <!--{if $list_data.status == 1}--> checked="checked" <!--{/if}--> <!--{if $list_data.status == 2}-->disabled<!--{/if}-->><label for="no_mem">仮会員</label>
                    <input type="radio" name="status"value=2 id="mem"<!--{if $list_data.status == 2}--> checked="checked" <!--{/if}-->><label for="mem">本会員</label>
                </td>
            </tr>
            <tr>
                <th>お名前<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
                    <input type="text" name="name01" value="<!--{$list_data.name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;&nbsp;<input type="text" name="name02" value="<!--{$list_data.name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th>お名前(フリガナ)<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
                    <input type="text" name="kana01" value="<!--{$list_data.kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.kana01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;&nbsp;<input type="text" name="kana02" value="<!--{$list_data.kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.kana02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th>郵便番号<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span>
                    〒 <input type="text" name="zip01" value="<!--{$list_data.zip01|escape}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" size="6" class="box6" maxlength="3" <!--{if $arrErr.zip01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="zip02" value="<!--{$list_data.zip02|escape}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" size="6" class="box6" maxlength="4" <!--{if $arrErr.zip02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                    <input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01');" />
                </td>
            </tr>
            <tr>
                <th>ご住所<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
                    <select name="pref" <!--{if $arrErr.pref != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                        <option value="" selected="selected">都道府県を選択</option>
                        <!--{html_options options=$arrPref selected=$list_data.pref}-->
                    </select>
                    <input type="text" name="addr01" value="<!--{$list_data.addr01|escape}-->" size="60" class="box60" <!--{if $arrErr.addr01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS1}--><br />
                    <input type="text" name="addr02" value="<!--{$list_data.addr02|escape}-->" size="60" class="box60" <!--{if $arrErr.addr02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS2}-->
                </td>
            </tr>
            <tr>
                <th>メールアドレス<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.email}--></span>
                    <input type="text" name="email" value="<!--{$list_data.email|escape}-->" size="60" class="box60" <!--{if $arrErr.email != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th>携帯メールアドレス</th>
                <td>
                    <span class="attention"><!--{$arrErr.email_mobile}--></span>
                    <input type="text" name="email_mobile" value="<!--{$list_data.email_mobile|escape}-->" size="60" class="box60" <!--{if $arrErr.email_mobile != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th>電話番号<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
                    <input type="text" name="tel01" value="<!--{$list_data.tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel02" value="<!--{$list_data.tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel03" value="<!--{$list_data.tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th>FAX</th>
                <td>
                    <span class="attention"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span>
                    <input type="text" name="fax01" value="<!--{$list_data.fax01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax02" value="<!--{$list_data.fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.tel02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax03" value="<!--{$list_data.fax03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.fax03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th>ご性別<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.sex}--></span>
                    <span <!--{if $arrErr.sex != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                        <!--{html_radios name="sex" options=$arrSex separator=" " selected=$list_data.sex}-->
                    </span>
                </td>
            </tr>
            <tr>
                <th>ご職業</th>
                <td>
                    <span class="attention"><!--{$arrErr.job}--></span>
                    <select name="job" <!--{if $arrErr.job != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                    <option value="" selected="selected">選択してください</option>
                    <!--{html_options options=$arrJob selected=$list_data.job}-->
                    </select>
                </td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td>
                    <span class="attention"><!--{$arrErr.year}--></span>
                    <select name="year" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <option value="" selected="selected">------</option>
                        <!--{html_options options=$arrYear selected=$list_data.year}-->
                    </select>年
                    <select name="month" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <option value="" selected="selected">----</option>
                        <!--{html_options options=$arrMonth selected=$list_data.month}-->
                    </select>月
                    <select name="day" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <option value="" selected="selected">----</option>
                        <!--{html_options options=$arrDay selected=$list_data.day"}-->
                    </select>日
                </td>
            </tr>
            <tr>
                <th>パスワード<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.password}--></span>
                    <input type="password" name="password" value="<!--{$list_data.password|escape}-->" size="30" class="box30" <!--{if $arrErr.password != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />　半角英数小文字<!--{$smarty.const.PASSWORD_LEN1}-->～<!--{$smarty.const.PASSWORD_LEN2}-->文字（記号不可）
                </td>
            </tr>
            <tr>
                <th>パスワードを忘れたときのヒント<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>
                    質問： 
                    <select name="reminder" <!--{if $arrErr.reminder != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <option value="" selected="selected">選択してください</option>
                        <!--{html_options options=$arrReminder selected=$list_data.reminder}-->
                    </select><br />
                    答え： 
                    <input type="text" name="reminder_answer" value="<!--{$list_data.reminder_answer|escape}-->" size="30" class="box30" <!--{if $arrErr.reminder_answer != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th>メールマガジン<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.mailmaga_flg}--></span>
                    <input type="radio" name="mailmaga_flg" value="1" <!--{if $arrErr.mailmaga_flg != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.mailmaga_flg eq 1 or $list_data.mailmaga_flg eq 4}-->checked<!--{/if}--> />HTML　
                    <input type="radio" name="mailmaga_flg" value="2" <!--{if $arrErr.mailmaga_flg != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.mailmaga_flg eq 2 or $list_data.mailmaga_flg eq 5}-->checked<!--{/if}--> />テキスト　
                    <input type="radio" name="mailmaga_flg" value="3" <!--{if $arrErr.mailmaga_flg != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $list_data.mailmaga_flg eq "" or $list_data.mailmaga_flg eq 3 or $list_data.mailmaga_flg eq 6}-->checked<!--{/if}--> />希望しない
                </td>
            </tr>
            <tr>
                <th>SHOP用メモ</th>
                <td>
                    <span class="attention"><!--{$arrErr.note}--></span>
                    <textarea name="note" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" <!--{if $arrErr.note != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> cols="60" rows="8" class="area60"><!--{$list_data.note|escape}--></textarea>
                </td>
            </tr>
            <tr>
                <th>所持ポイント</th>
                <td>
                    <span class="attention"><!--{$arrErr.point}--></span>
                    <input type="text" name="point" value="<!--{$list_data.point|escape}-->" maxlength="<!--{$smarty.const.TEL_LEN}-->" <!--{if $arrErr.point != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> size="6" class="box6" <!--{if $arrErr.point != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> pt
                </td>
            </tr>
        </table>

        <div class="btn">
            <button type="button" onclick="return fnReturn();"><span>検索画面に戻る</span></button>
            <button type="submit"><span>確認ページへ</span></button>
        </div>

        <input type="hidden" name="order_id" value="" />
        <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
        <input type="hidden" name="edit_customer_id" value="<!--{$edit_customer_id}-->" >

        <h2>購入履歴一覧</h2>
        <p><span class="attention"><!--購入履歴一覧--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。</p>

        <!--{include file=$tpl_pager}-->

        <!--{if $tpl_linemax > 0}-->
            <!--{* 購入履歴一覧表示テーブル *}-->
            <table class="list">
                <tr>
                    <th>日付</th>
                    <th>注文番号</th>
                    <th>購入金額</th>
                    <th>発送日</th>
                    <th>支払方法</th>
                </tr>
                <!--{section name=cnt loop=$arrPurchaseHistory}-->
                <tr class="center">
                    <td><!--{$arrPurchaseHistory[cnt].create_date|sfDispDBDate}--></td>
                    <td><a href="#" onclick="fnOpenWindow('../order/edit.php?order_id=<!--{$arrPurchaseHistory[cnt].order_id}-->','order_disp','800','900'); return false;" ><!--{$arrPurchaseHistory[cnt].order_id}--></a></td>
                    <td><!--{$arrPurchaseHistory[cnt].payment_total|number_format}-->円</td>
                    <td><!--{if $arrPurchaseHistory[cnt].status eq 5}--><!--{$arrPurchaseHistory[cnt].commit_date|sfDispDBDate}--><!--{else}-->未発送<!--{ /if }--></td>
                    <!--{assign var=payment_id value="`$arrPurchaseHistory[cnt].payment_id`"}-->
                    <td><!--{$arrPayment[$payment_id]|escape}--></td>
                </tr>
                <!--{/section}-->
            </table>
            <!--{* 購入履歴一覧表示テーブル *}-->
        <!--{else}-->
            <div class="message">購入履歴はありません。</div>
        <!--{/if}-->

    </div>
</form>
