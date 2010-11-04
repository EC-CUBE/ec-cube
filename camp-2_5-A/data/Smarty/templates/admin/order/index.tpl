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
    function fnSelectCheckSubmit(action){

        var fm = document.form1;
        
        if (!fm["pdf_order_id[]"]) {
            return false;
        }
        
        var checkflag = false;
        var max = fm["pdf_order_id[]"].length;
        
        if (max) {
            for (var i=0; i<max; i++) {
                if(fm["pdf_order_id[]"][i].checked == true){
                    checkflag = true;
                }
            }
        } else {
            if(fm["pdf_order_id[]"].checked == true) {
                checkflag = true;
            }
        }

        if(!checkflag){
            alert('チェックボックスが選択されていません');
            return false;
        }
        
        fnOpenPdfSettingPage(action);
    }

    function fnOpenPdfSettingPage(action){
        var fm = document.form1;
        var WIN;
        WIN = window.open("about:blank", "pdf", "width=500,height=600,scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no");
        
        // 退避
        tmpTarget = fm.target;
        tmpMode = fm.mode.value;
        tmpAction = fm.action;
        
        fm.target = "pdf";
        fm.mode.value = 'pdf';
        fm.action = action;
        fm.submit();
        WIN.focus();
        
        // 復元
        fm.target = tmpTarget;
        fm.mode.value = tmpMode;
        fm.action = tmpAction;
    }
    
    function fnBoxChecked(check){
        var fm = document.form1;
        var max = fm["pdf_order_id[]"].length;
        if (max) {
            for (var i=0; i<max; i++) {
                fm["pdf_order_id[]"][i].checked = check;
            }
        } else {
            fm["pdf_order_id[]"].checked = check;
        }
    }
    
