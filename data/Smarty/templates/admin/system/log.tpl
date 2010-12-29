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

<p>直近の<!--{$line_max}-->行</p>
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
