<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼NAVI-->
<table width="170" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<!--{if $tpl_mypageno == 'index'}-->
			<td><a href="./index.php"><img src="/img/mypage/navi01_on.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01"></a></td>
		<!--{else}-->
			<td><a href="./index.php" onmouseover="chgImg('/img/mypage/navi01_on.jpg','m_navi01');" onmouseout="chgImg('/img/mypage/navi01.jpg','m_navi01');"><img src="/img/mypage/navi01.jpg" width="170" height="30" alt="購入履歴一覧" border="0" name="m_navi01"></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_mypageno == 'change'}-->
			<td><a href="./change.php"><img src="/img/mypage/navi02_on.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02"></a></td>
		<!--{else}-->
			<td><a href="./change.php" onmouseover="chgImg('/img/mypage/navi02_on.jpg','m_navi02');" onmouseout="chgImg('/img/mypage/navi02.jpg','m_navi02');"><img src="/img/mypage/navi02.jpg" width="170" height="30" alt="会員登録内容変更" border="0" name="m_navi02"></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_mypageno == 'delivery'}-->
			<td><a href="./delivery.php"><img src="/img/mypage/navi03_on.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03"></a></td>
		<!--{else}-->
			<td><a href="./delivery.php" onmouseover="chgImg('/img/mypage/navi03_on.jpg','m_navi03');" onmouseout="chgImg('/img/mypage/navi03.jpg','m_navi03');"><img src="/img/mypage/navi03.jpg" width="170" height="30" alt="お届け先追加・変更" border="0" name="m_navi03"></a></td>
		<!--{/if}-->
	</tr>
	<tr>
		<!--{if $tpl_mypageno == 'refusal'}-->
			<td><a href="./refusal.php"><img src="/img/mypage/navi04_on.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04"></a></td>
		<!--{else}-->
			<td><a href="./refusal.php" onmouseover="chgImg('/img/mypage/navi04_on.jpg','m_navi04');" onmouseout="chgImg('/img/mypage/navi04.jpg','m_navi04');"><img src="/img/mypage/navi04.jpg" width="170" height="30" alt="退会手続き" border="0" name="m_navi04"></a></td>
		<!--{/if}-->
	</tr>
</table>

<table><tr><td height="15"></td></tr></table>

<!-- 現在のポイント ここから -->
<!--{if $point_disp !== false}-->
<table width="170" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr align="center">
		<td class="fs12" bgcolor="#f0d0a0">
		<table width="170" border="0" cellspacing="3" cellpadding="10" summary=" ">
			<tr align="center">
				<td class="fs12" bgcolor="#ffffff">
				ようこそ <br/>
				<!--{$CustomerName1|escape}--> <!--{$CustomerName2|escape}-->様<br>
				現在の所持ポイントは<span class="redst"><!--{$CustomerPoint|number_format|escape|default:"0"}-->pt</span>です。
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<!--{/if}-->
<!-- 現在のポイント ここまで -->

<!--▲NAVI-->
