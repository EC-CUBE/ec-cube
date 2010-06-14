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
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="complete" />
<!--{foreach from=$arrForm key=key item=item}-->
<!--{if $key ne "mode" && $key ne "subm"}-->
<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->" />
<!--{/if}-->
<!--{/foreach}-->
<div id="customer" class="contents-main">
  <h2>顧客編集</h2>
  <div class="message">登録が完了致しました。</div>
</div>
</form>
