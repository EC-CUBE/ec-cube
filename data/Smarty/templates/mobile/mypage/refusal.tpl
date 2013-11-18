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
    <!--{$CustomerName1|h}--> <!--{$CustomerName2|h}-->様、会員から退会をされますと、登録されているお届け先の情報など全て削除されますがよろしいでしょうか。<br>
    <br>
    <div align="center">
        <form action="?" method="post">
            <input type="hidden" name="mode" value="complete">
            <input type="hidden" name="refusal_transactionid" value="<!--{$refusal_transactionid}-->" />
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">

            <input type="submit" name="complete" value="退会する">
        </form>
    </div>
    <br>
<!--{/strip}-->
