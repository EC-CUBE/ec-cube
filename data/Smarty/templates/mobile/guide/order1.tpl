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
<center>特定商取引に関する法律に基づく表記(1/3)</center>

<hr>

<!-- ▼本文 ここから -->
[emoji:39]<font color="#800000">販売業者</font><br>
<!--{$arrRet.law_company|escape}--><br>
[emoji:63]<font color="#800000">運営責任者</font><br>
<!--{$arrRet.law_manager|escape}--><br>
[emoji:38]<font color="#800000">住所</font><br>
〒<!--{$arrRet.law_zip01|escape}-->-<!--{$arrRet.law_zip02|escape}--><br>
<!--{$arrPref[$arrRet.law_pref]|escape}--><!--{$arrRet.law_addr01|escape}--><!--{$arrRet.law_addr02|escape}--><br>
[emoji:74]<font color="#800000">電話番号</font><br>
<!--{$arrRet.law_tel01|escape}-->-<!--{$arrRet.law_tel02|escape}-->-<!--{$arrRet.law_tel03|escape}--><br>
[emoji:76]<font color="#800000">FAX番号</font><br>
<!--{$arrRet.law_fax01|escape}-->-<!--{$arrRet.law_fax02|escape}-->-<!--{$arrRet.law_fax03|escape}--><br>
[emoji:120]<font color="#800000">メールアドレス</font><br>
<a href="mailto:<!--{$arrRet.law_email|escape:'hex'}-->"><!--{$arrRet.law_email|escape:'hexentity'}--></a><br>
[emoji:e11]<font color="#800000">URL</font><br>
<a href="<!--{$arrRet.law_url|escape}-->"><!--{$arrRet.law_url|escape}--></a><br>
<!-- ▲本文 ここまで -->

<br>
<a href="order.php?page=2" accesskey="2"><!--{2|numeric_emoji}-->進む</a><br>
<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
