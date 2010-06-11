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
<script type="text/javascript">
<!--
    function fnEdit(customer_id) {
        document.form1.action = '<!--{$smarty.const.URL_DIR}-->admin/customer/edit.php';
        document.form1.mode.value = "edit"
        document.form1['edit_customer_id'].value = customer_id;
        document.form1.submit();
        return false;
    }

    function fnCopyFromOrderData() {
        df = document.form1;

        df.deliv_name01.value = df.order_name01.value;
        df.deliv_name02.value = df.order_name02.value;
        df.deliv_kana01.value = df.order_kana01.value;
        df.deliv_kana02.value = df.order_kana02.value;
        df.deliv_zip01.value = df.order_zip01.value;
        df.deliv_zip02.value = df.order_zip02.value;
        df.deliv_tel01.value = df.order_tel01.value;
        df.deliv_tel02.value = df.order_tel02.value;
        df.deliv_tel03.value = df.order_tel03.value;
        df.deliv_pref.value = df.order_pref.value;
        df.deliv_addr01.value = df.order_addr01.value;
        df.deliv_addr02.value = df.order_addr02.value;
    }

    function fnOpenPdfSettingPage(action){
        var WIN;
        WIN = window.open("about:blank", "pdf", "width=500,height=600,scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");

        // 退避
        tmpTarget = document.form1.target;
        tmpMode = document.form1.mode.value;
        tmpAction = document.form1.action;

        document.form1.target = "pdf";
        document.form1.mode.value = 'pdf';
        document.form1.action = action;
        document.form1.submit();
        WIN.focus();

        // 復元
        document.form1.target = tmpTarget;
        document.form1.mode.value = tmpMode;
        document.form1.action = tmpAction;
    }
