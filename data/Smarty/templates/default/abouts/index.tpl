<!--{*
    /*
    * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
    *
    * http://www.lockon.co.jp/
    */
    *}-->
<!--▼CONTENTS-->
<div id="undercolumn">

  <div id="undercolumn_aboutus">
    <h2 class="title"><img src="<!--{$TPL_DIR}-->img/aboutus/title.jpg" width="580" height="40" alt="当サイトについて" /></h2>

    <table summary="当サイトについて">

    <!--{assign var=_site value=$arrSiteInfo}-->

      <!--{if $_site.shop_name}-->
        <tr>
          <th>店名</th>
          <td><!--{$_site.shop_name|escape}--></td>
        </tr>
      <!--{/if}-->

      <!--{if $_site.company_name}-->
      <tr>
        <th>会社名</th>
        <td><!--{$_site.company_name|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if $_site.zip01}-->
      <tr>
        <th>住所</th>
        <td>〒<!--{$_site.zip01|escape}-->-<!--{$_site.zip02|escape}--><br /><!--{$_site.pref|escape}--><!--{$_site.addr01|escape}--><!--{$_site.addr02|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if $_site.tel01}-->
      <tr>
        <th>電話番号</th>
        <td><!--{$_site.tel01|escape}-->-<!--{$_site.tel02|escape}-->-<!--{$_site.tel03|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if $_site.fax01}-->
      <tr>
        <th>FAX番号</th>
        <td><!--{$_site.fax01|escape}-->-<!--{$_site.fax02|escape}-->-<!--{$_site.fax03|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if $_site.email02}-->
      <tr>
        <th>メールアドレス</th>
        <td><a href="mailto:<!--{$_site.email02|escape:'hex'}-->"><!--{$_site.email02|escape:'hexentity'}--></a></td>
      </tr>
      <!--{/if}-->

      <!--{if $_site.business_hour}-->
      <tr>
        <th>営業時間</th>
        <td><!--{$_site.business_hour|escape}--></td>
      </tr>
      <!--{/if}-->

      <!--{if $_site.business_hour}-->
      <tr>
        <th>取扱商品</th>
        <td><!--{$_site.good_traded|escape|nl2br}--></td>
      </tr>
      <!--{/if}-->

      <!--{if $_site.message}-->
      <tr>
        <th>メッセージ</th>
        <td><!--{$_site.message|escape|nl2br}--></td>
      </tr>
      <!--{/if}-->

    </table>

  </div>
</div>
<!--▲CONTENTS-->

