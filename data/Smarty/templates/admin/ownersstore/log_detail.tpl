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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="register" />

<div id="ownersstore" class="contents-main">

    <table class="form">
        <tr>
            <th><!--{t string="tpl_Module name_01"}--></th>
            <td><!--{$arrLogDetail.module_name|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Status_01"}--></th>
            <td><!--{if $arrLogDetail.error_flg}--><!--{t string="tpl_Failure_01"}--><!--{else}--><!--{t string="tpl_Success_01"}--><!--{/if}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Date_02"}--></th>
            <td><!--{$arrLogDetail.update_date|sfDispDBDate|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Backup path_01"}--></th>
            <td><!--{$arrLogDetail.buckup_path|wordwrap:100:"
":true|h|nl2br}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Details_01"}--></th>
            <td>
            <!--{$arrLogDetail.error|wordwrap:100:"
":true|h|nl2br}-->
            <!--{$arrLogDetail.ok|wordwrap:100:"
":true|h|nl2br}-->
            </td>
        </tr>
    </table>
    <div class="btn">
        <a class="btn-action" href='./log.php'><span class="btn-prev"><!--{t string="tpl_Return to list_01"}--></span></a>
    </div>
</div>
</form>
