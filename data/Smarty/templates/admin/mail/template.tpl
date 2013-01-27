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
<div class="btn">
    <a class="btn-action" href="./template_input.php"><span class="btn-next"><!--{t string="tpl_Add template_01"}--></span></a>
</div>
<!--{if count($arrTemplates) > 0}-->
<div id="mail" class="contents-main">
    <table class="list center">
    <col width="15%" />
    <col width="35%" />
    <col width="20%" />
    <col width="10%" />
    <col width="10%" />
    <col width="10%" />
        <tr>
            <th><!--{t string="tpl_Creation date_01"}--></th>
            <th><!--{t string="tpl_Subject_01"}--></th>
            <th><!--{t string="tpl_E-mail format_01"}--></th>
            <th><!--{t string="tpl_Edit_01"}--></th>
            <th><!--{t string="tpl_Remove_01"}--></th>
            <th><!--{t string="tpl_Preview_01"}--></th>
        </tr>
        <!--{section name=data loop=$arrTemplates}-->
        <tr>
            <td><!--{$arrTemplates[data].create_date|date_format:'%Y/%m/%d'|h}--></td>
            <td class="left"><!--{$arrTemplates[data].subject|h}--></td>
            <!--{assign var=type value=$arrTemplates[data].mail_method|h}-->
            <td><!--{$arrMagazineType[$type]}--></td>
            <td><a href="./template_input.php?mode=edit&amp;template_id=<!--{$arrTemplates[data].template_id}-->"><!--{t string="tpl_Edit_01"}--></a></td>
            <td><a href="#" onclick="fnDelete('?mode=delete&amp;id=<!--{$arrTemplates[data].template_id}-->'); return false;"><!--{t string="tpl_Remove_01"}--></a></td>
            <td><a href="#" onclick="win03('./preview.php?mode=template&amp;template_id=<!--{$arrTemplates[data].template_id}-->','preview','650','700'); return false;" target="_blank"><!--{t string="tpl_Preview_01"}--></a></td>
        </tr>
        <!--{/section}-->
    </table>
</div>
<!--{/if}-->
</form>
