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

<!--▼コンテンツここから -->
<section id="undercolumn">

    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!--★インフォメーション★-->
    <div class="information">
        <p>一覧よりお届け先住所を選択してください。</p>
    </div>

    <!--▼フォームここから -->
    <div class="form_area">
        <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->shopping/deliv.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="customer_addr" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="other_deliv_id" value="" />
            <!--{if $arrErr.deli != ""}-->
                <p class="attention"><!--{$arrErr.deli}--></p>
            <!--{/if}-->

            <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                <!--☆右にスライドボタン -->
                <div class="bubbleBox">
                    <div class="bubble_announce clearfix">
                        <p class="fb"><a rel="external" href="javascript:eccube.setModeAndSubmit('multiple', '', '');">複数のお届け先に送りますか？</a></p>
                    </div>
                    <div class="bubble_arrow_line"><!--矢印空タグ --></div>
                    <div class="bubble_arrow"><!--矢印空タグ --></div>
                </div>
            <!--{/if}-->

            <div class="formBox">
                <!--{section name=cnt loop=$arrAddr}-->
                    <dl class="deliv_check">
                        <!--{if $smarty.section.cnt.first}-->
                            <dt class="first">
                                <p>
                                    <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> class="data-role-none" />
                                    <label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">会員登録住所</label>
                                </p>
                            </dt>
                        <!--{else}-->
                            <dt>
                                <p>
                                    <input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->"<!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> class="data-role-none" />
                                    <label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">追加登録住所</label>
                                </p>
                                <ul class="edit">
                                    <li><a rel="external" href="javascript:void(0);" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','600','640'); return false;" class="b_edit">編集</a></li>
                                    <li><img src="<!--{$TPL_URLPATH}-->img/button/btn_delete.png" width="21" height="20" alt="削除" onclick="eccube.setModeAndSubmit('delete', 'other_deliv_id', '<!--{$arrAddr[cnt].other_deliv_id}-->');" /></li>
                                </ul>
                            </dt>
                        <!--{/if}-->
                        <dd <!--{if $smarty.section.cnt.last && !($tpl_addrmax < $smarty.const.DELIV_ADDR_MAX)}-->class="end"<!--{/if}-->>
                            <!--{assign var=key value=$arrAddr[cnt].pref}-->
                            <!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|h}--><!--{$arrAddr[cnt].addr02|h}--><br />
                            <!--{$arrAddr[cnt].name01|h}--> <!--{$arrAddr[cnt].name02|h}-->
                        </dd>
                    </dl>
                <!--{/section}-->

                <!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
                    <div class="inner">
                        <a rel="external" href="javascript:void(0);" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->','new_deiv','600','640'); return false;" class="btn_sub addbtn">新しいお届け先を追加</a>
                    </div>
                <!--{/if}-->
            </div><!-- /.formBox -->

            <ul class="btn_btm">
                <li><a rel="external" href="javascript:eccube.setModeAndSubmit('customer_addr','','');" class="btn">選択したお届け先に送る</a></li>
                <li><a rel="external" href="<!--{$smarty.const.CART_URL}-->" class="btn_back">戻る</a></li>
            </ul>

        </form>
    </div><!-- /.form_area -->
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

<!--▲コンテンツここまで -->
