<!--{*
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
 *}-->

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="お客様の声書き込み（確認ページ）"}-->

<div id="window_area">
    <h2 class="title">お客様の声書き込み</h2>
    <form name="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="complete" />
    <!--{foreach from=$arrForm key=key item=item}-->
        <!--{if $key ne "mode"}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->

    <table summary="お客様の声書き込み">
        <col width="30%" />
        <col width="70%" />
        <tr>
            <th>商品名</th>
            <td><!--{$arrForm.name|h}--></td>
        </tr>
        <tr>
            <th>投稿者名<span class="attention">※</span></th>
            <td><!--{$arrForm.reviewer_name|h}--></td>
        </tr>
        <tr>
            <th>投稿者URL</th>
            <td><!--{$arrForm.reviewer_url|h}--></td>
        </tr>
        <tr>
            <th>性別</th>
            <td><!--{if $arrForm.sex eq 1}-->男性<!--{elseif $arrForm.sex eq 2}-->女性<!--{/if}--></td>
        </tr>
        <tr>
            <th>おすすめレベル<span class="attention">※</span></th>
            <td><span class="recommend_level"><!--{$arrRECOMMEND[$arrForm.recommend_level]}--></span></td>
        </tr>
        <tr>
            <th>タイトル<span class="attention">※</span></th>
            <td><!--{$arrForm.title|h}--></td>
        </tr>
        <tr>
            <th>コメント<span class="attention">※</span></th>
            <td><!--{$arrForm.comment|h|nl2br}--></td>
        </tr>
    </table>
    <div class="btn_area">
        <ul>
            <li><input type="image" onclick=" mode.value='return';" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る"    name="back" id="back" /></li>
            <li><input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_complete_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_complete.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_complete.jpg" alt="送信" name="send" id="send" /></li>
        </ul>
    </div>
    </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
