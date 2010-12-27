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

  function fnCustomerPage(pageno) {
    document.form1.search_pageno.value = pageno;
    document.form1.submit();
  }

  function fnCSVDownload(pageno) {
    document.form1['csv_mode'].value = 'csv';
    document.form1.submit();
    document.form1['csv_mode'].value = '';
    return false;
  }

  function fnDelete(customer_id) {
    if (confirm('この顧客情報を削除しても宜しいですか？')) {
      document.form1.mode.value = "delete"
      document.form1['edit_customer_id'].value = customer_id;
      document.form1.submit();
      return false;
    }
  }

  function fnEdit(customer_id) {
    document.form1.action = './edit.php';
    document.form1.mode.value = "edit_search"
    document.form1['edit_customer_id'].value = customer_id;
    document.form1.search_pageno.value = 1;
    document.form1.submit();
    return false;
  }

  function fnReSendMail(customer_id) {
    if (confirm('仮登録メールを再送しても宜しいですか？')) {
      document.form1.mode.value = "resend_mail"
      document.form1['edit_customer_id'].value = customer_id;
      document.form1.submit();
      return false;
    }
  }

  function fnSubmit() {
    document.form1.submit();
    return false;
  }
//-->
</script>


<div id="customer" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="mode" value="search" />
  <h2>検索条件設定</h2>

  <!--検索条件設定テーブルここから-->
  <table class="form">
    <tr>
      <th>顧客ID</th>
      <td><!--{if $arrErr.customer_id}--><span class="attention"><!--{$arrErr.customer_id}--></span><br /><!--{/if}--><input type="text" name="customer_id" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.customer_id|escape}-->" size="30" class="box30" <!--{if $arrErr.customer_id}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
      <th>都道府県</th>
      <td>
        <!--{if $arrErr.pref}--><span class="attention"><!--{$arrErr.pref}--></span><br /><!--{/if}-->
        <select name="pref">
          <option value="" selected="selected" <!--{if $arrErr.name}--><!--{sfSetErrorStyle}--><!--{/if}-->>都道府県を選択</option>
          <!--{html_options options=$arrPref selected=$arrForm.pref}-->
        </select>
      </td>
    </tr>
    <tr>
      <th>顧客名</th>
      <td><!--{if $arrErr.name}--><span class="attention"><!--{$arrErr.name}--></span><br /><!--{/if}--><input type="text" name="name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.name|escape}-->" size="30" class="box30" <!--{if $arrErr.name}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
      <th>顧客名(カナ)</th>
      <td><!--{if $arrErr.kana}--><span class="attention"><!--{$arrErr.kana}--></span><br /><!--{/if}--><input type="text" name="kana" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.kana|escape}-->" size="30" class="box30" <!--{if $arrErr.kana}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
    </tr>
    <tr>
      <th>性別</th>
      <td><!--{html_checkboxes name="sex" options=$arrSex separator="&nbsp;" selected=$arrForm.sex}--></td>
      <th>誕生月</th>
      <td><!--{if $arrErr.birth_month}--><span class="attention"><!--{$arrErr.birth_month}--></span><br /><!--{/if}-->
        <select name="birth_month" style="<!--{$arrErr.birth_month|sfGetErrorColor}-->" >
          <option value="" selected="selected">--</option>
          <!--{html_options options=$objDate->getMonth() selected=$arrForm.birth_month}-->
        </select>月
      </td>
    </tr>
    <tr>
      <th>会員状態</th>
      <td colspan="3"><!--{html_checkboxes name="status" options=$arrStatus separator="&nbsp;" selected=$arrForm.status}--></td>
    </tr>
    <tr>
      <th>誕生日</th>
      <td colspan="3">
        <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><span class="attention"><!--{$arrErr.b_start_year}--><!--{$arrErr.b_end_year}--></span><br /><!--{/if}-->
        <select name="b_start_year" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.b_start_year}-->
        </select>年
        <select name="b_start_month" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.b_start_month}-->
        </select>月
        <select name="b_start_day" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.b_start_day}-->
        </select>日～
        <select name="b_end_year" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.b_end_year}-->
        </select>年
        <select name="b_end_month" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.b_end_month}-->
        </select>月
        <select name="b_end_day" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.b_end_day}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th>メールアドレス</th>
      <td colspan="3"><!--{if $arrErr.email}--><span class="attention"><!--{$arrErr.email}--></span><!--{/if}--><input type="text" name="email" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.email|escape}-->" size="60" class="box60" <!--{if $arrErr.email}--><!--{sfSetErrorStyle}--><!--{/if}-->/></td>
    </tr>
    <tr>
      <th>携帯メールアドレス</th>
      <td colspan="3"><!--{if $arrErr.email_mobile}--><span class="attention"><!--{$arrErr.email_mobile}--></span><!--{/if}--><input type="text" name="email_mobile" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.email_mobile|escape}-->" size="60" class="box60" <!--{if $arrErr.email_mobile}--><!--{sfSetErrorStyle}--><!--{/if}-->/></td>
    </tr>
    <tr>
      <th>電話番号</th>
      <td colspan="3"><!--{if $arrErr.tel}--><span class="attention"><!--{$arrErr.tel}--></span><br /><!--{/if}--><input type="text" name="tel" maxlength="<!--{$smarty.const.TEL_LEN}-->" value="<!--{$arrForm.tel|escape}-->" size="60" class="box60" /></td>
    </tr>
    <tr>
      <th>職業</th>
      <td colspan="3"><!--{html_checkboxes name="job" options=$arrJob separator="&nbsp;" selected=$arrForm.job}--></td>
    </tr>
    <tr>
      <th>購入金額</th>
      <td><!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><span class="attention"><!--{$arrErr.buy_total_from}--><!--{$arrErr.buy_total_to}--></span><br /><!--{/if}--><input type="text" name="buy_total_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_total_from|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円 ～ <input type="text" name="buy_total_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_total_to|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円</td>
      <th>購入回数</th>
      <td><!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><span class="attention"><!--{$arrErr.buy_times_from}--><!--{$arrErr.buy_times_to}--></span><br /><!--{/if}--><input type="text" name="buy_times_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_times_from|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 回 ～ <input type="text" name="buy_times_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_times_to|escape}-->" size="6" class="box6" <!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 回</td>
    </tr>
    <tr>
      <th>登録・更新日</th>
      <td colspan="3">
        <!--{if $arrErr.start_year || $arrErr.end_year}--><span class="attention"><!--{$arrErr.start_year}--><!--{$arrErr.end_year}--></span><br /><!--{/if}-->
        <select name="start_year" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.start_year}-->
        </select>年
        <select name="start_month" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.start_month}-->
        </select>月
        <select name="start_day" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.start_day}-->
        </select>日～
        <select name="end_year" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.end_year}-->
        </select>年
        <select name="end_month" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.end_month}-->
        </select>月
        <select name="end_day" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.end_day}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th>最終購入日</th>
      <td colspan="3">
        <!--{if $arrErr.buy_start_year || $arrErr.buy_end_year}--><span class="attention"><!--{$arrErr.buy_start_year}--><!--{$arrErr.buy_end_year}--></span><br /><!--{/if}-->
        <select name="buy_start_year" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
          <option value="" selected="selected">----</option>
          <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR)  selected=$arrForm.buy_start_year}-->
        </select>年
        <select name="buy_start_month" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.buy_start_month}-->
        </select>月
        <select name="buy_start_day" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.buy_start_day}-->
        </select>日～
        <select name="buy_end_year" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
          <option value="" selected="selected">----</option>
          <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR)  selected=$arrForm.buy_end_year}-->
        </select>年
        <select name="buy_end_month" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.buy_end_month}-->
        </select>月
        <select name="buy_end_day" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.buy_end_day}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th>購入商品名</th>
      <td>
        <!--{if $arrErr.buy_product_name}--><span class="attention"><!--{$arrErr.buy_product_name}--></span><!--{/if}-->
        <span style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->">
        <input type="text" name="buy_product_name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.buy_product_name|escape}-->" size="30" class="box30" style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->"/>
        </span>
      </td>
      <th>購入商品コード</th>
      <td>
        <!--{if $arrErr.buy_product_code}--><span class="attention"><!--{$arrErr.buy_product_code}--></span><!--{/if}-->
        <input type="text" name="buy_product_code" value="<!--{$arrForm.buy_product_code}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" style="<!--{$arrErr.buy_product_code|sfGetErrorColor}-->" >
      </td>
    </tr>
    <tr>
      <th>カテゴリ</th>
      <td colspan="3">
        <select name="category_id" style="<!--{if $arrErr.category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
          <option value="">選択してください</option>
          <!--{html_options options=$arrCatList selected=$arrForm.category_id}-->
        </select>
      </td>
    </tr>
  </table>
  <div class="btn">
    検索結果表示件数
    <select name="page_rows">
      <!--{html_options options=$arrPageRows selected=$arrForm.page_rows}-->
    </select> 件
    <a class="btn_normal" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', '');"><span>この条件で検索する</span></a>
  </div>
