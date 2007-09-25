<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
  <div id="undercolumn_order">
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/aboutshopping/transaction_title.jpg" width="580" height="40" alt="特定商取引に関する法律に基づく表記" />
    </h2>
    <table summary="特定商取引に関する法律に基づく表記">
      <tr>
        <th>販売業者</th>
        <td><!--{$arrRet.law_company|escape}--></td>
      </tr>
      <tr>
        <th>運営責任者</th>
        <td><!--{$arrRet.law_manager|escape}--></td>
      </tr>
      <tr>
        <th>住所</th>
        <td>〒<!--{$arrRet.law_zip01|escape}-->-<!--{$arrRet.law_zip02|escape}--><br /><!--{$arrPref[$arrRet.law_pref]|escape}--><!--{$arrRet.law_addr01|escape}--><!--{$arrRet.law_addr02|escape}--></td>
      </tr>
      <tr>
        <th>電話番号</th>
        <td><!--{$arrRet.law_tel01|escape}-->-<!--{$arrRet.law_tel02|escape}-->-<!--{$arrRet.law_tel03|escape}--></td>
      </tr>
      <tr>
        <th>FAX番号</th>
        <td><!--{$arrRet.law_fax01|escape}-->-<!--{$arrRet.law_fax02|escape}-->-<!--{$arrRet.law_fax03|escape}--></td>
      </tr>
      <tr>
        <th>メールアドレス</th>
        <td><a href="mailto:<!--{$arrRet.law_email|escape:'hex'}-->"><!--{$arrRet.law_email|escape:'hexentity'}--></a></td>
      </tr>
      <tr>
        <th>URL</th>
        <td><a href="<!--{$arrRet.law_url|escape}-->"><!--{$arrRet.law_url|escape}--></a></td>
      </tr>
      <tr>
        <th>商品以外の必要代金</th>
        <td><!--{$arrRet.law_term01|escape|nl2br}--></td>
      </tr>
      <tr>
        <th>注文方法</th>
        <td><!--{$arrRet.law_term02|escape|nl2br}--></td>
      </tr>
      <tr>
        <th>支払方法</th>
        <td><!--{$arrRet.law_term03|escape|nl2br}--></td>
      </tr>
      <tr>
        <th>支払期限</th>
        <td><!--{$arrRet.law_term04|escape|nl2br}--></td>
      </tr>
      <tr>
        <th>引渡し時期</th>
        <td><!--{$arrRet.law_term05|escape|nl2br}--></td>
      </tr>
      <tr>
        <th>返品・交換について</th>
        <td><!--{$arrRet.law_term06|escape|nl2br}--></td>
      </tr>
    </table>
  </div>
</div>
<!--▲CONTENTS-->
