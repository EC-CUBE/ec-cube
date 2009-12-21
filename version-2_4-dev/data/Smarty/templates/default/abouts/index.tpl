<!--{*
/*
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
 */
*}-->
<!--▼CONTENTS-->
<div id="undercolumn">

  <div id="undercolumn_aboutus">
    <h2 class="title"><img src="<!--{$TPL_DIR}-->img/aboutus/title.jpg" width="580" height="40" alt="当サイトについて" /></h2>

    <table summary="当サイトについて">

    <!--{assign var=_site value=$arrSiteInfo}-->

      <!--{if strlen($_site.shop_name)}-->
        <tr>
          <th>店名</th>
          <td><!--{$_site.shop_name|escape}--></td>
        </tr>
      <!--{/if}-->

      <!--{if strlen($_site.company_name)}-->
      <tr>
        <th>会社名</th>
        <td><!--{$_site.company_name|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if strlen($_site.zip01)}-->
      <tr>
        <th>住所</th>
        <td>〒<!--{$_site.zip01|escape}-->-<!--{$_site.zip02|escape}--><br /><!--{$_site.pref|escape}--><!--{$_site.addr01|escape}--><!--{$_site.addr02|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if strlen($_site.tel01)}-->
      <tr>
        <th>電話番号</th>
        <td><!--{$_site.tel01|escape}-->-<!--{$_site.tel02|escape}-->-<!--{$_site.tel03|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if strlen($_site.fax01)}-->
      <tr>
        <th>FAX番号</th>
        <td><!--{$_site.fax01|escape}-->-<!--{$_site.fax02|escape}-->-<!--{$_site.fax03|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if strlen($_site.email02)}-->
      <tr>
        <th>メールアドレス</th>
        <td><a href="mailto:<!--{$_site.email02|escape:'hex'}-->"><!--{$_site.email02|escape:'hexentity'}--></a></td>
      </tr>
      <!--{/if}-->

      <!--{if strlen($_site.business_hour)}-->
      <tr>
        <th>営業時間</th>
        <td><!--{$_site.business_hour|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if strlen($_site.good_traded)}-->
      <tr>
        <th>取扱商品</th>
        <td><!--{$_site.good_traded|escape|nl2br}--></td>
      </tr>
      <!--{/if}-->

      <!--{if strlen($_site.message)}-->
      <tr>
        <th>メッセージ</th>
        <td><!--{$_site.message|escape|nl2br}--></td>
      </tr>
      <!--{/if}-->

    </table>

  </div>
</div>
<!--▲CONTENTS-->

