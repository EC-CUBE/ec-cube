<!--{*
/*
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
 */
*}-->

<script type="text/javascript">
// 表示非表示切り替え
function lfDispSwitch(id){
    var obj = document.getElementById(id);
    if (obj.style.display == 'none') {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

// セレクトボックスのリストを移動
// (移動元セレクトボックスID, 移動先セレクトボックスID)
function fnMoveSelect(select, target) {
    $('#' + select).children().each(function() {
        if (this.selected) {
            $('#' + target).append(this);
            $(this).attr({selected: false});
        }
    });
    // IE7再描画不具合対策
    if ($.browser.msie && $.browser.version >= 7) {
        $('#' + select).hide();
        $('#' + select).show();
        $('#' + target).hide();
        $('#' + target).show();
    }
}

// target の子要素を選択状態にする
function selectAll(target) {
    $('#' + target).children().attr({selected: true});
}

</script>

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="image_key" value="" />
<input type="hidden" name="down_key" value="">
<input type="hidden" name="product_id" value="<!--{$arrForm.product_id|h}-->" />
<input type="hidden" name="product_class_id" value="<!--{$arrForm.product_class_id|h}-->" />
<input type="hidden" name="copy_product_id" value="<!--{$arrForm.copy_product_id|h}-->" />
<input type="hidden" name="anchor_key" value="" />
<input type="hidden" name="select_recommend_no" value="" />
<input type="hidden" name="has_product_class" value="<!--{$arrForm.has_product_class|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">
    <h2><!--{t string="tpl_Basic information_01" escape="none"}--></h2>

    <table class="form">
        <tr>
            <th><!--{t string="tpl_Product ID_01"}--></th>
            <td><!--{$arrForm.product_id|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product name<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.name}--></span>
                <input type="text" name="name" value="<!--{$arrForm.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product category<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.category_id}--></span>
                <table class="layout">
                    <tr>
                        <td>
                            <select name="category_id[]" id="category_id" style="<!--{if $arrErr.category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> height: 120px; min-width: 200px;" onchange="" size="10" multiple>
                            </select>
                        </td>
                        <td style="padding: 15px;">
                            <a class="btn-normal" href="javascript:;" name="on_select" onclick="fnMoveSelect('category_id_unselect','category_id'); return false;">&nbsp;&nbsp;&lt;-&nbsp;<!--{t string="tpl_Register_02"}-->&nbsp;&nbsp;</a><br /><br />
                            <a class="btn-normal" href="javascript:;" name="un_select" onclick="fnMoveSelect('category_id','category_id_unselect'); return false;">&nbsp;&nbsp;<!--{t string="tpl_Remove_01"}-->&nbsp;-&gt;&nbsp;&nbsp;</a>
                        </td>
                        <td>
                            <select name="category_id_unselect[]" id="category_id_unselect" onchange="" size="10" style="height: 120px; min-width: 200px;" multiple>
                                <!--{html_options values=$arrCatVal output=$arrCatOut selected=$arrForm.category_id}-->
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Disclosed/not disclosed<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{html_radios name="status" options=$arrDISP selected=$arrForm.status separator='&nbsp;&nbsp;'}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product status_01"}--></th>
            <td>
                <!--{html_checkboxes name="product_status" options=$arrSTATUS selected=$arrForm.product_status separator='&nbsp;&nbsp;'}-->
            </td>
        </tr>
        <!--{if $arrForm.has_product_class == false}-->
        <tr>
            <th><!--{t string="tpl_Product type<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{html_radios name="product_type_id" options=$arrProductType selected=$arrForm.product_type_id separator='&nbsp;&nbsp;'}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Downloaded product file name<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.down_filename}--></span>
                <input type="text" name="down_filename" value="<!--{$arrForm.down_filename|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.down_filename != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="60" class="box60" />
                <span class="red"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <!--{assign var=key value="down_file"}-->
            <th><!--{t string="tpl_File upload for downloaded product<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <a name="<!--{$key}-->"></a>
                <span class="attention"><!--{$arrErr[$key]}--><!--{$arrErr.down_realfilename}--></span>
                    <!--{if $arrForm.down_realfilename != ""}-->
                        <!--{$arrForm.down_realfilename|h}--><input type="hidden" name="down_realfilename" value="<!--{$arrForm.down_realfilename|h}-->">
                        <a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_down', 'down_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_[Erase file]_01"}--></a><br>
                    <!--{/if}-->
                    <input type="file" name="down_file" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                    <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_down', 'down_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_Upload_01"}--></a><br /><!--{t string="tpl_Extensions for uploaded files: T_ARG1 (Parameter DOWNLOAD_EXTENSION)_01" T_ARG1=$smarty.const.DOWNLOAD_EXTENSION}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product code<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.product_code}--></span>
                <input type="text" name="product_code" value="<!--{$arrForm.product_code|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.product_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{$smarty.const.NORMAL_PRICE_TITLE}--></th>
            <td>
                <span class="attention"><!--{$arrErr.price01}--></span>
                <!--{t string="currency_prefix"}--><input type="text" name="price01" value="<!--{$arrForm.price01|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.price01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/><!--{t string="currency_suffix"}-->
                <span class="attention"> <!--{t string="tpl_(Numbers only)_01"}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_T_ARG1<span class='attention'> *</span>_01" escape="none" T_ARG1=$smarty.const.SALE_PRICE_TITLE}--></th>
            <td>
                <span class="attention"><!--{$arrErr.price02}--></span>
                <!--{t string="currency_prefix"}-->
                <input type="text" name="price02" value="<!--{$arrForm.price02|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.price02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
                <!--{t string="currency_suffix"}-->
                <span class="attention"> <!--{t string="tpl_(Numbers only)_01"}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Inventory count<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.stock}--></span>
                <input type="text" name="stock" value="<!--{$arrForm.stock|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" style="<!--{if $arrErr.stock != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
                <label><input type="checkbox" name="stock_unlimited" value="1" <!--{if $arrForm.stock_unlimited == "1"}-->checked<!--{/if}--> onclick="fnCheckStockLimit('<!--{$smarty.const.DISABLED_RGB}-->');"/><!--{t string="tpl_No limit_01"}--></label>
            </td>
        </tr>
        <!--{/if}-->

        <tr>
            <th><!--{t string="tpl_Product shipping fee_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.deliv_fee}--></span>
                <!--{t string="currency_prefix"}-->
                <input type="text" name="deliv_fee" value="<!--{$arrForm.deliv_fee|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.deliv_fee != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
                <!--{t string="currency_suffix"}-->
                <span class="attention"> <!--{t string="tpl_(Numbers only)_01"}--></span>
                <!--{if $smarty.const.OPTION_PRODUCT_DELIV_FEE != 1}--><br /><span class="attention"><!--{t string="tpl_* Currently invalid_01"}--></span> <!--{t string="tpl_(Parameter OPTION_PRODUCT_DELIV_FEE)_01"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Point grant rate<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.point_rate}--></span>
                <input type="text" name="point_rate" value="<!--{$arrForm.point_rate|default:$arrForm.arrInfo.point_rate|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PERCENTAGE_LEN}-->" style="<!--{if $arrErr.point_rate != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/><!--{t string="%"}-->
                <span class="attention"> <!--{t string="tpl_(Numbers only)_01"}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Shipment date Target_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.deliv_date_id}--></span>
                <select name="deliv_date_id" style="<!--{$arrErr.deliv_date_id|sfGetErrorColor}-->">
                    <option value=""><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrDELIVERYDATE selected=$arrForm.deliv_date_id}-->
                </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Number of sales restrictions_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.sale_limit}--></span>
                <input type="text" name="sale_limit" value="<!--{$arrForm.sale_limit|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" style="<!--{if $arrErr.sale_limit != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
                <span class="attention"> <!--{t string="tpl_(Numbers only)_01"}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Manufacturer_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.maker_id}--></span>
                <select name="maker_id" style="<!--{$arrErr.maker_id|sfGetErrorColor}-->">
                    <option value=""><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrMaker selected=$arrForm.maker_id}-->
                </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Manufacturer URL_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.comment1}--></span>
                <input type="text" name="comment1" value="<!--{$arrForm.comment1|h}-->" maxlength="<!--{$smarty.const.URL_LEN}-->" size="60" class="box60" style="<!--{$arrErr.comment1|sfGetErrorColor}-->" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.URL_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Search word *If multiple words, separate using a comma (,)_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.comment3}--></span>
                <textarea name="comment3" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.comment3|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm.comment3|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LLTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Remarks column (for SHOP)_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.note}--></span>
                <textarea name="note" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.note|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm.note|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LLTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_List - Main comments<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.main_list_comment}--></span>
                <textarea name="main_list_comment" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{if $arrErr.main_list_comment != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{"\n"}--><!--{$arrForm.main_list_comment|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.MTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Details - Main comment_01"}--><span class="attention"><!--{t string="tpl_(Tags permitted)_01"}-->*</span></th>
            <td>
                <span class="attention"><!--{$arrErr.main_comment}--></span>
                <textarea name="main_comment" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.main_comment != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{"\n"}--><!--{$arrForm.main_comment|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LLTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <!--{assign var=key value="main_list_image"}-->
            <th><!--{t string="tpl_List - Main images<br />[T_ARG1 x T_ARG2]_01" escape="none" T_ARG1=$smarty.const.SMALL_IMAGE_WIDTH T_ARG2=$smarty.const.SMALL_IMAGE_HEIGHT }--></th>
            <td>
                <a name="<!--{$key}-->"></a>
                <a name="main_image"></a>
                <a name="main_large_image"></a>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />&nbsp;<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_[Cancel image]_01"}--></a><br />
                <!--{/if}-->
                <input type="file" name="main_list_image" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_Upload_01"}--></a>
            </td>
        </tr>
        <tr>
            <!--{assign var=key value="main_image"}-->
            <th><!--{t string="tpl_Details - Main image<br />[T_ARG1 x T_ARG2]_01" escape="none" T_ARG1=$smarty.const.NORMAL_IMAGE_WIDTH T_ARG2=$smarty.const.NORMAL_IMAGE_HEIGHT }--></th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />&nbsp;<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_[Cancel image]_01"}--></a><br />
                <!--{/if}-->
                <input type="file" name="main_image" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_Upload_01"}--></a>
            </td>
        </tr>
        <tr>
            <!--{assign var=key value="main_large_image"}-->
            <th><!--{t string="tpl_Details - Main enlarged image<br />[T_ARG1 x T_ARG2]_01" escape="none" T_ARG1=$smarty.const.LARGE_IMAGE_WIDTH T_ARG2=$smarty.const.LARGE_IMAGE_HEIGHT }--></th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />&nbsp;<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_[Cancel image]_01"}--></a><br />
                <!--{/if}-->
                <input type="file" name="<!--{$key}-->" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_Upload_01"}--></a>
            </td>
        </tr>
    </table>

    <!--{* オペビルダー用 *}-->
    <!--{if "sfViewAdminOpe"|function_exists === TRUE}-->
    <!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/admin_ope_view.tpl}-->
    <!--{/if}-->

    <div class="btn">
        <a class="btn-normal" href="javascript:;" onclick="selectAll('category_id'); lfDispSwitch('sub_detail'); return false;"><span><!--{t string="tpl_Toggle display of subinformation_01"}--></span></a>
    </div>

    <!--{if $arrForm.sub_find == true}-->
    <div id="sub_detail" style="">
    <!--{else}-->
    <div id="sub_detail" style="display:none">
    <!--{/if}-->
    <h2><!--{t string="tpl_Subinformation_01"}--></h2>
    <table class="form">
        <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
        <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
        <tr>
            <th><!--{t string="tpl_Details - Sub title(T_ARG1)_01" T_ARG1=$smarty.section.cnt.iteration}--></th>
            <!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="sub_title<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrForm[$key]|h}-->" size="60" class="box60" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/>
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.STEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Details - Sub comment(T_ARG1)_01" T_ARG1=$smarty.section.cnt.iteration}--><span class="attention"><!--{t string="tpl_(Tags permitted)_01"}--></span></th>
            <!--{assign var=key value="sub_comment`$smarty.section.cnt.iteration`"}-->
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="sub_comment<!--{$smarty.section.cnt.iteration}-->" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LLTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
            <th><!--{t string="tpl_Details - Sub image(T_ARG1)<br />[T_ARG2 x T_ARG3]_01" escape="none" T_ARG1=$smarty.section.cnt.iteration T_ARG2=$smarty.const.NORMAL_SUBIMAGE_WIDTH T_ARG3=$smarty.const.NORMAL_SUBIMAGE_HEIGHT}--></th>
            <td>
                <a name="<!--{$key}-->"></a>
                <!--{assign var=largekey value="sub_large_image`$smarty.section.cnt.iteration`"}-->
                <a name="<!--{$largekey}-->"></a>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />&nbsp;<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_[Cancel image]_01"}--></a><br />
                <!--{/if}-->
                <input type="file" name="<!--{$key}-->" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/>
                <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_Upload_01"}--></a>
            </td>
        </tr>
        <tr>
            <!--{assign var=key value="sub_large_image`$smarty.section.cnt.iteration`"}-->
            <th><!--{t string="tpl_Details - Sub enlarged image(T_ARG1)<br />[T_ARG2 x T_ARG3]_01" escape="none" T_ARG1=$smarty.section.cnt.iteration T_ARG2=$smarty.const.LARGE_SUBIMAGE_WIDTH T_ARG3=$smarty.const.LARGE_SUBIMAGE_HEIGHT}--></th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />&nbsp;<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_[Cancel image]_01"}--></a><br />
                <!--{/if}-->
                <input type="file" name="<!--{$key}-->" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/>
                <a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;"><!--{t string="tpl_Upload_01"}--></a>
            </td>
        </tr>
        <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
        <!--{/section}-->
    </table>
    </div>

    <div class="btn">
        <a class="btn-normal" href="javascript:;" onclick="selectAll('category_id'); lfDispSwitch('recommend_select'); return false;"><span><!--{t string="tpl_Toggle display of related products_01"}--></span></a>
    </div>

    <!--{if $smarty.const.OPTION_RECOMMEND == 1}-->
    <!--{if count($arrRecommend) > 0}-->
    <div id="recommend_select" style="">
    <!--{else}-->
    <div id="recommend_select" style="display:none">
    <!--{/if}-->
    <h2><!--{t string="tpl_Related products_01"}--></h2>
    <table class="form">
        <!--▼関連商品-->
        <!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->
        <!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
        <tr>
            <!--{assign var=key value="recommend_id`$smarty.section.cnt.iteration`"}-->
            <!--{assign var=anckey value="recommend_no`$smarty.section.cnt.iteration`"}-->
            <th><!--{t string="tpl_Related products(T_ARG1)_01" T_ARG1=$smarty.section.cnt.iteration}--><br />
                <!--{if $arrRecommend[$recommend_no].product_id}-->
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrRecommend[$recommend_no].main_list_image|sfNoImageMainList|h}-->&width=65&height=65" alt="<!--{$arrRecommend[$recommend_no].name|h}-->" />
                <!--{/if}-->
            </th>
            <td>
                <a name="<!--{$anckey}-->"></a>
                <input type="hidden" name="<!--{$key}-->" value="<!--{$arrRecommend[$recommend_no].product_id|h}-->" />
                <a class="btn-normal" href="javascript:;" name="change" onclick="selectAll('category_id'); win03('./product_select.php?no=<!--{$smarty.section.cnt.iteration}-->', 'search', '615', '500'); return false;"><!--{t string="tpl_Change_01"}--></a>
                <!--{assign var=key value="recommend_delete`$smarty.section.cnt.iteration`"}-->
                <label><input type="checkbox" name="<!--{$key}-->" value="1" /><!--{t string="tpl_Remove_01"}--></label><br />
                <!--{assign var=key value="recommend_comment`$smarty.section.cnt.iteration`"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{t string="tpl_Product code_01"}-->:<!--{$arrRecommend[$recommend_no].product_code_min}--><br />
                <!--{t string="tpl_Product name_01"}-->:<!--{$arrRecommend[$recommend_no].name|h}--><br />
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{"\n"}--><!--{$arrRecommend[$recommend_no].comment|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LTEXT_LEN}--></span>
            </td>
        </tr>
        <!--{/section}-->
        <!--▲関連商品-->
    </table>
    </div>
    <!--{/if}-->

    <div class="btn-area">
        <!--{if count($arrSearchHidden) > 0}-->
        <!--▼検索結果へ戻る-->
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev"><!--{t string="tpl_Return to search screen_01"}--></span></a></li>
        <!--▲検索結果へ戻る-->
        <!--{/if}-->
            <li><a class="btn-action" href="javascript:;" onclick="selectAll('category_id'); document.form1.submit(); return false;"><span class="btn-next"><!--{t string="tpl_Confirmation page_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
