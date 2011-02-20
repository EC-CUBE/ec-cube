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

<script type="text/javascript">
<!--

    function fnReturn() {
        document.search_form.action = './<!--{$smarty.const.DIR_INDEX_PATH}-->';
        document.search_form.submit();
        return false;
    }
//-->
</script>

<form name="search_form" method="post" action="">
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "del_mode" && $key ne "edit_customer_id" && $key ne "del_customer_id" && $key ne "csv_mode" && $key ne "job" && $key ne "sex"}--><input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->"><!--{/if}-->
    <!--{/foreach}-->

    <!--{foreach from=$arrSearchData.job key="key" item="item"}-->
        <input type="hidden" name="job[]" value="<!--{$item}-->" />
    <!--{/foreach}-->
    <!--{foreach from=$arrSearchData.sex key="key" item="item"}-->
        <input type="hidden" name="sex[]" value="<!--{$item}-->" />
    <!--{/foreach}-->
</form>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="complete" />
<!--{foreach from=$arrForm key=key item=item}-->
<!--{if $key ne "mode" && $key ne "subm"}-->
<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
<!--{/if}-->
<!--{/foreach}-->
	<div id="complete">
		<div class="complete-top"></div>
		<div class="contents">
			<div class="message">
				登録が完了致しました。
			</div>
		</div>
		<div class="btn-area-top"></div>
		<div class="btn-area">
			<ul>
				<li><!--{* TODO *}--><a class="btn-action" href="javascript:;" onclick="return fnReturn();"><span class="btn-prev">検索結果へ戻る</span></a></li>
				<li><a class="btn-action" href="./edit.php"><span class="btn-next">続けて登録を行う</span></a></li>
			</ul>
		</div>
		<div class="btn-area-bottom"></div>
	</div>
</form>
