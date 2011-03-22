<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{if $arrErr.deli != ""}-->
<font color="#ff0000"><!--{$arrErr.deli}--></font>
<!--{/if}-->

<!--▼CONTENTS-->
<!--{section name=cnt loop=$arrAddr}-->
<form method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="deli" value="<!--{$smarty.section.cnt.iteration}-->">
<input type="hidden" name="mode" value="customer_addr">
<!--{if $smarty.section.cnt.first}-->
<input type="hidden" name="other_deliv_id" value="">
<input type="hidden" name="deliv_check" value="-1">
<!--{else}-->
<input type="hidden" name="other_deliv_id" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
<input type="hidden" name="deliv_check" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
<br>
<!--{/if}-->
■お届け先<!--{$smarty.section.cnt.iteration}--><br>
〒<!--{$arrAddr[cnt].zip01}-->-<!--{$arrAddr[cnt].zip02}--><br>
<!--{assign var=key value=$arrAddr[cnt].pref}--><!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|h}--><br>
<!--{if $arrAddr[cnt].addr02 != ""}-->
<!--{$arrAddr[cnt].addr02|h}--><br>
<!--{/if}-->
<!--{$arrAddr[cnt].name01}--> <!--{$arrAddr[cnt].name02}--><br>
<center><input type="submit" value="ここに送る"></center>
</form>
<!--{/section}-->

<br>

■新しいお届け先を追加する<br>
<form method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="ParentPage" value="<!--{$smarty.const.DELIV_URLPATH}-->">
<center><input type="submit" value="新規登録"></center>
</form>

<br>

■お届け先を複数指定する<br>
<form method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="mode" value="multiple">
<center><input type="submit" value="複数お届け先"></center>
</form>