//-->
</script>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="<!--{$tpl_mode|default:"edit"}-->">
<input type="hidden" name="order_id" value="<!--{$tpl_order_id}-->">
<input type="hidden" name="edit_customer_id" value="">
<input type="hidden" name="anchor_key" value="">
<input type="hidden" id="add_product_id" name="add_product_id" value="">
<input type="hidden" id="add_classcategory_id1" name="add_classcategory_id1" value="">
<input type="hidden" id="add_classcategory_id2" name="add_classcategory_id2" value="">
<input type="hidden" id="edit_product_id" name="edit_product_id" value="">
<input type="hidden" id="edit_classcategory_id1" name="edit_classcategory_id1" value="">
<input type="hidden" id="edit_classcategory_id2" name="edit_classcategory_id2" value="">
<input type="hidden" id="no" name="no" value="">
<input type="hidden" id="delete_no" name="delete_no" value="">

    <tr valign="top">
        <td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
            <!--▼SUB NAVI-->
            <!--{include file=$tpl_subnavi}-->
            <!--▲SUB NAVI-->
        </td>
        <td class="mainbg" >
        <table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
            <!--メインエリア-->
            <tr>
                <td align="center">
                <table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">

                    <tr><td height="14"></td></tr>
                    <tr>
                        <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
                    </tr>
                    <tr>
                        <td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                        <td bgcolor="#cccccc">

                        <!--登録テーブルここから-->
                        <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
                            </tr>
                            <tr>
                                <td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
                                <td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->受注履歴編集</span></td>
                                <td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
                            </tr>
                        </table>

                        <!--{* GMOPG連携用 *}-->
                        <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_gmopg/templates/order_edit.tpl}-->
                        <!--{if file_exists($path)}-->
                            <!--{include file=$path}-->
                        <!--{/if}-->

                        <!--{* SPS連携用 *}-->
                        <!--{assign var=sps_path value=`$smarty.const.MODULE_PATH`mdl_sps/templates/sps_request.tpl}-->
                        <!--{if file_exists($sps_path) && $paymentType[0].module_code == $smarty.const.MDL_SPS_CODE}-->
                            <!--{include file=$sps_path}-->
                        <!--{/if}-->

                        <!--{* ペイジェントモジュール連携用 *}-->
                        <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_paygent/paygent_order.tpl}-->
                        <!--{if file_exists($path)}-->
                            <!--{include file=$path}-->
                        <!--{/if}-->

                        <!--▼お客様情報ここから-->
                        <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
                          <!--{if $tpl_mode != 'add'}-->
                            <tr class="fs12n">
                            <td bgcolor="#f2f1ec" width="110">帳票</td>
                            <td bgcolor="#ffffff">
                              <input type="button" name="address_input" value="帳票の作成" onClick="fnOpenPdfSettingPage('pdf.php?order_id=<!--{$tpl_order_id}-->','pdf_input','500','650'); return false;" />
                            </td>
                            </tr>
                          <!--{/if}-->
                        </table>

                        <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
                        </table>

                        <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">対応状況</td>
                                <td bgcolor="#ffffff">
                                    <!--{assign var=key value="status"}-->
                                    <span class="red12"><!--{$arrErr[$key]}--></span>
                                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                    <option value="">選択してください</option>
                                    <!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
                                    </select><br />
                                    <!--{if $smarty.get.mode != 'add'}-->
                                    <span class="red12">※ <!--{$arrORDERSTATUS[$smarty.const.ORDER_CANCEL]}-->に変更時には、在庫数を手動で戻してください。</span>
                                    <!--{/if}-->
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">発送日</td>
                                <td bgcolor="#ffffff"><!--{$arrForm.commit_date.value|sfDispDBDate|default:"未発送"}--></td>
                            </tr>
                        </table>

                        <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
                        </table>

                        <!--{* F-REGI決済モジュール用 *}-->
                        <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_fregi/fregi_order.tpl}-->
                        <!--{if file_exists($path)}-->
                            <!--{include file=$path}-->
                        <!--{/if}-->

                        <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
                            <!--{foreach key=key item=item from=$arrSearchHidden}-->
                                <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
                            <!--{/foreach}-->
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="717" colspan="4">▼お客様情報
                                <!--{if $tpl_mode == 'add'}-->
                                    &nbsp;&nbsp;&nbsp;<input type="button" name="address_input" value="顧客検索" onclick="fnOpenWindow('<!--{$smarty.const.SITE_URL}-->admin/customer/search_customer.php','search','500','650'); return false;" />
                                <!--{/if}-->
                                </td>
                            </tr>
                                <tr class="fs12n">
                                    <td bgcolor="#f2f1ec" width="110">注文番号</td>
                                    <td bgcolor="#ffffff" width="248"><!--{$arrForm.order_id.value}--></td>
                                    <td bgcolor="#f2f1ec" width="110">顧客ID</td>
                                    <td bgcolor="#ffffff" width="249">
                                    <!--{if $arrForm.customer_id.value > 0}-->
                                        <!--{$arrForm.customer_id.value}-->
                                        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id.value}-->">
                                    <!--{else}-->
                                        （非会員）
                                    <!--{/if}-->
                                    </td>
                                </tr>
                                <tr class="fs12n">
                                    <td bgcolor="#f2f1ec" width="110">受注日</td>
                                    <td bgcolor="#ffffff" width="607" colspan="3"><!--{$arrForm.create_date.value|sfDispDBDate}--></td>
                                    <input type="hidden" name="create_date" value="<!--{$arrForm.create_date.value}-->">
                                </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">顧客名</td>
                                <td bgcolor="#ffffff" width="248">
                                  <!--{assign var=key1 value="order_name01"}-->
                                  <!--{assign var=key2 value="order_name02"}-->
                                  <span class="red12"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                                  <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                                  <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                                </td>
                                <td bgcolor="#f2f1ec" width="110">顧客名（カナ）</td>
                                <td bgcolor="#ffffff" width="249">
                                  <!--{assign var=key1 value="order_kana01"}-->
                                  <!--{assign var=key2 value="order_kana02"}-->
                                  <span class="red12"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                                  <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                                  <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">メールアドレス</td>
                                <td bgcolor="#ffffff" width="248">
                                  <!--{assign var=key1 value="order_email"}-->
                                  <span class="red12"><!--{$arrErr[$key1]}--></span>
                                  <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="30" class="box30" />
                                </td>
                                <td bgcolor="#f2f1ec" width="110">TEL</td>
                                <td bgcolor="#ffffff" width="249">
                                  <!--{assign var=key1 value="order_tel01"}-->
                                  <!--{assign var=key2 value="order_tel02"}-->
                                  <!--{assign var=key3 value="order_tel03"}-->
                                  <span class="red12"><!--{$arrErr[$key1]}--></span>
                                  <span class="red12"><!--{$arrErr[$key2]}--></span>
                                  <span class="red12"><!--{$arrErr[$key3]}--></span>
                                  <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                                  <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                                  <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">住所</td>
                                <td bgcolor="#ffffff" width="607" colspan="3">
                                  <table border="0" cellspacing="0" cellpadding="0" summary=" ">
                                  <tr>
                                    <!--{assign var=key1 value="order_zip01"}-->
                                    <!--{assign var=key2 value="order_zip02"}-->
                                    <span class="red12"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                                    〒
                                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" class="box6" />
                                    -
                                    <input type="text"  name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box6" />
                                    <input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01');" />
                                    <td>
                                    </td>
                                    <tr>
                                      <td>
                                        <!--{assign var=key value="order_pref"}-->
                                        <span class="red12"><!--{$arrErr[$key]}--></span>
                                        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                        <option value="" selected="">都道府県を選択</option>
                                        <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                                        </select>
                                      </td>
                                    </tr>
                                    <tr><td height="5"></td></tr>
                                    <tr class="fs10n">
                                      <td>
                                        <!--{assign var=key value="order_addr01"}-->
                                        <span class="red12"><!--{$arrErr[$key]}--></span>
                                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                                      </td>
                                    </tr>
                                    <tr><td height="5"></td></tr>
                                    <tr class="fs10n">
                                      <td>
                                        <!--{assign var=key value="order_addr02"}-->
                                        <span class="red12"><!--{$arrErr[$key]}--></span>
                                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">備考</td>
                                <td bgcolor="#ffffff" width="607" colspan="3"><!--{$arrForm.message.value|escape|nl2br}--></td>
                            </tr>
                        </table>
                        <!--▲お客様情報ここまで-->

                        <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
                        </table>

                        <!--▼お届け先情報ここから-->
                        <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="717" colspan="4">▼お届け先情報
                                &nbsp;&nbsp;&nbsp;<input type="button" name="input_from_order_data" value="上記お客様情報をコピー" onclick="fnCopyFromOrderData();" /></td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">お名前</td>
                                <td bgcolor="#ffffff" width="248">
                                <!--{assign var=key1 value="deliv_name01"}-->
                                <!--{assign var=key2 value="deliv_name02"}-->
                                <span class="red12"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                                </td>
                                <td bgcolor="#f2f1ec" width="110">お名前（カナ）</td>
                                <td bgcolor="#ffffff" width="249">
                                <!--{assign var=key1 value="deliv_kana01"}-->
                                <!--{assign var=key2 value="deliv_kana02"}-->
                                <span class="red12"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">郵便番号</td>
                                <td bgcolor="#ffffff" width="248">
                                <!--{assign var=key1 value="deliv_zip01"}-->
                                <!--{assign var=key2 value="deliv_zip02"}-->
                                <span class="red12"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                                〒
                                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6" class="box6" />
                                 -
                                <input type="text"  name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box6" />
                                <input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'deliv_zip01', 'deliv_zip02', 'deliv_pref', 'deliv_addr01');" />
                                </td>
                                <td bgcolor="#f2f1ec" width="110">TEL</td>
                                <td bgcolor="#ffffff" width="249">
                                <!--{assign var=key1 value="deliv_tel01"}-->
                                <!--{assign var=key2 value="deliv_tel02"}-->
                                <!--{assign var=key3 value="deliv_tel03"}-->
                                <span class="red12"><!--{$arrErr[$key1]}--></span>
                                <span class="red12"><!--{$arrErr[$key2]}--></span>
                                <span class="red12"><!--{$arrErr[$key3]}--></span>
                                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6" class="box6" /> -
                                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec">住所</td>
                                <td bgcolor="#ffffff" colspan="3">
                                <table border="0" cellspacing="0" cellpadding="0" summary=" ">
                                    <tr>
                                    <td>
                                        <!--{assign var=key value="deliv_pref"}-->
                                        <span class="red12"><!--{$arrErr[$key]}--></span>
                                        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                        <option value="" selected="">都道府県を選択</option>
                                        <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                                        </select>
                                    </td>
                                    </tr>
                                    <tr><td height="5"></td></tr>
                                    <tr class="fs10n">
                                        <td>
                                        <!--{assign var=key value="deliv_addr01"}-->
                                        <span class="red12"><!--{$arrErr[$key]}--></span>
                                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                                        </td>
                                        </tr>
                                        <tr><td height="5"></td></tr>
                                        <tr class="fs10n">
                                            <td>
                                            <!--{assign var=key value="deliv_addr02"}-->
                                            <span class="red12"><!--{$arrErr[$key]}--></span>
                                            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                                            </td>
                                        </tr>
                                </table>
                                </td>
                            </tr>
                        </table>
                        <!--▲お届け先情報ここまで-->

                        <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
                        </table>

                        <!--▼受注商品情報ここから-->
                        <a name="order_products"></a>
                        <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="717" colspan="7">▼受注商品情報
                                <input type="button" name="cheek" value="計算結果の確認" onclick="fnModeSubmit('cheek','anchor_key','order_products');" />
                                <input type="button" name="add_product" value="商品の追加" onclick="win03('<!--{$smarty.const.SITE_URL}-->admin/order/product_select.php<!--{if $tpl_order_id}-->?order_id=<!--{$tpl_order_id}--><!--{/if}-->', 'search', '500', '500'); " />
                                <br />
