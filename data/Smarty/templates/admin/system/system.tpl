<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<h2>概要</h2>
<table border="0" cellspacing="1" cellpadding="8" summary=" ">
    <!--{foreach from=$arrSystemInfo item=info}-->
        <tr>
            <th>
            <!--{$info.title|h}-->
            </th>
            <td>
            <!--{$info.value|h|nl2br}-->
            </td>
        </tr>
    <!--{/foreach}-->
</table>

<h2>PHP情報</h2>
<iframe src="?mode=info" height="500" frameborder="0" style="width: 100%;"></iframe>
