<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

<!--{strip}-->
    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
    	<input type="hidden" name="mode" value="complete">
    	<!--{foreach from=$arrForm key=key item=item}-->
    		<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
    	<!--{/foreach}-->
    	下記の内容でご登録してもよろしいですか？<br>
    	<br>

    	【個人情報】<br>
    	<!--{$arrForm.name01|h}-->　<!--{$arrForm.name02|h}--><br>
    	<!--{$arrForm.kana01|h}-->　<!--{$arrForm.kana02|h}--><br>
    	<!--{if $arrForm.sex eq 1}-->男性<!--{else}-->女性<!--{/if}--><br>
    	<!--{$arrJob[$arrForm.job]|h}--><br>
    	<!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日生まれ<!--{else}-->生年月日 未登録<!--{/if}--><br>
    	〒<!--{$arrForm.zip01|h}--> - <!--{$arrForm.zip02|h}--><br>
    	<!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--><br>
    	<!--{$arrForm.tel01|h}-->-<!--{$arrForm.tel02|h}-->-<!--{$arrForm.tel03|h}--><br>
    	<!--{if $arrForm.fax01 > 0}-->
    	    <!--{$arrForm.fax01|h}-->-<!--{$arrForm.fax02|h}-->-<!--{$arrForm.fax03|h}--><br>
    	<!--{/if}-->
    	<br>

    	【携帯ﾒｰﾙｱﾄﾞﾚｽ】<br>
    	<!--{$arrForm.email_mobile|default:"未登録"|h}--><br>
    	<br>
    	
    	【ﾊﾟｽﾜｰﾄﾞ確認用質問】<br>
    	<!--{$arrReminder[$arrForm.reminder]|h}--><br>
    	<br>

    	【質問の答え】<br>
    	<!--{$arrForm.reminder_answer|h}--><br>
    	<br>

    	【ﾒｰﾙﾏｶﾞｼﾞﾝﾞ】<br>
    	<!--{if $arrForm.mailmaga_flg eq 2}-->希望する<!--{else}-->希望しない<!--{/if}--><br>
    	<br>

    	<center>
        	<input type="submit" name="submit" value="変更"><br>
        	<input type="submit" name="return" value="戻る">
    	</center>
    </form>
<!--{/strip}-->
