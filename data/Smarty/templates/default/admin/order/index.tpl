<!--{*
/*
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 */
*}-->
<!--★★メインコンテンツ★★-->
<script type="text/javascript">
<!--
    function fnSelectCheckSubmit(action){

        var fm = document.form1;

        var i;
        var checkflag = 0;
        var max = fm["pdf_order_id[]"].length;

        if(max) {
            for (i=0;i<max;i++){
                if(fm["pdf_order_id[]"][i].checked == true){
                    checkflag = 1;
                }
            }
        } else {
            if(fm["pdf_order_id[]"].checked == true) {
                checkflag = 1;
            }
        }

        if(checkflag == 0){
            alert('チェックボックスが選択されていません');
            return false;
        }

        if(checkflag == 1){
            fnOpenPdfSettingPage(action);
        }
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

    function fnBoxChecked(check){
        var count;
        var fm = document.form1;
        var max = fm["pdf_order_id[]"].length;
        for(count=0; count<max; count++){
            fm["pdf_order_id[]"][count].checked = check;
        }
    }

//-->
</script>
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="search_form" id="search_form" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="search">
    <tr valign="top">
        <td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
            <!-- サブナビ -->
            <!--{include file=$tpl_subnavi}-->
        </td>
        <td class="mainbg">
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
                        <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
                            </tr>
                            <tr>
                                <td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
                                <td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->検索条件設定</span></td>
                                <td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
                            </tr>
                        </table>
                        <!--検索条件設定テーブルここから-->
                        <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">注文番号</td>
                                <td bgcolor="#ffffff" width="194">
                                    <!--{assign var=key1 value="search_order_id1"}-->
                                    <!--{assign var=key2 value="search_order_id2"}-->
                                    <span class="red12"><!--{$arrErr[$key1]}--></span>
                                    <span class="red12"><!--{$arrErr[$key2]}--></span>
                                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"    size="6" class="box6" />
                                     〜
                                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"    size="6" class="box6" />
                                </td>
                                <td bgcolor="#f2f1ec" width="110">対応状況</td>
                                <td bgcolor="#ffffff" width="195">
                                    <!--{assign var=key value="search_order_status"}-->
                                    <span class="red12"><!--{$arrErr[$key]}--></span>
                                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                    <option value="">選択してください</option>
                                    <!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
                                    </select>
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">顧客名</td>
                                <td bgcolor="#ffffff" width="194">
                                <!--{assign var=key value="search_order_name"}-->
                                <span class="red12"><!--{$arrErr[$key]}--></span>
                                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                                </td>
                                <td bgcolor="#f2f1ec" width="110">顧客名（カナ）</td>
                                <td bgcolor="#ffffff" width="195">
                                <!--{assign var=key value="search_order_kana"}-->
                                <span class="red12"><!--{$arrErr[$key]}--></span>
                                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">メールアドレス</td>
                                <td bgcolor="#ffffff" width="194">
                                    <!--{assign var=key value="search_order_email"}-->
                                    <span class="red12"><!--{$arrErr[$key]}--></span>
                                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                                </td>
                                <td bgcolor="#f2f1ec" width="110">TEL</td>
                                <td bgcolor="#ffffff" width="195">
                                    <!--{assign var=key value="search_order_tel"}-->
                                    <span class="red12"><!--{$arrErr[$key]}--></span>
                                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">生年月日</td>
                                <td bgcolor="#ffffff" width="499" colspan="3">
                                    <span class="red"><!--{$arrErr.search_sbirthyear}--></span>
                                    <span class="red"><!--{$arrErr.search_ebirthyear}--></span>
                                    <select name="search_sbirthyear" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
                                    <option value="">----</option>
                                    <!--{html_options options=$arrBirthYear selected=$arrForm.search_sbirthyear.value}-->
                                    </select>年
                                    <select name="search_sbirthmonth" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrMonth selected=$arrForm.search_sbirthmonth.value}-->
                                    </select>月
                                    <select name="search_sbirthday" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrDay selected=$arrForm.search_sbirthday.value}-->
                                    </select>日〜
                                    <select name="search_ebirthyear" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
                                    <option value="">----</option>
                                    <!--{html_options options=$arrBirthYear selected=$arrForm.search_ebirthyear.value}-->
                                    </select>年
                                    <select name="search_ebirthmonth" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrMonth selected=$arrForm.search_ebirthmonth.value}-->
                                    </select>月
                                    <select name="search_ebirthday" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrDay selected=$arrForm.search_ebirthday.value}-->
                                    </select>日
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">性別</td>
                                <td bgcolor="#ffffff" width="499" colspan="3">
                                <!--{assign var=key value="search_order_sex"}-->
                                <span class="red12"><!--{$arrErr[$key]}--></span>
                                <!--{html_checkboxes name="$key" options=$arrSex selected=$arrForm[$key].value}-->
                            </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">支払方法</td>
                                <td bgcolor="#ffffff" width="499" colspan="3">
                                <!--{assign var=key value="search_payment_id"}-->
                                <span class="red12"><!--{$arrErr[$key]|escape}--></span>
                                <!--{html_checkboxes name="$key" options=$arrPayment|escape selected=$arrForm[$key].value}-->
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">受注日</td>
                                <td bgcolor="#ffffff" width="499" colspan="3">
                                    <span class="red"><!--{$arrErr.search_sorderyear}--></span>
                                    <span class="red"><!--{$arrErr.search_eorderyear}--></span>
                                    <select name="search_sorderyear"    style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                                    <option value="">----</option>
                                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_sorderyear.value}-->
                                    </select>年
                                    <select name="search_sordermonth" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrMonth selected=$arrForm.search_sordermonth.value}-->
                                    </select>月
                                    <select name="search_sorderday" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrDay selected=$arrForm.search_sorderday.value}-->
                                    </select>日〜
                                    <select name="search_eorderyear" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                                    <option value="">----</option>
                                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_eorderyear.value}-->
                                    </select>年
                                    <select name="search_eordermonth" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrMonth selected=$arrForm.search_eordermonth.value}-->
                                    </select>月
                                    <select name="search_eorderday" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrDay selected=$arrForm.search_eorderday.value}-->
                                    </select>日
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">更新日</td>
                                <td bgcolor="#ffffff" width="499" colspan="3">
                                    <span class="red"><!--{$arrErr.search_supdateyear}--></span>
                                    <span class="red"><!--{$arrErr.search_eupdateyear}--></span>
                                    <select name="search_supdateyear"    style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                                    <option value="">----</option>
                                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_supdateyear.value}-->
                                    </select>年
                                    <select name="search_supdatemonth" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrMonth selected=$arrForm.search_supdatemonth.value}-->
                                    </select>月
                                    <select name="search_supdateday" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrDay selected=$arrForm.search_supdateday.value}-->
                                    </select>日〜
                                    <select name="search_eupdateyear" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                                    <option value="">----</option>
                                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_eupdateyear.value}-->
                                    </select>年
                                    <select name="search_eupdatemonth" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrMonth selected=$arrForm.search_eupdatemonth.value}-->
                                    </select>月
                                    <select name="search_eupdateday" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                                    <option value="">--</option>
                                    <!--{html_options options=$arrDay selected=$arrForm.search_eupdateday.value}-->
                                    </select>日
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td bgcolor="#f2f1ec" width="110">お支払い合計</td>
                                <td bgcolor="#ffffff" width="499" colspan="3">
                                    <!--{assign var=key1 value="search_total1"}-->
                                    <!--{assign var=key2 value="search_total2"}-->
                                    <span class="red12"><!--{$arrErr[$key1]}--></span>
                                    <span class="red12"><!--{$arrErr[$key2]}--></span>
                                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"    size="6" class="box6" />

                                    円 〜
                                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"    size="6" class="box6" />
                                    円
                                </td>
                            </tr>
                        </table>
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
                                        <td class="fs12n">検索結果表示件数
                                            <!--{assign var=key value="search_page_max"}-->
                                            <span class="red12"><!--{$arrErr[$key]}--></span>
                                            <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                                            <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
                                            </select> 件
                                        </td>
                                        <td><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="10" height="1" alt=""></td>
                                        <td><input type="image" name="subm" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_search_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_search.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_search.jpg" width="123" height="24" alt="この条件で検索する" border="0" ></td>
                                    </tr>
                                </table>
                                </td>
                                <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
                            </tr>
                        </table>
                        <!--検索条件設定テーブルここまで-->
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

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete_order') }-->

