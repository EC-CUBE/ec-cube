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
			<tr><td height="20"></td></tr>
			<tr valign="top">
				<!--▼CONTENTS-->

				<td>
				<div id="maintitle"><img src="../img/shopping/conveni_title.jpg" width="700" height="40" alt="コンビニ決済" /></div>
				<div class="fs12n" id="comment01">下記から、お支払いするコンビニをご選択くださいませ。<br />
				選択後、一番下の「ご注文完了ページへ」ボタンをクリックしてください。</div>
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
				<input type="hidden" name="mode" value="complete">
				<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
				<span class="red12st"><!--{$arrErr.convenience}--></span>
				<table cellspacing="1" cellpadding="8" summary=" " id="frame">
					<tr class="fs12n">
						<td id="select">選択</td>
						<td id="payment">コンビニの種類</td>
					</tr>
					<!--{foreach key=key item=item from=$arrConv}-->
					<tr>
						<td id="select_c"><input type="radio" name="convenience" id="<!--{$key}-->" value="<!--{$key}-->" style="<!--{$arrErr.convenience|sfGetErrorColor}-->"></td>
						<label for="<!--{$key}-->"><td class="fs12n" id="payment_c"><!--{$item|escape}--></td></label>
					</tr>
					<!--{/foreach}-->
				</table>
				<div class="red12" id="comment02">※「ご注文完了ページへ」をクリック後、完了ページが表示されるまでお待ちください。</div>
				<div id="button">
				<!--「戻る」「登録」-->
				<a href="<!--{$smarty.server.PHP_SELF}-->" onmouseover="chgImg('/img/button/back03_on.gif','back03')" onmouseout="chgImg('/img/button/back03.gif','back03')" onclick="fnModeSubmit('return', '', ''); return false;" /><img src="/img/button/back03.gif" width="110" height="30" alt="戻る" border="0" name="back03" id="back03" ></a><img src="../img/_.gif" width="20" height="" alt="" /><input type="image" onmouseover="chgImgImageSubmit('../img/shopping/complete_on.gif',this)" onmouseout="chgImgImageSubmit('../img/shopping/complete.gif',this)" src="../img/shopping/complete.gif" width="170" height="30" alt="ご注文完了ページへ" border="0" name="complete" id="complete" />
				</div>
				</form>
				
				<table cellspacing="0" cellpadding="0" summary=" " id="verisign">
					<tr>
						<td><script src=https://seal.verisign.com/getseal?host_name=secure.tokado.jp&size=S&use_flash=YES&use_transparent=NO&lang=ja></script></td>
						<td><img src="../img/_.gif" width="10" height="1" alt="" /></td>
						<td class="fs10">トーカ堂インターネットショッピングでは、通信の安全性を確保するセキュリティモードを設定しています。「暗号化(SSL)」を選択すると、送受信するデータが暗号化され、漏洩の危険性が低くなります。また、日本ベリサイン社によって通信サーバが認証されるため、なりすましなどによるID・パスワードの盗用の可能性も低減できます。</td>
					</tr>

				</table>
				</td>
				<!--▲ONTENTS-->	
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="../img/_.gif" width="39" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" />

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
