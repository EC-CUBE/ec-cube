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
<center><font color="#008080"><b><!--{$tpl_title|h}--></b></font></center>

<hr>

[emoji:39]<font color="#800000">販売業者</font><br>
<!--{$arrRet.law_company|h}--><br>
<hr>

[emoji:170]<font color="#800000">運営責任者</font><br>
<!--{$arrRet.law_manager|h}--><br>
<hr>

[emoji:38]<font color="#800000">住所</font><br>
〒<!--{$arrRet.law_zip01|h}-->-<!--{$arrRet.law_zip02|h}--><br>
<!--{$arrPref[$arrRet.law_pref]|h}--><!--{$arrRet.law_addr01|h}--><br>
<!--{$arrRet.law_addr02|h}--><br>
<hr>

[emoji:74]<font color="#800000">電話番号</font><br>
<a href="tel:<!--{$arrRet.law_tel01|h}-->-<!--{$arrRet.law_tel02|h}-->-<!--{$arrRet.law_tel03|h}-->"><!--{$arrRet.law_tel01|h}-->-<!--{$arrRet.law_tel02|h}-->-<!--{$arrRet.law_tel03|h}--></a><br>
<hr>

[emoji:107]<font color="#800000">FAX番号</font><br>
<!--{$arrRet.law_fax01|h}-->-<!--{$arrRet.law_fax02|h}-->-<!--{$arrRet.law_fax03|h}--><br>
<hr>

[emoji:110]<font color="#800000">メールアドレス</font><br>
<a href="mailto:<!--{$arrRet.law_email|escape:'hex'}-->"><!--{$arrRet.law_email|escape:'hexentity'}--></a><br>
<hr>

[emoji:e11]<font color="#800000">サイトURL</font><br>
<a href="<!--{$arrRet.law_url|h}-->"><!--{$arrRet.law_url|h}--></a><br>
<hr>

[emoji:113]<font color="#800000">商品以外の必要代金</font><br>
<!--{$arrRet.law_term01|h|nl2br}--><br>
<hr>

[emoji:146]<font color="#800000">注文方法</font><br>
<!--{$arrRet.law_term02|h|nl2br}--><br>
<hr>

[emoji:42]<font color="#800000">支払方法</font><br>
<!--{$arrRet.law_term03|h|nl2br}--><br>
<hr>

[emoji:176]<font color="#800000">支払期限</font><br>
<!--{$arrRet.law_term04|h|nl2br}--><br>
<hr>

[emoji:72]<font color="#800000">引渡し時期</font><br>
<!--{$arrRet.law_term05|h|nl2br}--><br>
<hr>

[emoji:e42]<font color="#800000">返品・交換について</font><br>
<!--{$arrRet.law_term06|h|nl2br}--><br>
<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URLPATH}-->" accesskey="9"><!--{9|numeric_emoji}-->カート見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URLPATH}-->" accesskey="0"><!--{0|numeric_emoji}-->ホームへ</a><br>

<br>

<!--{include file='footer.tpl'}-->
