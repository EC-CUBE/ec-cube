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

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrForm}-->
    <!--{if $key == 'product_status'}-->
        <!--{foreach item=statusVal from=$item}-->
            <input type="hidden" name="<!--{$key}-->[]" value="<!--{$statusVal|h}-->" />
        <!--{/foreach}-->
    <!--{elseif $key == 'arrCategoryId'}-->
        <!--{* nop *}-->
    <!--{elseif $key == 'arrFile'}-->
        <!--{* nop *}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<div id="products" class="contents-main">

    <table>
        <tr>
            <th><!--{t string="tpl_188"}--></th>
            <td>
                <!--{$arrForm.name|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_498"}--></th>
            <td>
                <!--{section name=cnt loop=$arrForm.arrCategoryId}-->
                    <!--{assign var=key value=$arrForm.arrCategoryId[cnt]}-->
                    <!--{$arrCatList[$key]|sfTrim}--><br />
                <!--{/section}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_499"}--></th>
            <td>
                <!--{$arrDISP[$arrForm.status]}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_554"}--></th>
            <td>
                <!--{if count($arrForm.product_status) > 0}-->
                    <ul class="status_icon clearfix">
                        <!--{foreach from=$arrForm.product_status item=status}-->
							<li class="status_<!--{$status}-->">
							   <span id="icon<!--{$status}-->"><!--{$arrSTATUS[$status]}--></span>
							</li>						
                        <!--{/foreach}-->
                    </ul>
                <!--{/if}-->
			    
            </td>
        </tr>

        <!--{if $arrForm.has_product_class != true}-->
            <tr>
                <th><!--{t string="tpl_Product type_01"}--></th>
                <td>
                    <!--{$arrProductType[$arrForm.product_type_id]}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_555"}--></th>
                <td>
                    <!--{$arrForm.down_filename|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_556" escape="none"}--></th>
                <td>
                    <!--{if $arrForm.down_realfilename != ""}-->
                        <!--{$arrForm.down_realfilename|h}-->
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_192"}--></th>
                <td>
                    <!--{$arrForm.product_code|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{$smarty.const.NORMAL_PRICE_TITLE}--></th>
                <td>
                    <!--{if strlen($arrForm.price01) >= 1}--><!--{t string="currency_prefix"}--><!--{$arrForm.price01|h}--><!--{t string="currency_suffix"}--><!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><!--{$smarty.const.SALE_PRICE_TITLE}--></th>
                <td>
                    <!--{if strlen($arrForm.price02) >= 1}--><!--{t string="currency_prefix"}--><!--{$arrForm.price02|h}--><!--{t string="currency_suffix"}--><!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_557"}--></th>
                <td>
                    <!--{if $arrForm.stock_unlimited == 1}-->
                        <!--{t string="tpl_053"}-->
                    <!--{else}-->
                        <!--{$arrForm.stock|h}-->
                    <!--{/if}-->
                </td>
            </tr>
        <!--{/if}-->

        <tr>
            <th><!--{t string="tpl_558"}--></th>
            <td>
                <!--{if strlen($arrForm.deliv_fee) >= 1}--><!--{t string="currency_prefix"}--><!--{$arrForm.deliv_fee|h}--><!--{t string="currency_suffix"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_559"}--></th>
            <td>
                <!--{if strlen($arrForm.point_rate) >= 1}--><!--{$arrForm.point_rate|h}--> <!--{t string="%"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_560"}--></th>
            <td>
                <!--{$arrDELIVERYDATE[$arrForm.deliv_date_id]|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_561"}--></th>
            <td>
                <!--{$arrForm.sale_limit|default_t:"tpl_053"|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_562"}--></th>
            <td>
                <!--{$arrMaker[$arrForm.maker_id]|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_563"}--></th>
            <td style="word-break: break-all;">
                <!--{$arrForm.comment1|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_564"}--></th>
            <td>
                <!--{$arrForm.comment3|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_565"}--></th>
            <td>
                <!--{$arrForm.note|h|nl2br}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_566"}--></th>
            <td>
                <!--{$arrForm.main_list_comment|h|nl2br}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_567"}--></th>
            <td>
                <!--{$arrForm.main_comment|nl2br_html}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_568"}--></th>
            <td>
                <!--{assign var=key value="main_list_image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                    <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_569"}--></th>
            <td>
                <!--{assign var=key value="main_image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                    <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_570"}--></th>
            <td>
                <!--{assign var=key value="main_large_image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                    <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>

        <!--{* オペビルダー用 *}-->
        <!--{if "sfViewAdminOpe"|function_exists === TRUE}-->
            <!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/admin_ope_view.tpl}-->
        <!--{/if}-->

        <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
            <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
            <tr>
                <th><!--{t string="tpl_571" T_FIELD=$smarty.section.cnt.iteration}--></th>
                <td>
                    <!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
                    <!--{$arrForm[$key]|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_572" T_FIELD=$smarty.section.cnt.iteration}--></th>
                <td>
                    <!--{assign var=key value="sub_comment`$smarty.section.cnt.iteration`"}-->
                    <!--{$arrForm[$key]|nl2br_html}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_573" T_FIELD=$smarty.section.cnt.iteration}--></th>
                <td>
                    <!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
                    <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                        <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_574" T_FIELD=$smarty.section.cnt.iteration}--></th>
                <td>
                    <!--{assign var=key value="sub_large_image`$smarty.section.cnt.iteration`"}-->
                    <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                        <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                    <!--{/if}-->
                </td>
            </tr>
            <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
        <!--{/section}-->

        <!--{if $smarty.const.OPTION_RECOMMEND == 1}-->
            <!--▼関連商品-->
            <!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->
            <!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
                <tr>
                    <th><!--{t string="tpl_575" T_FIELD=$smarty.section.cnt.iteration}--><br />
                        <!--{if $arrRecommend[$recommend_no].product_id|strlen >= 1}-->
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrRecommend[$recommend_no].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[$recommend_no].name|h}-->">
                        <!--{/if}-->
                    </th>
                    <td>
                        <!--{if $arrRecommend[$recommend_no].product_id|strlen >= 1}-->
                            <!--{t string="tpl_192"}-->:<!--{$arrRecommend[$recommend_no].product_code_min}--><br />
                            <!--{t string="tpl_188"}-->:<!--{$arrRecommend[$recommend_no].name|h}--><br />
                            <!--{t string="tpl_576"}--><br />
                            <!--{$arrRecommend[$recommend_no].comment|h|nl2br}-->
                        <!--{/if}-->
                    </td>
                </tr>
            <!--{/section}-->
            <!--▲関連商品-->
        <!--{/if}-->
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev"><!--{t string="tpl_Return to previous page_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
