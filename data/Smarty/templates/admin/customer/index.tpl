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
<!--

    function fnDelete(customer_id) {
        if (confirm('この会員情報を削除しても宜しいですか？')) {
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
//-->
</script>


<div id="customer" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />

    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table class="form">
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`/adminparts/form_customer_search.tpl"}-->
        <tr>
            <th>会員状態</th>
            <td colspan="3"><!--{html_checkboxes name="search_status" options=$arrStatus separator="&nbsp;" selected=$arrForm.search_status.value}--></td>
        </tr>
    </table>
    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <select name="search_page_max">
            <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
</form>
<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'resend_mail')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="edit_customer_id" value="" />
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
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;">CSV ダウンロード</a>
        <a class="btn-normal" href="javascript:;" onclick="location.href='../contents/csv.php?tpl_subno_csv=customer'">CSV 出力項目設定</a>
    </div>
    <!--{if count($arrData) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--検索結果表示テーブル-->
    <table class="list" id="customer-search-result">
        <col width="8%" />
        <col width="10%" />
        <col width="30%" />
        <col width="8%" />
        <col width="30%" />
        <col width="7%" />
        <col width="7%" />
        <tr>
            <th rowspan="2">種別</th>
            <th>会員ID</th>
            <th rowspan="2">お名前/(フリガナ)</th>
            <th rowspan="2">性別</th>
            <th>TEL</th>
            <th rowspan="2">編集</th>
            <th rowspan="2">削除</th>
        </tr>
        <tr>
            <th>都道府県</th>
            <th>メールアドレス</th>
        </tr>
        <!--{foreach from=$arrData item=row}-->
            <tr>
                <td class="center" rowspan="2"><!--{if $row.status eq 1}-->仮<!--{else}-->本<!--{/if}--></td>
                <td><!--{$row.customer_id|h}--></td>
                <td rowspan="2"><!--{$row.name01|h}--> <!--{$row.name02|h}--><br>(<!--{$row.kana01|h}--> <!--{$row.kana02|h}-->)</td>
                <td class="center" rowspan="2"><!--{$arrSex[$row.sex]|h}--></td>
                <td><!--{$row.tel01|h}-->-<!--{$row.tel02|h}-->-<!--{$row.tel03|h}--></td>
                <td class="center" rowspan="2"><span class="icon_edit"><a href="#" onclick="return fnEdit('<!--{$row.customer_id|h}-->');">編集</a></span></td>
                <td class="center" rowspan="2"><span class="icon_delete"><a href="#" onclick="return fnDelete('<!--{$row.customer_id|h}-->');">削除</a></span></td>
            </tr>
            <tr>
                <td><!--{assign var=pref value=$row.pref}--><!--{$arrPref[$pref]}--></td>
                <td><!--{mailto address=$row.email encode="javascript"}--></a><!--{if $row.status eq 1}--><br /><a href="#" onclick="return fnReSendMail('<!--{$row.customer_id|h}-->');">仮登録メール再送</a><!--{/if}--></td>
            </tr>
        <!--{/foreach}-->
    </table>
    <!--検索結果表示テーブル-->

    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->

<!--{/if}-->
</div>