//-->
</script>
<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>
    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th>注文番号</th>
            <td>
                <!--{assign var=key1 value="search_order_id1"}-->
                <!--{assign var=key2 value="search_order_id2"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                ～ 
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th>対応状況</th>
            <td>
                <!--{assign var=key value="search_order_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>顧客名</th>
            <td>
            <!--{assign var=key value="search_order_name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>顧客名(カナ)</th>
            <td>
            <!--{assign var=key value="search_order_kana"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>
                <!--{assign var=key value="search_order_email"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>TEL</th>
            <td>
                <!--{assign var=key value="search_order_tel"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>生年月日</th>
            <td>
                <span class="attention"><!--{$arrErr.search_sbirthyear}--></span>
                <span class="attention"><!--{$arrErr.search_ebirthyear}--></span>
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
                </select>日～
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
        <tr>
            <th>性別</th>
            <td>
            <!--{assign var=key value="search_order_sex"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{html_checkboxes name="$key" options=$arrSex selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>支払方法</th>
            <td>
            <!--{assign var=key value="search_payment_id"}-->
            <span class="attention"><!--{$arrErr[$key]|escape}--></span>
            <!--{html_checkboxes name="$key" options=$arrPayment|escape selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>受注日</th>
            <td>
                <!--{if $arrErr.search_sorderyear}--><span class="attention"><!--{$arrErr.search_sorderyear}--></span><!--{/if}-->
                <!--{if $arrErr.search_eorderyear}--><span class="attention"><!--{$arrErr.search_eorderyear}--></span><!--{/if}-->
                <select name="search_sorderyear" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
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
                </select>日～
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
        <tr>
            <th>更新日</th>
            <td>
                <!--{if $arrErr.search_supdateyear}--><span class="attention"><!--{$arrErr.search_supdateyear}--></span><!--{/if}-->
                <!--{if $arrErr.search_eupdateyear}--><span class="attention"><!--{$arrErr.search_eupdateyear}--></span><!--{/if}-->
                <select name="search_supdateyear" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
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
                </select>日～
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
        <tr>
            <th>購入金額</th>
            <td>
                <!--{assign var=key1 value="search_total1"}-->
                <!--{assign var=key2 value="search_total2"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                円 ～ 
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                円
            </td>
        </tr>
        <tr>
            <th>購入商品</th>
            <td>
                <!--{assign var=key value="search_product_name"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box30" />
            </td>
        </tr>
    </table>

    <div>
        検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
        </select> 件
        <button type="submit"><span>この条件で検索する</span></button>
    </div>
    <!--検索条件設定テーブルここまで-->
</form>

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete') }-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="order_id" value="" />
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/foreach}-->
    <h2>検索結果一覧</h2>
　<p>
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
        <button type="button" onclick="fnModeSubmit('delete_all','','');"><span>検索結果をすべて削除</span></button>
        <!--{/if}-->
        <button type="button" onclick="fnModeSubmit('csv','','');">CSV DOWNLOAD</button>
        <a href="../contents/csv.php?tpl_subno_csv=order">&gt;&gt; CSV出力項目設定</a>
        <button type="button" onclick="fnSelectCheckSubmit('pdf.php');"><span>PDF一括出力</span></button>
    </p>
    <!--{include file=$tpl_pager}-->

    <!--{if count($arrResults) > 0}-->

    <!--{* 検索結果表示テーブル *}-->
    <table class="list">
        <!--{* ペイジェントモジュール連携用 *}-->
        <!--{assign var=path value=`$smarty.const.MODULE_PATH`mdl_paygent/paygent_order_index.tpl}-->
        <!--{if file_exists($path)}-->
            <!--{include file=$path}-->
        <!--{else}-->
        <tr>
            <th>受注日</th>
            <th>注文番号</th>
            <th>顧客名</th>
            <th>支払方法</th>
            <th>購入金額(円)</th>
            <th>全商品発送日</th>
            <th>対応状況</th>
            <th>
                帳票<br />
                <button type="button" onclick="fnBoxChecked(true);">全て選択</button>
                <button type="button" onclick="fnBoxChecked(false);">全て解除</button>
            </th>
            <th>編集</th>
            <th>メール</th>
            <th>削除</th>
        </tr>

        <!--{section name=cnt loop=$arrResults}-->
        <!--{assign var=status value="`$arrResults[cnt].status`"}-->
        <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;">
            <td class="center"><!--{$arrResults[cnt].create_date|sfDispDBDate}--></td>
            <td class="center"><!--{$arrResults[cnt].order_id}--></td>
            <td><!--{$arrResults[cnt].order_name01|escape}--> <!--{$arrResults[cnt].order_name02|escape}--></td>
            <!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
            <td class="center"><!--{$arrPayment[$payment_id]}--></td>
            <td class="right"><!--{$arrResults[cnt].total|number_format}--></td>
            <td class="center"><!--{$arrResults[cnt].commit_date|sfDispDBDate|default:"未発送"}--></td>
            <td class="center"><!--{$arrORDERSTATUS[$status]}--></td>
            <td class="center">
                <input type="checkbox" name="pdf_order_id[]" value="<!--{$arrResults[cnt].order_id}-->" id="pdf_order_id_<!--{$arrResults[cnt].order_id}-->"/><label for="pdf_order_id_<!--{$arrResults[cnt].order_id}-->">一括出力</label>&nbsp;
                <a href="./" onClick="win02('pdf.php?order_id=<!--{$arrResults[cnt].order_id}-->','pdf_input','500','650'); return false;"><span class="icon_class">個別出力</span></a>
            </td>
            <td class="center"><a href="?" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_EDIT}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_edit">編集</span></a></td>
            <td class="center">
                <!--{if $arrResults[cnt].order_email|strlen >= 1}-->
                    <a href="?" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_MAIL}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_mail">通知</span></a>
                <!--{/if}-->
            </td>
            <td class="center"><a href="?" onclick="fnModeSubmit('delete_order', 'order_id', <!--{$arrResults[cnt].order_id}-->); return false;"><span class="icon_delete">削除</span></a></td>
        </tr>
        <!--{/section}-->
        <!--{/if}-->
    </table>
    <!--{* 検索結果表示テーブル *}-->

    <!--{/if}-->

</form>
<!--{/if}-->
</div>
