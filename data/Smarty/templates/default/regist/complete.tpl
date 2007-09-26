<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
  <div id="undercolumn_entry">
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/entry/title.jpg" width="580" height="40" alt="会員登録" />
    </h2>
    <div id="completetext">
      <em>本登録が完了いたしました。<br />
        それではショッピングをお楽しみください。</em>

      <p>今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>

      <p><!--{$arrSiteInfo.company_name|escape}--><br />
        TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--> <!--{if $arrSiteInfo.business_hour != ""}-->（受付時間/<!--{$arrSiteInfo.business_hour}-->）<!--{/if}--><br />
        E-mall：<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>

      <div class="tblareabtn">
	<a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$TPL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage" /></a>
      </div>
    </div>
  </div>
</div>
<!--▲CONTENTS-->
