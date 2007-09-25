<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
  <div id="undercolumn_contact">
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/contact/title.jpg" width="580" height="40" alt="お問い合わせ" />
    </h2>
    <div id="completetext">
      お問い合わせ内容の送信が完了いたしました。<br />
      万一、ご回答メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせください。<br />
      今後ともご愛顧賜りますようよろしくお願い申し上げます。
    <p><!--{$arrSiteInfo.company_name|escape}--><br />
      TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}-->
       <!--{if $arrSiteInfo.business_hour != ""}-->
      （受付時間/<!--{$arrSiteInfo.business_hour}-->）
       <!--{/if}--><br />
      E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>
    </div>
    <div class="tblareabtn">
    <!--{if $is_campaign}-->
      <a href="<!--{$smarty.const.CAMPAIGN_URL}--><!--{$campaign_dir}-->/index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$TPL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage" id="b_toppage" /></a>

    <!--{else}-->

      <a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$TPL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage" id="b_toppage" /></a>
    <!--{/if}-->
    </div>
  </div>
