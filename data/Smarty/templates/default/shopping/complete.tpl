<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_shopping">
    <p class="flowarea">
      <img src="<!--{$TPL_DIR}-->img/shopping/flow04.gif" width="700" height="36" alt="購入手続きの流れ" />
    </p>
    <h2>
      <img src="<!--{$TPL_DIR}-->img/shopping/complete_title.jpg" width="700" height="40" alt="ご注文完了" />
    </h2>

    <!-- ▼クレジット(コンビニ)決済 -->
    <!--{if $arrModuleParam.module_id > 0 }-->
      <img src="<!--{$smarty.const.CREDIT_HTTP_ANALYZE_URL}-->?mid=<!--{$arrModuleParam.module_id}-->&tid=<!--{$arrModuleParam.payment_total}-->&pid=<!--{$arrModuleParam.payment_id}-->" width="0" height="0" border="0" style="width: 0px; height: 0px" />
    <!--{/if}-->
    <!-- ▲クレジット(コンビニ)決済 -->

    <!-- ▼その他決済情報を表示する場合は表示 -->
    <!--{if $arrOther.title.value }-->
    <p><em>■<!--{$arrOther.title.name}-->情報</em><br />
        <!--{foreach key=key item=item from=$arrOther}-->
        <!--{if $key != "title"}-->
          <!--{if $item.name != ""}-->
            <!--{$item.name}-->：
          <!--{/if}-->
            <!--{$item.value|nl2br}--><br />
        <!--{/if}-->
        <!--{/foreach}-->
    </p>
     <!--{/if}-->
     <!-- ▲コンビに決済の場合には表示 -->

    <div id="completetext">
      <em><!--{$arrInfo.shop_name|escape}-->の商品をご購入いただき、ありがとうございました。</em>

      <p>ただいま、ご注文の確認メールをお送りさせていただきました。<br />
        万一、ご確認メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせくださいませ。<br />
        今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>

      <p><!--{$arrInfo.shop_name|escape}--><br />
        TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br />
        E-mail：<a href="mailto:<!--{$arrInfo.email02|escape:'hex'}-->"><!--{$arrInfo.email02|escape:'hexentity'}--></a></p>
    </div>

    <div class="tblareabtn">
      <!--{if $is_campaign}-->
        <a href="<!--{$smarty.const.CAMPAIGN_URL}--><!--{$campaign_dir}-->/index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$TPL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage" /></a>
      <!--{else}-->
        <a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$TPL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage" /></a>
      <!--{/if}-->
    </div>
  </div>
</div>
<!--▲CONTENTS-->