<!--{**
                                <span class="red">（商品の追加、及び数量の変更に伴う在庫数の変更は手動にてお願いします。)</span>
**}-->
                                <br />
                                <span class="red12"><!--{$arrErr.product_id}--></span>
                                <span class="red12"><!--{$arrErr.quantity}--></span>
                                <span class="red12"><!--{$arrErr.price}--></span>
                                </td>
                            </tr>
                            <tr bgcolor="#f2f1ec" align="center" class="fs12n">
                                <td width="140">商品コード</td>
                                <td width="215">商品名/規格1/規格2</td>
                                <td width="84">単価</td>
                                <td width="45">数量</td>
                                <td width="84">税込み価格</td>
                                <td width="94">小計</td>
                            </tr>
                            <!--{section name=cnt loop=$arrForm.quantity.value}-->
                            <!--{assign var=key value="`$smarty.section.cnt.index`"}-->
                            <tr bgcolor="#ffffff" class="fs12">
                                <td width="140">
                                    <!--{$arrForm.product_code.value[$key]|escape}-->
                                    <input type="hidden" name="product_code[<!--{$key}-->]" value="<!--{$arrForm.product_code.value[$key]}-->" id="product_code_<!--{$key}-->">
                                </td>
                                <td width="215">
                                    <!--{$arrForm.product_name.value[$key]|escape}-->/<!--{$arrForm.classcategory_name1.value[$key]|escape|default:" (なし)"}-->/<!--{$arrForm.classcategory_name2.value[$key]|escape|default:" (なし)"}-->
                                    <input type="hidden" name="product_name[<!--{$key}-->]" value="<!--{$arrForm.product_name.value[$key]}-->" id="product_name_<!--{$key}-->">
                                    <input type="hidden" name="classcategory_name1[<!--{$key}-->]" value="<!--{$arrForm.classcategory_name1.value[$key]}-->" id="classcategory_name1_<!--{$key}-->">
                                    <input type="hidden" name="classcategory_name2[<!--{$key}-->]" value="<!--{$arrForm.classcategory_name2.value[$key]}-->" id="classcategory_name2_<!--{$key}-->">
                                    <br />
                                    <input type="button" name="change" value="変更" onclick="win03('<!--{$smarty.const.SITE_URL}-->admin/order/product_select.php?no=<!--{$key}--><!--{if $tpl_order_id}-->&order_id=<!--{$tpl_order_id}--><!--{/if}-->', 'search', '500', '500'); " >
                                    <!--{if $product_count > 1}-->
                                        <input type="button" name="delete" value="削除" onclick="fnSetFormVal('form1', 'delete_no', <!--{$key}-->); fnModeSubmit('delete_product','anchor_key','order_products');" />
                                    <!--{/if}-->
                                </td>
                                <input type="hidden" name="product_id[<!--{$key}-->]" value="<!--{$arrForm.product_id.value[$key]}-->" id="product_id_<!--{$key}-->">
                                <input type="hidden" name="point_rate[<!--{$key}-->]" value="<!--{$arrForm.point_rate.value[$key]}-->" id="point_rate_<!--{$key}-->">
                                <input type="hidden" name="classcategory_id1[<!--{$key}-->]" value="<!--{$arrForm.classcategory_id1.value[$key]}-->" id="classcategory_id1_<!--{$key}-->">
                                <input type="hidden" name="classcategory_id2[<!--{$key}-->]" value="<!--{$arrForm.classcategory_id2.value[$key]}-->" id="classcategory_id2_<!--{$key}-->">
                                <td width="84" align="center"><input type="text" name="price[<!--{$key}-->]" value="<!--{$arrForm.price.value[$key]|escape}-->" size="6" class="box6" maxlength="<!--{$arrForm.price.length}-->" id="price_<!--{$key}-->"/> 円</td>
                                <td width="45" align="center"><input type="text" name="quantity[<!--{$key}-->]" value="<!--{$arrForm.quantity.value[$key]|escape}-->" size="3" class="box3" maxlength="<!--{$arrForm.quantity.length}-->"/></td>


                                <!--{assign var=price value=`$arrForm.price.value[$key]`}-->
                                <!--{assign var=quantity value=`$arrForm.quantity.value[$key]`}-->
                                <td width="84" align="right"><!--{$price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</td>
                                <td width="94" align="right"><!--{$price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
                            </tr>
                            <!--{/section}-->
                            <tr bgcolor="#ffffff" class="fs12n">
                                <td colspan="5" align="right">小計</td>
                                <td align="right"><!--{$arrForm.subtotal.value|number_format}-->円</td>
                            </tr>
                            <tr bgcolor="#ffffff" class="fs12n">
                                <td colspan="5" align="right">値引</td>
                                <td align="right">
                            <!--{assign var=key value="discount"}-->
                            <span class="red12"><!--{$arrErr[$key]}--></span>
                            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"  size="5" class="box6" />
                             円</td>
                            </tr>
                            <tr bgcolor="#ffffff" class="fs12n">
                                <td colspan="5" align="right">送料</td>
                                <td align="right">
                            <!--{assign var=key value="deliv_fee"}-->
                            <span class="red12"><!--{$arrErr[$key]}--></span>
                            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"  size="5" class="box6" />
                             円</td>
                            </tr>
                            <tr bgcolor="#ffffff" class="fs12n">
                                <td colspan="5" align="right">手数料</td>
                                <td align="right">
                            <!--{assign var=key value="charge"}-->
                            <span class="red12"><!--{$arrErr[$key]}--></span>
                            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"  size="5" class="box6" />
                             円</td>
                            </tr>
                            <tr bgcolor="#ffffff" class="fs12n">
                                <td colspan="5" align="right">合計</td>
                                <td align="right">
                                <span class="red12"><!--{$arrErr.total}--></span>
                                <!--{$arrForm.total.value|number_format}--> 円</td>
                            </tr>
                            <tr bgcolor="#ffffff" class="fs12n">
                                <td colspan="5" align="right">お支払い合計</td>
                                <td align="right">
                                <span class="red12"><!--{$arrErr.payment_total}--></span>
                                <!--{$arrForm.payment_total.value|number_format}-->
                                 円</td>
                            </tr>
                            <!--{if $smarty.const.USE_POINT === true}-->
                                <tr bgcolor="#ffffff" class="fs12n">
                                    <td colspan="5" align="right">使用ポイント</td>
                                    <td align="right">
                                    <!--{assign var=key value="use_point"}-->
                                    <span class="red12"><!--{$arrErr[$key]}--></span>
                                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape|default:0}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"  size="5" class="box6" />
                                     pt</td>
                                </tr>
                                <!--{if $arrForm.birth_point.value > 0}-->
                                <tr bgcolor="#ffffff" class="fs12n">
                                    <td colspan="5" align="right">お誕生日ポイント</td>
                                    <td align="right">
                                    <!--{$arrForm.birth_point.value|number_format}-->
                                     pt</td>
                                </tr>
                                <!--{/if}-->
                                <tr bgcolor="#ffffff" class="fs12n">
                                    <td colspan="5" align="right">加算ポイント</td>
                                    <td align="right">
                                    <!--{$arrForm.add_point.value|number_format|default:0}-->
                                     pt</td>
                                </tr>
                                <tr bgcolor="#ffffff" class="fs12n">
                                    <!--{if $arrForm.customer_id > 0}-->
                                    <td colspan="5" align="right">現在ポイント（ポイントの修正は<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="return fnEdit('<!--{$arrForm.customer_id.value}-->');">顧客編集</a>から手動にてお願い致します。）</td>
                                    <td align="right">
                                    <!--{$arrForm.point.value|number_format}-->
                                     pt</td>
                                    <!--{else}-->
                                    <td colspan="5" align="right">現在ポイント</td><td align="center">（なし）</td>
                                    <!--{/if}-->
                                </tr>
                            <!--{/if}-->
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" colspan="6">▼お支払方法<span class="red">（お支払方法の変更に伴う手数料の変更は手動にてお願いします。)</span></td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#ffffff" colspan="6">
                                <!--{assign var=key value="payment_id"}-->
                                <span class="red12"><!--{$arrErr[$key]}--></span>
                                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnModeSubmit('payment','anchor_key','order_products');">
                                <option value="" selected="">選択してください</option>
                                <!--{html_options options=$arrPayment selected=$arrForm[$key].value}-->
                                </select></td>
                            </tr>

                            <!--{if $arrForm.payment_info|@count > 0}-->
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" colspan="6">▼<!--{$arrForm.payment_type}-->情報</td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#ffffff" colspan="6">
                                    <!--{foreach key=key item=item from=$arrForm.payment_info}-->
                                    <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
                                    <!--{/foreach}-->
                                </td>
                            </tr>
                            <!--{/if}-->

                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" colspan="6">▼お届け指定</td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#ffffff" colspan="6">
                                <!--{assign var=key value="deliv_time_id"}-->
                                <span class="red12"><!--{$arrErr[$key]}--></span>
                                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                <option value="" selected="0">指定無し</option>
                                <!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
                                </select>
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" colspan="6">▼お届け日指定</td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#ffffff" colspan="6">
																	<!--{assign var=key value="deliv_date_year"}-->
                                  <select name="deliv_date_year" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                    <!--{html_options options=$arrYearDelivDate selected=$arrForm.deliv_date_year.value|default:""}-->
                                  </select>年
                                  <select name="deliv_date_month" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                    <!--{html_options options=$arrMonthDelivDate selected=$arrForm.deliv_date_month.value|default:""}-->
                                  </select>月
                                  <select name="deliv_date_day" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                    <!--{html_options options=$arrDayDelivDate selected=$arrForm.deliv_date_day.value|default:""}-->
                                  </select>日
                                    <span class="red12"><!--{$arrErr[$key]}--></span>
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" colspan="6">▼メモ</td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#ffffff" colspan="6">
                                <!--{assign var=key value="note"}-->
                                <span class="red12"><!--{$arrErr[$key]}--></span>
                                <textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="80" rows="6" class="area80" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea></td>
                                </td>
                            </tr>
                        </table>
                        <!--▲受注商品情報ここまで-->

                        <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr>
                                <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                                <td><img src="<!--{$TPL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
                                <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                            </tr>
                            <tr>
                                <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                                <td bgcolor="#e9e7de" align="center">
                                <table border="0" cellspacing="0" cellpadding="0" summary=" ">
                                    <tr>
                                        <td>
                                            <!--{if count($arrSearchHidden) > 0}-->
                                            <a href="#" onmouseover="chgImg('<!--{$TPL_DIR}-->img/contents/btn_search_back_on.jpg','back');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/contents/btn_search_back.jpg','back');" onclick="fnChangeAction('<!--{$smarty.const.URL_SEARCH_ORDER}-->'); fnModeSubmit('search','',''); return false;"><img src="<!--{$TPL_DIR}-->img/contents/btn_search_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="back"></a>
                                            <!--{/if}-->
                                            <input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onclick="return fnConfirm();">
                                        </td>
                                    </tr>
                                </table>
                                </td>
                                <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
                            </tr>
                        </table>

                        <!--登録テーブルここまで-->
                        </td>
                        <td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                    </tr>
                    <tr>
                        <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
                    </tr>
                    <tr><td height="30"></td></tr>

                </table>
                </td>
            </tr>
            <!--メインエリア-->
        </table>
        </td>
    </tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
