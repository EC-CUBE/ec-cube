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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<input type="hidden" name="review_id" value="<!--{$arrForm.review_id|h}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<div id="products" class="contents-main">

    <!--▼編集テーブルここから-->
    <table>
        <tr>
            <th>商品名</th>
            <td><!--{$arrForm.name|h}-->
            <input type="hidden" name="name" value="<!--{$arrForm.name|h}-->">
            </td>
        </tr>
        <tr>
            <th>投稿日</th>
            <td><!--{$arrForm.create_date|sfDispDBDate}-->
            <input type="hidden" name="create_date" value="<!--{$arrForm.create_date|h}-->">
            </td>
        </tr>
        <tr>
            <th>レビュー表示</th>
            <td>
                <!--{if $arrErr.status}--><span class="attention"><!--{$arrErr.status}--></span><!--{/if}-->
                <input type="radio" name="status" value="2" <!--{if $arrForm.status eq 2}-->checked<!--{/if}-->>非表示<!--{if $arrForm.status eq 2 && !$tpl_status_change}--><!--{else}--><input type="radio" name="status" value="1" <!--{if $arrForm.status eq 1}-->checked<!--{/if}-->>表示<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>投稿者名 <span class="attention">*</span></th>
            <td>
                <!--{if $arrErr.reviewer_name}--><span class="attention"><!--{$arrErr.reviewer_name}--></span><!--{/if}-->
                <input type="text" class="box60" name="reviewer_name" value="<!--{$arrForm.reviewer_name|h}-->" style="<!--{$arrErr.reviewer_name|sfGetErrorColor}-->" size=30>
            </td>
        </tr>
        <tr>
            <th>投稿者URL</th>
            <td>
                <!--{if $arrErr.reviewer_url}--><span class="attention"><!--{$arrErr.reviewer_url}--></span><!--{/if}-->
                <input type="text" class="box60" name="reviewer_url" maxlength="<!--{$smarty.const.URL_LEN}-->" value="<!--{$arrForm.reviewer_url|h}-->" style="<!--{$arrErr.reviewer_url|sfGetErrorColor}-->" size=30>
            </td>
        </tr>
        <tr>
            <th>性別</th>
            <td><!--{html_radios_ex name="sex" options=$arrSex selected=$arrForm.sex}--></td>
        </tr>
        <tr>
            <th>おすすめレベル <span class="attention">*</span></th>
            <td>
                <!--{assign var=key value="recommend_level"}-->
                <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
                <option value="" selected="selected">選択してください</option>
                <!--{html_options options=$arrRECOMMEND selected=$arrForm[$key]}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>タイトル <span class="attention">*</span></th>
            <td>
                <!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
                <input type="text" class="box60" name="title" value="<!--{$arrForm.title|h}-->" style="<!--{$arrErr.title|sfGetErrorColor}-->" size=30><span class="attention">
            </td>
        </tr>
        <tr>
            <th>コメント <span class="attention">*</span></th>
            <td>
                <!--{if $arrErr.comment}--><span class="attention"><!--{$arrErr.comment}--></span><!--{/if}-->
                <textarea name="comment" rows="20" cols="60" class="area60" wrap="soft" style="<!--{$arrErr.comment|sfGetErrorColor}-->" ><!--{$arrForm.comment|h}--></textarea>
            </td>
        </tr>
    </table>
    <!--▲編集テーブルここまで-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.action='./review.php'; fnModeSubmit('search','',''); return false;" ><span class="btn-prev">検索画面に戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('complete','',''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
</div>
</form>
