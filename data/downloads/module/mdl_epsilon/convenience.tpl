<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<!--購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow03.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->
		
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/confirm_title.jpg" width="700" height="40" alt="ご入力内容のご確認"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記ご注文内容で送信してもよろしいでしょうか？<br>
				よろしければ、一番下の「<!--{if $payment_type != ""}-->次へ<!--{else}-->ご注文完了ページへ<!--{/if}-->」ボタンをクリックしてください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
			<input type="hidden" name="mode" value="confirm">
			<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
			<tr>
				<td bgcolor="#cccccc">
				<!--お支払方法・お届け時間の指定・その他お問い合わせここから-->		
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="100" align="center" bgcolor="#f0f0f0" class="fs12">選択</td>
						<td width="507" bgcolor="#f0f0f0" class="fs12">コンビニの種類</td>
					</tr>
					<!--{foreach key=key item=item from=$arrConv}-->
					<tr>
						<label for="<!--{$key}-->">
							<td align="center" bgcolor="#ffffff" class="fs12"><input type="radio" name="convenience" id="<!--{$key}-->" value="<!--{$key}-->" style="<!--{$arrErr.convenience|sfGetErrorColor}-->"></td>
							<td bgcolor="#ffffff" class="fs12"><!--{$item|escape}--></td>
						</label>
					</tr>
					<!--{/foreach}-->
				</table>
				<!--お支払方法・お届け時間の指定・その他お問い合わせここまで-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<a href="<!--{$smarty.server.PHP_SELF}-->" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif',back03)" onclick="fnModeSubmit('return', '', ''); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03"/></a>
					<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<!--{if $payment_type != ""}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif" width="150" height="30" alt="次へ" border="0" name="next" id="next" />
					<!--{else}-->
						<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif" width="150" height="30" alt="ご注文完了ページへ" border="0" name="next" id="next" />
					<!--{/if}-->
				</td>
			</tr>
			</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->