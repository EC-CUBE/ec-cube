<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<center>お届け先指定</center>

<hr>

<!--{if $arrErr.deli != ""}-->
<font color="#ff0000"><!--{$arrErr.deli}--></font>
<!--{/if}-->

<!--▼CONTENTS-->
<!--{section name=cnt loop=$arrAddr}-->
<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="deli" value="<!--{$smarty.section.cnt.iteration}-->">
<!--{if $smarty.section.cnt.first}-->
<input type="hidden" name="mode" value="customer_addr">
<input type="hidden" name="other_deliv_id" value="">
<!--{else}-->
<input type="hidden" name="mode" value="other_addr">
<input type="hidden" name="other_deliv_id" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
<!--{/if}-->
■お届け先<!--{$smarty.section.cnt.iteration}--><br>
〒<!--{$arrAddr[cnt].zip01}-->-<!--{$arrAddr[cnt].zip02}--><br>
<!--{assign var=key value=$arrAddr[cnt].pref}--><!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|h}--><br>
<!--{if $arrAddr[cnt].addr02 != ""}-->
<!--{$arrAddr[cnt].addr02|h}--><br>
<!--{/if}-->
<center><input type="submit" value="ここに送る"></center>
</form>
<!--{/section}-->

<br>

■その他のお届け先を指定<br>
<form method="get" action="deliv_addr.php">
<center><input type="submit" value="新規登録"></center>
</form>
<!--▲CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URL_PATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URL_PATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
