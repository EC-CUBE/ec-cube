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
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`/adminparts/form_customer_search.tpl"}-->
        <tr>
            <th>配信形式</th>
            <td>
                <!--{if $arrErr.htmlmail}--><span class="attention"><!--{$arrErr.htmlmail}--></span><br /><!--{/if}-->
                <!--{html_radios name="htmlmail" options=$arrHtmlmail separator="&nbsp;" selected=$list_data.htmlmail}-->
            </td>
        </tr>
        <tr>
            <th>配信メールアドレス種別</th>
            <td colspan="3">
                <!--{html_radios name="mail_type" options=$arrMailType separator="<br />" selected=$list_data.mail_type}-->
            </td>
        </tr>
    </table>
    <!--{* 検索条件設定テーブルここまで *}-->

    <div class="btn-area">
        <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a>
    </div>
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'back')}-->

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" />
<input type="hidden" name="result_email" value="" />
<!--{foreach key=key item=val from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$val|h}-->" />
<!--{/foreach}-->

    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;"><span>検索結果をすべて削除</span></a>
        <!--{/if}-->
        <!--{if $tpl_linemax > 0}-->
            <a class="btn-normal" href="javascript:;" onclick="document.form1['mode'].value='input'; document.form1.submit(); return false;"><span>配信内容を設定する</span></a>
        <!--{/if}-->
    </div>
    <!--{if count($arrResults) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--検索結果表示テーブル-->
    <table class="list">
    <colgroup width="5%">
    <colgroup width="10%">
    <colgroup width="10%">
    <colgroup width="25%">
    <colgroup width="15%">
    <colgroup width="10%">
    <colgroup width="15%">
    <colgroup width="5%">
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

            <td><!--{$arrResults[i].name01|h}--> <!--{$arrResults[i].name02|h}--></td>
            <td><!--{$arrResults[i].email|h}--></td>
            <!--{assign var="key" value="`$arrResults[i].mailmaga_flg`"}-->
            <td class="center"><!--{$arrMAILMAGATYPE[$key]}--></td>
            <td><!--{$arrResults[i].create_date|sfDispDBDate}--></td>
            <!--{if $arrResults[i].customer_id != ""}-->
            <td class="center">-</td>
            <!--{else}-->
            <td class="center"><a href="?" onclick="fnFormModeSubmit('form1','delete','result_email','<!--{$arrResults[i].email|h}-->'); return false;">削除</a></td>
            <!--{/if}-->
        </tr>
        <!--{/section}-->
    </table>
    <!--検索結果表示テーブル-->
    <!--{/if}-->

</form>

<!--{/if}-->
</div>
