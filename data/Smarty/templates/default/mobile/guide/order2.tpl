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
<center>特定商取引に関する法律に基づく表記(2/3)</center>

<hr>

<!-- ▼本文 ここから -->
[emoji:e44]<font color="#800000">商品以外の必要代金</font><br>
<!--{$arrRet.law_term01|escape|nl2br}--><br>
[emoji:115]<font color="#800000">注文方法</font><br>
<!--{$arrRet.law_term02|escape|nl2br}--><br>
[emoji:e10]<font color="#800000">支払方法</font><br>
<!--{$arrRet.law_term03|escape|nl2br}--><br>
<!-- ▲本文 ここまで -->

<br>
<a href="order.php?page=1" accesskey="1"><!--{1|numeric_emoji}-->戻る</a><br>
<a href="order.php?page=3" accesskey="3"><!--{3|numeric_emoji}-->進む</a><br>
<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
