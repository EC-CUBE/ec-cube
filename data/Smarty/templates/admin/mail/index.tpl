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
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>配信先検索条件設定</h2>

    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`/adminparts/form_customer_search.tpl"}-->
        <tr>
            <th>配信形式</th>
            <td colspan="3">
                <!--{assign var=key value="search_htmlmail"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
                <!--{html_radios name=$key options=$arrHtmlmail separator="&nbsp;" selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>配信メールアドレス種別</th>
            <td colspan="3">
                <!--{assign var=key value="search_mail_type"}-->
                <!--{html_radios name=$key options=$arrMailType separator="<br />" selected=$arrForm[$key].value|default:1}-->
            </td>
        </tr>
    </table>
    <!--{* 検索条件設定テーブルここまで *}-->

    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <select name="<!--{$key}-->">
            <!--{html_options options=$arrPageRows selected=$arrForm[$key]}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'back')}-->

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<!--{foreach key=key item=item from=$arrHidden}-->
<!--{if is_array($item)}-->
    <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key}-->[]" value="<!--{$c_item|h}-->" />
    <!--{/foreach}-->
<!--{else}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/if}-->
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
    <col width="10%" />
    <col width="25%" />
    <col width="35%" />
    <col width="15%" />
    <col width="15%" />
        <tr>
            <th>会員ID</th>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>希望配信</th>
            <th>登録・更新日</th>
        </tr>
        <!--{section name=i loop=$arrResults}-->
        <tr>
            <td class="center"><!--{$arrResults[i].customer_id}--></td>
            <td><!--{$arrResults[i].name01|h}--> <!--{$arrResults[i].name02|h}--></td>
            <td><!--{$arrResults[i].email|h}--></td>
            <!--{assign var="key" value="`$arrResults[i].mailmaga_flg`"}-->
            <td class="center"><!--{$arrHtmlmail[$key]}--></td>
            <td class="center"><!--{$arrResults[i].update_date|sfDispDBDate}--></td>
        </tr>
        <!--{/section}-->
    </table>
    <!--検索結果表示テーブル-->
    <!--{/if}-->

</form>

<!--{/if}-->
</div>
