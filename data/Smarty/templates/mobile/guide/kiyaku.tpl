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
<center>ご利用規約(<!--{$tpl_kiyaku_index+1}-->/<!--{$tpl_kiyaku_last_index+1}-->)</center>

<hr>

<!-- ▼本文 ここから -->
<font color="#ff0000"><!--{$tpl_kiyaku_title|escape}--></font><br>
<!--{$tpl_kiyaku_text|escape}--><br>
<!-- ▲本文 ここまで -->

<!--{if !$tpl_kiyaku_is_first || !$tpl_kiyaku_is_last}-->
<br>
<!--{if !$tpl_kiyaku_is_first}-->
<a href="kiyaku.php?page=<!--{$tpl_kiyaku_index-1}-->" accesskey="1"><!--{1|numeric_emoji}-->戻る</a><br>
<!--{/if}-->
<!--{if !$tpl_kiyaku_is_last}-->
<a href="kiyaku.php?page=<!--{$tpl_kiyaku_index+1}-->" accesskey="2"><!--{2|numeric_emoji}-->進む</a><br>
<!--{/if}-->
<!--{/if}-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
