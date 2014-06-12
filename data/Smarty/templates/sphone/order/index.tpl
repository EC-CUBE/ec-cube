<!--{*
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
 *}-->

<section id="undercolumn">
    <!--☆特定商取引に関する法律に基づく表記 -->
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <dl class="form_info">
        <dt>販売業者</dt>
        <dd><!--{$arrOrder.law_company|h}--></dd>

        <dt>運営責任者</dt>
        <dd><!--{$arrOrder.law_manager|h}--></dd>

        <dt>住所</dt>
        <dd>〒<!--{$arrOrder.law_zip01|h}-->-<!--{$arrOrder.law_zip02|h}--><br /><!--{$arrPref[$arrOrder.law_pref]|h}--><!--{$arrOrder.law_addr01|h}--><!--{$arrOrder.law_addr02|h}--></dd>

        <dt>電話番号</dt>
        <dd><!--{$arrOrder.law_tel01|h}-->-<!--{$arrOrder.law_tel02|h}-->-<!--{$arrOrder.law_tel03|h}--></dd>

        <dt>FAX番号</dt>
        <dd><!--{$arrOrder.law_fax01|h}-->-<!--{$arrOrder.law_fax02|h}-->-<!--{$arrOrder.law_fax03|h}--></dd>

        <dt>メールアドレス</dt>
        <dd><a href="mailto:<!--{$arrOrder.law_email|escape:'hex'}-->" rel="external"><!--{$arrOrder.law_email|escape:'hexentity'}--></a></dd>

        <dt>URL</dt>
        <dd><a href="<!--{$arrOrder.law_url|h}-->" rel="external"><!--{$arrOrder.law_url|h}--></a></dd>

        <dt>商品以外の必要代金</dt>
        <dd><!--{$arrOrder.law_term01|h|nl2br}--></dd>

        <dt>注文方法</dt>
        <dd><!--{$arrOrder.law_term02|h|nl2br}--></dd>

        <dt>支払方法</dt>
        <dd><!--{$arrOrder.law_term03|h|nl2br}--></dd>

        <dt>支払期限</dt>
        <dd><!--{$arrOrder.law_term04|h|nl2br}--></dd>

        <dt>引渡し時期</dt>
        <dd><!--{$arrOrder.law_term05|h|nl2br}--></dd>

        <dt>返品・交換について</dt>
        <dd><!--{$arrOrder.law_term06|h|nl2br}--></dd>
    </dl>
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->
<!--▲CONTENTS-->
