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
    <form name="form1" id="form1" method="post" action="?" utn>
    	<input type="hidden" name="mode" value="complete">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
    	<!--{foreach from=$arrForm key=key item=item}-->
    		<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
    	<!--{/foreach}-->
    	下記の内容でご登録してもよろしいですか？<br>
    	<br>

    	●お名前<br>
    	<!--{$arrForm.name01|h}-->　<!--{$arrForm.name02|h}--><br>

    	●お名前(フリガナ)<br>
    	<!--{$arrForm.kana01|h}-->　<!--{$arrForm.kana02|h}--><br>

    	●性別<br>
    	<!--{if $arrForm.sex eq 1}-->男性<!--{else}-->女性<!--{/if}--><br>

    	●職業<br>
    	<!--{if $arrForm.job}--><!--{$arrJob[$arrForm.job]|h}--><!--{else}-->未登録<!--{/if}--><br>

    	●生年月日<br>
    	<!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日生まれ<!--{else}-->未登録<!--{/if}--><br>

    	●住所<br>
    	〒<!--{$arrForm.zip01|h}--> - <!--{$arrForm.zip02|h}--><br>
    	<!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--><br>

    	●電話番号<br>
    	<!--{$arrForm.tel01|h}-->-<!--{$arrForm.tel02|h}-->-<!--{$arrForm.tel03|h}--><br>
    	
    	●ﾒｰﾙｱﾄﾞﾚｽ<br>
    	<!--{$arrForm.email|h}--><br>

    	●ﾊﾟｽﾜｰﾄﾞ確認用質問<br>
    	<!--{$arrReminder[$arrForm.reminder]|h}--><br>

    	●質問の答え<br>
    	<!--{$arrForm.reminder_answer|h}--><br>

    	●ﾒｰﾙﾏｶﾞｼﾞﾝ<br>
    	<!--{if $arrForm.mailmaga_flg eq 2}-->希望する<!--{else}-->希望しない<!--{/if}--><br>
    	<br>

    	<center>
        	<input type="submit" name="submit" value="登録"><br>
        	<input type="submit" name="return" value="戻る">
    	</center>
    </form>
<!--{/strip}-->
