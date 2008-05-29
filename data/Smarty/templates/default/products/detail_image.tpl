<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="拡大画像"}-->

<!--{if $tpl_width > 300}-->
  <!--{assign var=id value=bigimage}-->
<!--{else}-->
  <!--{assign var=id value=cartimage}-->
<!--{/if}-->
<div id="<!--{$id}-->"><a href="javascript:window.close()");><img src="<!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$tpl_image}-->" width="<!--{$tpl_width}-->" height="<!--{$tpl_height}-->" alt="<!--{$tpl_name}-->" /></a></div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
