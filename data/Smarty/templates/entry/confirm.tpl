<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
				<input type="hidden" name="mode" value="complete">
			<!--{foreach from=$list_data key=key item=item}-->
				<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
			<!--{/foreach}-->
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/entry/title.jpg" width="580" height="40" alt="会員登録"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記の内容で送信してもよろしいでしょうか？<br>
				よろしければ、一番下の「会員登録完了へ」ボタンをクリックしてください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--入力フォームここから-->
				<table width="580" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12">お名前<span class="red">※</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12"><!--{$list_data.name01|escape}-->　<!--{$list_data.name02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">お名前（フリガナ）<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$list_data.kana01|escape}-->　<!--{$list_data.kana02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">郵便番号<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12">〒<!--{$list_data.zip01|escape}--> - <!--{$list_data.zip02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">住所<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$arrPref[$list_data.pref]|escape}--><!--{$list_data.addr01|escape}--><!--{$list_data.addr02|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">電話番号<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$list_data.tel01|escape}--> - <!--{$list_data.tel02|escape}--> - <!--{$list_data.tel03|escape}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
						<td bgcolor="#ffffff" class="fs12"><!--{if strlen($list_data.fax01) > 0 && strlen($list_data.fax02) > 0 && strlen($list_data.fax03) > 0}--><!--{$list_data.fax01|escape}--> - <!--{$list_data.fax02|escape}--> - <!--{$list_data.fax03|escape}--><!--{else}-->未登録<!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">メールアドレス<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><a href="mailto:<!--{$list_data.email|escape}-->"><!--{$list_data.email|escape}--></a></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">性別<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><!--{if $list_data.sex eq 1}-->男性<!--{else}-->女性<!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0"  class="fs12n">職業</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{$arrJob[$list_data.job]|escape|default:"未登録"}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">生年月日</td>
						<td bgcolor="#ffffff" class="fs12n"><!--{if strlen($list_data.year) > 0 && strlen($list_data.month) > 0 && strlen($list_data.day) > 0}--><!--{$list_data.year|escape}-->年<!--{$list_data.month|escape}-->月<!--{$list_data.day|escape}-->日<!--{else}-->未登録<!--{/if}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" ><span class="fs12">希望するパスワード<span class="red">※</span></span><br>
						<span class="fs10">パスワードは購入時に必要です</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{$passlen}--></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">パスワードを忘れた時のヒント<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">質問：</td>
								<td class="fs12n"><!--{$arrReminder[$list_data.reminder]|escape}--></td>
							</tr>
							<tr>
								<td class="fs12n">答え：</td>
								<td class="fs12n"><!--{$list_data.reminder_answer|escape}--></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">メールマガジン送付について<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><!--{if $list_data.mailmaga_flg eq 1}-->HTMLメール＋テキストメールを受け取る<!--{elseif $list_data.mailmaga_flg eq 2}-->テキストメールを受け取る<!--{else}-->受け取らない<!--{/if}--></td>
					</tr>
				</table>
				<!--入力フォームここまで-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','back')" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','back')"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back" id="back" /></a>
					<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/entry/b_entrycomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/entry/b_entrycomp.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/entry/b_entrycomp.gif" width="150" height="30" alt="送信" border="0" name="send" id="send" />
				</td>
			</tr>
		</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