<!--★★検索結果一覧★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="order_id" value="">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
    <tr><td colspan="2"><img src="<!--{$TPL_DIR}-->img/contents/search_line.jpg" width="878" height="12" alt=""></td></tr>
    <tr bgcolor="cbcbcb">
        <td>
        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
            <tr>
                <td><img src="<!--{$TPL_DIR}-->img/contents/search_left.gif" width="19" height="22" alt=""></td>
                <td>
                <!--検索結果-->
                <table border="0" cellspacing="0" cellpadding="0" summary=" ">
                    <tr>
                        <td><img src="<!--{$TPL_DIR}-->img/contents/reselt_left_top.gif" width="22" height="5" alt=""></td>
                        <td background="<!--{$TPL_DIR}-->img/contents/reselt_top_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                        <td><img src="<!--{$TPL_DIR}-->img/contents/reselt_right_top.gif" width="22" height="5" alt=""></td>
                    </tr>
                    <tr>
                        <td background="<!--{$TPL_DIR}-->img/contents/reselt_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/reselt_left_middle.gif" width="22" height="12" alt=""></td>
                        <td bgcolor="#393a48" class="white10">検索結果一覧　<span class="reselt"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。</td>
                        <td background="<!--{$TPL_DIR}-->img/contents/reselt_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="22" height="8" alt=""></td>
                    </tr>
                    <tr>
                        <td><img src="<!--{$TPL_DIR}-->img/contents/reselt_left_bottom.gif" width="22" height="5" alt=""></td>
                        <td background="<!--{$TPL_DIR}-->img/contents/reselt_bottom_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                        <td><img src="<!--{$TPL_DIR}-->img/contents/reselt_right_bottom.gif" width="22" height="5" alt=""></td>
                    </tr>
                </table>
                <!--検索結果-->
                <!--{if $smarty.const.ADMIN_MODE == '1'}-->
                <input type="button" name="subm" value="検索結果をすべて削除" onclick="fnModeSubmit('delete_all','','');" />
                <!--{/if}-->
                </td>
                <td><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="8" height="1" alt=""></td>
                <td><a href="#" onmouseover="chgImg('<!--{$TPL_DIR}-->img/contents/btn_csv_on.jpg','btn_csv');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/contents/btn_csv.jpg','btn_csv');"    onclick="fnModeSubmit('csv','','');" ><img src="<!--{$TPL_DIR}-->img/contents/btn_csv.jpg" width="99" height="22" alt="CSV DOWNLOAD" border="0" name="btn_csv" id="btn_csv"></a></td>
                <td><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="8" height="1" alt=""></td>
                <td><a href="#" onmouseover="chgImg('<!--{$TPL_DIR}-->img/contents/btn_pdf_on.jpg','btn_pdf');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/contents/btn_pdf.jpg','btn_pdf');"  onclick="fnSelectCheckSubmit('pdf.php'); return false;" ><img src="<!--{$TPL_DIR}-->img/contents/btn_pdf.jpg" width="99" height="24" alt="PDF DOWNLOAD" border="0" name="btn_pdf" id="btn_pdf"></a></td>
                <td><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="8" height="1" alt=""></td>
                <td><a href="../contents/csv.php?tpl_subno_csv=order"><span class="fs12n"> >> CSV出力項目設定 </span></a></td>
            </tr>
        </table>
        </td>
        <td align="right">
            <!--{include file=$tpl_pager}-->
        </td>
    </tr>
    <tr><td bgcolor="cbcbcb" colspan="2"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td></tr>
