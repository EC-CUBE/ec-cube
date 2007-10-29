<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
 *}-->
<p>アップデート可能なファイル一覧です。</p>

<table>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="filename" value="">
<input type="hidden" name="mode" value="">
<tr>
<td>ファイル名</td>
<td>更新日時</td>
<td>ファイルサイズ</td>
</tr>
<!--{section name=cnt loop=$arrFile}-->
<tr>
<td><a href="#" onclick="fnModeSubmit('download', 'filename', '<!--{$arrFile[cnt].filename}-->');"><!--{$arrFile[cnt].filename|escape}--></a></td>
<td><!--{$arrFile[cnt].date|escape}--></td>
<td><!--{$arrFile[cnt].filesize|escape}--></td>
</tr>
<!--{/section}-->
</form>
</table>
