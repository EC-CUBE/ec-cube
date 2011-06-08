<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">//<![CDATA[
    $(document).ready(function() {
        $("a.expansion").fancybox({
        });
    });
//]]></script>
<h2 class="title"><!--{$tpl_title|h}--></h2>

<p>各商品のお届け先を選択してください。</p>
<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
    <p>一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。<br>
    ※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>
<!--{/if}-->

<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
    <p class="addbtn"><a class="kybtn" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->','new_deiv','600','640'); return false;">新しいお届け先を追加する</a></p><br /><br />
<!--{/if}-->
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
    <input type="hidden" name="line_of_num" value="<!--{$arrForm.line_of_num.value}-->" />
    <input type="hidden" name="mode" value="confirm" />

        <!--{section name=line loop=$arrForm.line_of_num.value}-->
        <!--{assign var=index value=$smarty.section.line.index}-->

        <table summary="商品情報" class="entryform">
        <tr>
            <th class="multi_ph">商品写真</th>
            <th class="multi_pr">商品名</th>
            <th class="multi_nu">数量</th>
        </tr>
            <tr>
                <td class="phototd">
                <a
                    <!--{if $arrForm.main_image.value[$index]|strlen >= 1}-->
                        href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrForm.main_image.value[$index]|sfNoImageMainList|h}-->"
                        class="expansion"
                        target="_blank"
                    <!--{/if}-->
                >
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrForm.main_list_image.value[$index]|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="&lt;!--{$arrForm.name[$index]|h}--&gt;" /></a>
                </td>
                <td class="multi_pr"><!--{* 商品名 *}--><strong><!--{$arrForm.name.value[$index]|h}--></strong><br />
                    <!--{if $arrForm.classcategory_name1.value[$index] != ""}-->
                        <!--{$arrForm.class_name1.value[$index]|h}-->：<!--{$arrForm.classcategory_name1.value[$index]|h}--><br />
                    <!--{/if}-->
                    <!--{if $arrForm.classcategory_name2.value[$index] != ""}-->
                        <!--{$arrForm.class_name2.value[$index]|h}-->：<!--{$arrForm.classcategory_name2.value[$index]|h}--><br />
                    <!--{/if}-->
                    <!--{$arrForm.price02.value[$index]|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                </td>
                <td class="multi_nu">
                  <input class="multi_nu" type="hidden" name="cart_no[<!--{$index}-->]" value="<!--{$index}-->" />
                  <!--{assign var=key value="product_class_id"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="name"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="class_name1"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="class_name2"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="classcategory_name1"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="classcategory_name2"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="main_image"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="main_list_image"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="price02"}-->
                  <input class="multi_nu" type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                  <!--{assign var=key value="quantity"}-->
                  <!--{if $arrErr[$key][$index] != ''}-->
                      <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                  <!--{/if}-->
                  <input class="multi_nu" type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" size="4" />
                </td>
             </tr>
            <tr>
              <td colspan="3">お届け先</td>
            </tr>
            <tr>
              <td colspan="3">
                <!--{assign var=key value="shipping"}-->
                <select name="<!--{$key}-->[<!--{$index}-->]"><!--{html_options options=$addrs selected=$arrForm[$key].value[$index]}--></select>
              </td>
            </tr>
           </table>
          <!--{/section}-->

    <div class="tblareabtn">
         <p><input type="submit" value="選択したお届け先に送る" class="spbtn spbtn-shopping" width="130" height="30" alt="選択したお届け先に送る" name="send_button" id="next" /></p>
         <p><a href="<!--{$smarty.const.CART_URLPATH}-->" class="spbtn spbtn-medeum">戻る</a></p>
    </div>
</form>
