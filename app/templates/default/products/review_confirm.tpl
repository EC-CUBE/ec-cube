<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="お客様の声書き込み（確認ページ）"}-->

<article id="popup_review_confirm" class="window_area">
    <h1 class="title">お客様の声書き込み</h1>
    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />
        <!--{foreach from=$arrForm key=key item=item}-->
            <!--{if $key ne "mode"}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->

		<!--お客様の声書き込み-->
        <dl class="table">
			<dt>商品名</dt>
			<dd><!--{$arrForm.name|h}-->&nbsp;</dd>
			<dt>投稿者名</dt>
			<dd><!--{$arrForm.reviewer_name|h}-->&nbsp;</dd>
			<dt>投稿者URL</dt>
			<dd><!--{$arrForm.reviewer_url|h}-->&nbsp;</dd>
			<dt>性別</dt>
			<dd><!--{if $arrForm.sex eq 1}-->男性<!--{elseif $arrForm.sex eq 2}-->女性<!--{/if}-->&nbsp;</dd>
			<dt>おすすめレベル</dt>
			<dd><span class="recommend_level"><!--{$arrRECOMMEND[$arrForm.recommend_level]}--></span>&nbsp;</dd>
			<dt>タイトル</dt>
			<dd><!--{$arrForm.title|h}-->&nbsp;</dd>
			<dt>コメント</dt>
			<dd><!--{$arrForm.comment|h|nl2br}-->&nbsp;</dd>
		</dl>
        <div class="btn_area">
            <ul>
                <li><input type="submit" onclick="mode.value='return';" class="btn btn-default" value="戻る" name="back" id="back" /></li>
                <li><input type="submit" class="btn btn-success" value="送信" name="send" id="send" /></li>
            </ul>
        </div>
    </form>
</article>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->