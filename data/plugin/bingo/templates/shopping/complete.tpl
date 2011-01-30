<!--{*
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
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_shopping">
    <p class="flowarea">
      <img src="<!--{$TPL_URLPATH}-->img/shopping/flow04.gif" width="700" height="36" alt="購入手続きの流れ" />
    </p>
    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!-- ▼その他決済情報を表示する場合は表示 -->
    <!--{if $arrOther.title.value}-->
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

      <em>
        当たり！当たり！当たり！<br />
        おめでとうございます！当たりです！お支払い合計が無料になります！<br />
        当たり！当たり！当たり！<br />
      </em><br />

      <em><!--{$arrInfo.shop_name|h}-->の商品をご購入いただき、ありがとうございました。</em>

      <p>ただいま、ご注文の確認メールをお送りさせていただきました。<br />
        万一、ご確認メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせくださいませ。<br />
        今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>

      <p><!--{$arrInfo.shop_name|h}--><br />
        TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br />
        E-mail：<a href="mailto:<!--{$arrInfo.email02|escape:'hex'}-->"><!--{$arrInfo.email02|escape:'hexentity'}--></a></p>
    </div>

    <div class="tblareabtn">
      <!--{if $is_campaign}-->
        <a href="<!--{$smarty.const.CAMPAIGN_URL}--><!--{$campaign_dir}-->/<!--{$smarty.const.DIR_INDEX_PATH}-->" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$TPL_URLPATH}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage" /></a>
      <!--{else}-->
        <a href="<!--{$smarty.const.TOP_URLPATH}-->" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$TPL_URLPATH}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage" /></a>
      <!--{/if}-->
    </div>
  </div>
</div>
<!--▲CONTENTS-->