</form>

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'resend_mail') }-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<!--{foreach from=$smarty.post key="key" item="item"}-->
<!--{if $key ne "mode" && $key ne "del_mode" && $key ne "edit_customer_id" && $key ne "del_customer_id" && $key ne "search_pageno" && $key ne "csv_mode" && $key ne "job" && $key ne "sex" && $key ne "status"}--><input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->"><!--{/if}-->
<!--{/foreach}-->
<!--{foreach from=$smarty.post.job key="key" item="item"}-->
<input type="hidden" name="job[]" value=<!--{$item}-->>
<!--{/foreach}-->
<!--{foreach from=$smarty.post.sex key="key" item="item"}-->
<input type="hidden" name="sex[]" value=<!--{$item}-->>
<!--{/foreach}-->
<!--{foreach from=$smarty.post.status key="key" item="item"}-->
<input type="hidden" name="status[]" value=<!--{$item}-->>
<!--{/foreach}-->
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="del_mode" value="" />
<input type="hidden" name="edit_customer_id" value="" />
<input type="hidden" name="del_customer_id" value="" />
<input type="hidden" name="search_pageno" value="<!--{$smarty.post.search_pageno|escape}-->" />
<input type="hidden" name="csv_mode" value="" />

  <h2>検索結果一覧</h2>
  <div class="btn">
    <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
    <!--検索結果-->
    <!--{if $smarty.const.ADMIN_MODE == '1'}-->
    <a class="btn_normal" href="javascript:;" onclick="fnModeSubmit('delete_all','','');">検索結果をすべて削除</a>
    <!--{/if}-->
    <a class="btn_normal" href="javascript:;" onclick="fnModeSubmit('csv','','');">CSV ダウンロード</a>
    <a class="btn_normal" href="javascript:;" onclick="location.href='../contents/csv.php?tpl_subno_csv=customer'">CSV 出力項目設定</a>
  </div>
  <!--{include file=$tpl_pager}-->

  <!--{if count($search_data) > 0}-->

  <!--検索結果表示テーブル-->
  <table class="list" id="customer-search-result">
    <tr>
      <th rowspan="2">種別</th>
      <th>顧客ID</th>
      <th rowspan="2">顧客名/(カナ)</th>
      <th rowspan="2">性別</th>
      <th>TEL</th>
      <th rowspan="2">編集</th>
      <th rowspan="2">削除</th>
    </tr>
    <tr>
      <th>都道府県</th>
      <th>メールアドレス</th>
    </tr>
    <!--{foreach from=$search_data item=row}-->
      <tr>
        <td class="center" rowspan="2"><!--{if $row.status eq 1}-->仮<!--{else}-->本<!--{/if}--></td>
        <td><!--{$row.customer_id|escape}--></td>
        <td rowspan="2"><!--{$row.name01|escape}--> <!--{$row.name02|escape}-->(<!--{$row.kana01|escape}--> <!--{$row.kana02|escape}-->)</td>
        <td class="center" rowspan="2"><!--{$arrSex[$row.sex]|escape}--></td>
        <td><!--{$row.tel01|escape}-->-<!--{$row.tel02|escape}-->-<!--{$row.tel03|escape}--></td>
        <td class="center" rowspan="2"><span class="icon_edit"><a href="#" onclick="return fnEdit('<!--{$row.customer_id|escape}-->');">編集</a></span></td>
        <td class="center" rowspan="2"><span class="icon_delete"><a href="#" onclick="return fnDelete('<!--{$row.customer_id|escape}-->');">削除</a></span></td>
      </tr>
      <tr>
        <td><!--{assign var=pref value=$row.pref}--><!--{$arrPref[$pref]}--></td>
        <td><!--{mailto address=$row.email encode="javascript"}--></a><!--{if $row.status eq 1}--><br /><a href="#" onclick="return fnReSendMail('<!--{$row.customer_id|escape}-->');">仮登録メール再送</a><!--{/if}--></td>
      </tr>
    <!--{/foreach}-->
  </table>
  <!--検索結果表示テーブル-->

  <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->

<!--{/if}-->
</div>