</table>

<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
    <tr>
        <td bgcolor="#f0f0f0" align="center">

        <!--{if count($arrResults) > 0}-->

            <table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
                <tr><td height="12"></td></tr>
                <tr>
                    <td bgcolor="#cccccc">
                    <!--検索結果表示テーブル-->
                    <table width="840" border="0" cellspacing="1" cellpadding="5" summary=" ">
                        <!--{* ペイジェントモジュール連携用 *}-->
                        <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_paygent/paygent_order_index.tpl}-->
                        <!--{if file_exists($path)}-->
                            <!--{include file=$path}-->
                        <!--{else}-->
                        <tr bgcolor="#636469" align="center" class="fs12n">
                            <td width="100"><span class="white">受注日</span></td>
                            <td width="65"><span class="white">注文番号</span></td>
                            <td width="110"><span class="white">顧客名</span></td>
                            <td width="90"><span class="white">支払方法</span></td>
                            <td width="70"><span class="white">お支払い<br />合計(円)</span></td>
                            <td width="100"><span class="white">全商品発送日</span></td>
                            <td width="75"><span class="white">対応状況</span></td>
                            <td width="45"><span class="white">帳票</span><br />
                            <input type="button" name="btn01" value="全て選択" onclick="fnBoxChecked(true);"> <input type="button" name="btn01" value="全て解除" onclick="fnBoxChecked(false);"></td>
                            <td width="45"><span class="white">編集</span></td>
                            <td width="45"><span class="white">メール</span></td>
                            <td width="45"><span class="white">削除</span></td>
                        </tr>

                        <!--{section name=cnt loop=$arrResults}-->
                        <!--{assign var=status value="`$arrResults[cnt].status`"}-->
                        <tr bgcolor="<!--{$arrORDERSTATUS_COLOR[$status]}-->" class="fs12n">
                            <td align="center"><!--{$arrResults[cnt].create_date|sfDispDBDate}--></td>
                            <td align="center"><!--{$arrResults[cnt].order_id}--></td>
                            <td><!--{$arrResults[cnt].order_name01|escape}--> <!--{$arrResults[cnt].order_name02|escape}--></td>
                            <!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
                            <td align="center"><!--{$arrPayment[$payment_id]}--></td>
                            <td align="right"><!--{$arrResults[cnt].payment_total|number_format}--></td>
                            <td align="center"><!--{$arrResults[cnt].commit_date|sfDispDBDate|default:"未発送"}--></td>
                            <td align="center"><!--{$arrORDERSTATUS[$status]}--></td>
                            <td align="center"><a href="./" onClick="fnOpenPdfSettingPage('pdf.php?order_id=<!--{$arrResults[cnt].order_id}-->','pdf_input','500','650'); return false;"><span class="icon_class">帳票</span></a><input type="checkbox" name="pdf_order_id[]" value="<!--{$arrResults[cnt].order_id}-->" >
                            </td>
                            <td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_EDIT}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_edit">編集</span></a></td>
                            <td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_MAIL}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_mail">通知</span></a></td>
                            <td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete_order', 'order_id', <!--{$arrResults[cnt].order_id}-->); return false;"><span class="icon_delete">削除</span></a></td>
                        </tr>
                        <!--{/section}-->
                        <!--{/if}-->
                    </table>
                    <!--検索結果表示テーブル-->
                    </td>
                </tr>
            </table>

        <!--{/if}-->

        </td>
    </tr>
</form>
</table>
<!--★★検索結果一覧★★-->

<!--{/if}-->
