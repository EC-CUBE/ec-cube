<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

<!--▼コンテンツここから -->
<section id="undercolumn">

    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!--★インフォメーション★-->
    <div class="information end">
        <p>各商品のお届け先を選択してください。</p>
        <p>※数量はカートの中の数量と合わせてください。</p>
    </div>

    <!--★ボタン★-->
    <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
        <div class="btn_area_top">
            <a rel="external" href="javascript:void(0);" class="btn_sub addbtn" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->','new_deiv','600','640'); return false;">新しいお届け先を追加</a>
        </div>
    <!--{/if}-->

    <!--▼フォームここから -->
    <div class="form_area">
        <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->shopping/multiple.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="line_of_num" value="<!--{$arrForm.line_of_num.value}-->" />
            <input type="hidden" name="mode" value="confirm" />

            <!--{section name=line loop=$arrForm.line_of_num.value}-->
                <!--{assign var=index value=$smarty.section.line.index}-->
                <!--{assign var=key value="quantity"}-->
                <!--{if $arrErr[$key][$index] != ''}-->
                    <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                <!--{/if}-->
                <div class="formBox">
                    <!--▼商品 -->
                    <div class="delivitemBox">
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrForm.main_list_image.value[$index]|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$arrForm.name.value[$index]|h}-->" class="photoL" />
                        <div class="delivContents">

                            <p>
                                <em><!--{$arrForm.name.value[$index]|h}--></em><br />
                                <!--{if $arrForm.classcategory_name1.value[$index] != ""}-->
                                    <span class="mini"><!--{$arrForm.class_name1.value[$index]|h}-->：<!--{$arrForm.classcategory_name1.value[$index]|h}--></span><br />
                                <!--{/if}-->
                                <!--{if $arrForm.classcategory_name2.value[$index] != ""}-->
                                    <span class="mini"><!--{$arrForm.class_name2.value[$index]|h}-->：<!--{$arrForm.classcategory_name2.value[$index]|h}--></span><br />
                                <!--{/if}-->
                                <!--{$arrForm.price.value[$index]|sfCalcIncTax|number_format}-->円
                            </p>
                            <ul>
                                <li class="result"><span class="mini">数量</li>
                                <li>
                                    <input type="number" name="<!--{$key}-->[<!--{$index}-->]" class="cartin_quantity txt" value="<!--{$arrForm[$key].value[$index]|h}-->" max="9" style="" />
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--▲商品 -->

                    <div class="btn_area_btm">
                        <input type="hidden" name="cart_no[<!--{$index}-->]" value="<!--{$index}-->" />
                        <!--{assign var=key value="product_class_id"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="name"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="class_name1"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="class_name2"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="classcategory_name1"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="classcategory_name2"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="main_image"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="main_list_image"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="price"}-->
                        <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="shipping"}-->
                        <select name="<!--{$key}-->[<!--{$index}-->]" class="boxLong data-role-none"><!--{html_options options=$addrs selected=$arrForm[$key].value[$index]}--></select>
                    </div>

                </div><!-- /.formBox -->
            <!--{/section}-->

            <ul class="btn_btm">
                <li><a rel="external" href="javascript:void(document.form1.submit());" class="btn">選択したお届け先に送る</a></li>
                <li><a rel="external" href="<!--{$smarty.const.CART_URLPATH}-->" class="btn_back">戻る</a></li>
            </ul>
        </form>
    </div><!-- /.form_area -->
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
<!--▲コンテンツここまで -->
