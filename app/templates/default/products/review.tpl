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
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="お客様の声書き込み（入力ページ）"}-->

<article id="popup_review" class="window_area">
    <h1 class="title">お客様の声書き込み</h1>
    <p class="windowtext">以下の商品について、お客様のご意見、ご感想をどしどしお寄せください。<br />
        「<span class="attention">※</span>」印は入力必須項目です。<br />
        ご入力後、一番下の「確認ページへ」ボタンをクリックしてください。</p>
    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="product_id" value="<!--{$arrForm.product_id|h}-->" />
		<!--お客様の声書き込み-->
        <dl class="table">
			<dt>商品名</dt>
			<dd><!--{$arrForm.name|h}-->&nbsp;</dd>
			<dt>投稿者名<span class="attention">※</span></dt>
			<dd><span class="attention"><!--{$arrErr.reviewer_name}--></span><input type="text" name="reviewer_name" value="<!--{$arrForm.reviewer_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.reviewer_name|sfGetErrorColor}-->" class="box350" />&nbsp;</dd>
			<dt>投稿者URL</dt>
			<dd><span class="attention"><!--{$arrErr.reviewer_url}--></span><input type="text" name="reviewer_url" value="<!--{$arrForm.reviewer_url|h}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.reviewer_url|sfGetErrorColor}-->" class="box350" />&nbsp;</dd>
			<dt>性別</dt>
			<dd>
				<input type="radio" name="sex" id="man" value="1" <!--{if $arrForm.sex eq 1}--> checked="checked"<!--{/if}--> /><label for="man">男性</label>&nbsp;
				<input type="radio" name="sex" id="woman" value="2" <!--{if $arrForm.sex eq 2}--> checked="checked"<!--{/if}--> /><label for="woman">女性</label>
			&nbsp;</dd>
			<dt>おすすめレベル<span class="attention">※</span></dt>
			<dd>
				<span class="attention"><!--{$arrErr.recommend_level}--></span>
				<select name="recommend_level" style="<!--{$arrErr.recommend_level|sfGetErrorColor}-->">
					<option value="" selected="selected">選択してください</option>
						<!--{html_options options=$arrRECOMMEND selected=$arrForm.recommend_level}-->
				</select>
			&nbsp;</dd>
			<dt>タイトル<span class="attention">※</span></dt>
			<dd>
				<span class="attention"><!--{$arrErr.title}--></span>
				<input type="text" name="title" value="<!--{$arrForm.title|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.title|sfGetErrorColor}-->" class="box350" />
			&nbsp;</dd>
			<dt>コメント<span class="attention">※</span></dt>
			<dd>
				<span class="attention"><!--{$arrErr.comment}--></span>
				<textarea name="comment" cols="50" rows="10" style="<!--{$arrErr.comment|sfGetErrorColor}-->" class="w95p"><!--{"\n"}--><!--{$arrForm.comment|h}--></textarea>
			&nbsp;</dd>
        </dl>
        <div class="btn_area">
            <ul>
                <li><input type="submit" class="btn btn-success" value="確認ページへ" name="conf" id="conf" /></li>
            </ul>
        </div>
    </form>
</article>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->