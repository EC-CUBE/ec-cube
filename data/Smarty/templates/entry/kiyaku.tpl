<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
		<!--▼MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" method="post" action="./payment.php">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/entry/agree_title.jpg" width="580" height="40" alt="ご利用規約"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>

				<td class="fs12"><span class="redst">【重要】 会員登録をされる前に、下記ご利用規約をよくお読みください。</span><br>
				規約には、本サービスを使用するに当たってのあなたの権利と義務が規定されております。<br>
				「規約に同意して会員登録をする」ボタン をクリックすると、あなたが本規約の全ての条件に同意したことになります。</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td>
				<textarea name="textfield" cols="80" rows="30" class="area80_2" readonly><!--{$tpl_kiyaku_text}--></textarea>
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td><a href="/index.php" onmouseover="chgImg('../img/entry/b_noagree_on.gif','b_noagree');" onmouseout="chgImg('../img/entry/b_noagree.gif','b_noagree');"><img src="<!--{$smarty.const.URL_DIR}-->img/entry/b_noagree.gif" width="180" height="30" alt="同意しない" border="0" name="b_noagree"></a>　 
                <a href="./index.php" onmouseover="chgImg('../img/entry/b_agree_on.gif','b_agree');" onmouseout="chgImg('../img/entry/b_agree.gif','b_agree');"><img src="<!--{$smarty.const.URL_DIR}-->img/entry/b_agree.gif" width="180" height="30" alt="規約に同意して会員登録" border="0" name="b_agree"></a></td>
			</tr>
		</form>
		</table>

		<!--▲MAIN ONTENTS-->