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

<style type="text/css">
    th {
        width: auto;
    }
</style>

<!--{if count($arrErr) >= 1}-->
    <div class="attention">
        <!--{foreach from=$arrErr item=err}-->
            <!--{$err}-->
        <!--{/foreach}-->
    </div>
<!--{/if}-->

<form action="?" name="form1" style="margin-bottom: 1ex;">
    <!--{assign var=key value="log"}-->
    <select name="<!--{$key|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrLogList selected=$arrForm[$key]}-->
    </select>
    <!--{assign var=key value="line_max"}-->
    直近の<input type="text" name="<!--{$key|h}-->" value="<!--{$arrForm[$key].value|h}-->" size="6" maxlength="<!--{$arrForm[$key].length|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />行
    <a class="btn-normal" href="javascript:;" onclick="form1.submit(); return false;"><span>読み込む</span></a>
</form>

<table class="list log">
    <tr>
        <th>日時</th>
        <th>パス</th>
        <th>内容</th>
    </tr>
    <!--{foreach from=$tpl_ec_log item=line}-->
        <tr>
            <td class="date"><!--{$line.date|h}--></td>
            <td class="path"><!--{$line.path|h}--></td>
            <td class="body"><!--{$line.body|h|nl2br}--></td>
        </tr>
    <!--{/foreach}-->
</table>
