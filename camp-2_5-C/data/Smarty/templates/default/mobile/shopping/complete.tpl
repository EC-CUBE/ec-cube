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
<center>ご注文完了</center>

<hr>

ご注文、有り難うございました。<br>
商品到着をお楽しみにお待ち下さいませ。<br>
どうぞ、今後とも、<!--{$arrInfo.shop_name|escape}-->をよろしくお願いします。<br>
<br>

<!--{if $arrOther.title.value }-->
<!-- ▼その他の決済情報 -->
■<!--{$arrOther.title.name}-->情報<br>
<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value|nl2br}--><br>
<!--{/if}-->
<!--{/foreach}-->
<br>
<!-- ▲その他の決済情報 -->
<!--{/if}-->

<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページに戻る</a><br>

<br>
<hr>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
