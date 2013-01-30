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

<script type="text/javascript">//<![CDATA[
    $(function(){
        //お届け先エリアを非表示にする（初期値）
        if ('1' != '<!--{$arrForm.deliv_check.value}-->') {
            $("#add_deliv_area").hide();
        }
    });
    //お届け先エリアの表示/非表示
    var speed = 1000; //表示アニメのスピード（ミリ秒）
    var stateDeliv = 1;
    function fnDelivToggle(areaEl) {
        areaEl.toggle(speed);
        if (stateDeliv == 0) {
            stateDeliv = 1;
        } else {
            stateDeliv = 0
        }
    }
//]]></script>

<section id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <div class="information end">
        <span class="attention">*</span> are required fields.
    </div>

    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="nonmember_confirm" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

        <dl class="form_entry">
            <dt>Name&nbsp;<span class="attention">*</span></dt>
            <dd>
                <!--{assign var=key1 value="order_name01"}-->
                <!--{assign var=key2 value="order_name02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->"
                    value="<!--{$arrForm[$key1].value|h}-->"
                    maxlength="<!--{$arrForm[$key1].length}-->"
                    style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"
                    class="boxHarf text data-role-none" placeholder="Last name" />&nbsp;&nbsp;
                <input type="text" name="<!--{$key2}-->"
                    value="<!--{$arrForm[$key2].value|h}-->"
                    maxlength="<!--{$arrForm[$key2].length}-->"
                    style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"
                    class="boxHarf text data-role-none" placeholder="First name"/>
            </dd>

            <dt>Postal code&nbsp;<span class="attention">*</span></dt>
            <dd>
                <!--{* <!--{assign var=key1 value="order_zip01"}--> *}-->
                <!--{* <!--{assign var=key2 value="order_zip02"}--> *}-->
                <!--{assign var=key1 value="order_zipcode"}-->

                <!--{* <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span> *}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>

                <p>
                    <!--{*
                    <input type="tel" name="<!--{$key1}-->"
                        value="<!--{$arrForm[$key1].value|h}-->"
                        max="<!--{$arrForm[$key1].length}-->"
                        style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;-&nbsp;
                    <input type="tel" name="<!--{$key2}-->"
                        value="<!--{$arrForm[$key2].value|h}-->"
                        max="<!--{$arrForm[$key2].length}-->"
                        style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp; *}-->
                    <input type="tel" name="<!--{$key1}-->"
                        value="<!--{$arrForm[$key1].value|h}-->"
                        max="<!--{$arrForm[$key1].length}-->"
                        style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;
                </p>

                <!--{* <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01');" class="btn_sub btn_inputzip">Automatic address input from postal code</a> *}-->
            </dd>

            <dt>Address&nbsp;<span class="attention">*</span></dt>
            <dd>
                <span class="attention"><!--{$arrErr.order_addr01}--><!--{$arrErr.order_addr02}--></span>
                <!--{assign var=key value="order_addr01"}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" class="boxLong top data-role-none" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" placeholder="Municipality name" />
                    <!--{assign var=key value="order_addr02"}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" class="boxLong data-role-none" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" placeholder="House number/building name" />
            </dd>

            <dt>Phone number&nbsp;<span class="attention">*</span></dt>
            <dd>
                <!--{assign var=key1 value="order_tel01"}-->
                <!--{assign var=key2 value="order_tel02"}-->
                <!--{assign var=key3 value="order_tel03"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <span class="attention"><!--{$arrErr[$key3]}--></span>
                <input type="tel" name="<!--{$key1}-->"
                    value="<!--{$arrForm[$key1].value|h}-->"
                    maxlength="<!--{$arrForm[$key1].length}-->"
                    style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"
                    class="boxShort text data-role-none" />&nbsp;-&nbsp;
                <input type="tel" name="<!--{$key2}-->"
                    value="<!--{$arrForm[$key2].value|h}-->"
                    maxlength="<!--{$arrForm[$key2].length}-->"
                    style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"
                    class="boxShort text data-role-none" />&nbsp;-&nbsp;
                <input type="tel" name="<!--{$key3}-->"
                    value="<!--{$arrForm[$key3].value|h}-->"
                    maxlength="<!--{$arrForm[$key3].length}-->"
                    style="<!--{$arrErr[$key3]|sfGetErrorColor}-->"
                    class="boxShort text data-role-none" />
            </dd>

            <dt>FAX</dt>
            <dd>
                <!--{assign var=key1 value="order_fax01"}-->
                <!--{assign var=key2 value="order_fax02"}-->
                <!--{assign var=key3 value="order_fax03"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <span class="attention"><!--{$arrErr[$key3]}--></span>
                <input type="tel" name="<!--{$key1}-->"
                    value="<!--{$arrForm[$key1].value|h}-->"
                    maxlength="<!--{$arrForm[$key1].length}-->"
                    style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"
                    class="boxShort text data-role-none" />&nbsp;-&nbsp;
                <input type="tel" name="<!--{$key2}-->"
                    value="<!--{$arrForm[$key2].value|h}-->"
                    maxlength="<!--{$arrForm[$key2].length}-->"
                    style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"
                    class="boxShort text data-role-none" />&nbsp;-&nbsp;
                <input type="tel" name="<!--{$key3}-->"
                    value="<!--{$arrForm[$key3].value|h}-->"
                    maxlength="<!--{$arrForm[$key3].length}-->"
                    style="<!--{$arrErr[$key3]|sfGetErrorColor}-->"
                    class="boxShort text data-role-none" />
            </dd>

            <dt>E-mail address&nbsp;<span class="attention">*</span></dt>
            <dd>
                <!--{assign var=key value="order_email"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="email" name="<!--{$key}-->"
                    value="<!--{$arrForm[$key].value|h}-->"
                    style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
                    maxlength="<!--{$arrForm[$key].length}-->" class="boxLong top data-role-none" />
                <!--{assign var=key value="order_email02"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="email" name="<!--{$key}-->"
                    value="<!--{$arrForm[$key].value|h}-->"
                    style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
                    maxlength="<!--{$arrForm[$key].length}-->" class="boxLong data-role-none" placeholder="Input twice for confirmation" />
            </dd>

            <dt>Gender&nbsp;<span class="attention">*</span></dt>
            <dd>
                <!--{assign var=key value="order_sex"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrErr[$key]}-->
                    <!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
                <!--{/if}-->
                <p style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <input type="radio" id="man" name="<!--{$key}-->" value="1" <!--{if $arrForm[$key].value eq 1}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="man">Male</label>&nbsp;&nbsp;
                    <input type="radio" id="woman" name="<!--{$key}-->" value="2" <!--{if $arrForm[$key].value eq 2}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="woman">Female</label>
                </p>
            </dd>

            <dt>Occupation</dt>
            <dd>
                <!--{assign var=key value="order_job"}-->
                <!--{if $arrErr[$key]}-->
                    <!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
                <!--{/if}-->
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="boxLong data-role-none">
                    <option value="" selected="selected">Please make a selection</option>
                    <!--{html_options options=$arrJob selected=$arrForm[$key].value}-->
                </select>
            </dd>

            <dt>Date of birth</dt>
            <dd>
                <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
                <div class="attention"><!--{$errBirth}--></div>
                <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
                    <!--{html_options options=$arrYear selected=$arrForm.year.value|default:''}-->
                </select><span class="selectdate">Year</span>

                <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
                    <!--{html_options options=$arrMonth selected=$arrForm.month.value|default:''}-->
                </select><span class="selectdate">Month</span>

                <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
                    <!--{html_options options=$arrDay selected=$arrForm.day.value|default:''}-->
                </select><span class="selectdate">Day</span>
            </dd>

            <dt class="bg_head">
                <!--{assign var=key value="deliv_check"}-->
                <input class="radio_btn data-role-none" type="checkbox" name="<!--{$key}-->" value="1" onchange="fnDelivToggle($('#add_deliv_area')); fnCheckInputDeliv();" <!--{$arrForm[$key].value|sfGetChecked:1}--> id="deliv_label" />
                <label for="deliv_label"><span class="fb">Designation of delivery destination</span></label>
            </dt>
            <dd>
                <br />* If the address is the same as the one entered above, it can be omitted.
            </dd>

            <div id="add_deliv_area">
                <dt>Name&nbsp;<span class="attention">*</span></dt>
                <dd>
                    <!--{assign var=key1 value="shipping_name01"}-->
                    <!--{assign var=key2 value="shipping_name02"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                    <input type="text" name="<!--{$key1}-->"
                        value="<!--{$arrForm[$key1].value|h}-->"
                        maxlength="<!--{$arrForm[$key1].length}-->"
                        style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"
                        class="boxHarf text data-role-none" placeholder="Last name" />&nbsp;&nbsp;
                    <input type="text" name="<!--{$key2}-->"
                        value="<!--{$arrForm[$key2].value|h}-->"
                        maxlength="<!--{$arrForm[$key2].length}-->"
                        style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"
                        class="boxHarf text data-role-none" placeholder="First name"/>
                </dd>

                <dt>Postal code&nbsp;<span class="attention">*</span></dt>
                <dd>
                    <!--{* <!--{assign var=key1 value="shipping_zip01"}--> *}-->
                    <!--{* <!--{assign var=key2 value="shipping_zip02"}--> *}-->
                    <!--{assign var=key1 value="shipping_zipcode"}-->

                    <!--{* <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span> *}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>

                    <p>
                        <!--{*
                        <input type="tel" name="<!--{$key1}-->"
                            value="<!--{$arrForm[$key1].value|h}-->"
                            max="<!--{$arrForm[$key1].length}-->"
                            style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;-&nbsp;
                        <input type="tel" name="<!--{$key2}-->"
                            value="<!--{$arrForm[$key2].value|h}-->"
                            max="<!--{$arrForm[$key2].length}-->"
                            style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;
                        *}-->
                        <input type="tel" name="<!--{$key1}-->"
                            value="<!--{$arrForm[$key1].value|h}-->"
                            max="<!--{$arrForm[$key1].length}-->"
                            style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;
                    </p>

                    <!--{* <a href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'shipping_zip01', 'shipping_zip02', 'shipping_pref', 'shipping_addr01');" class="btn_sub btn_inputzip">Automatic address input from postal code</a> *}-->
                </dd>

                <dt>Address&nbsp;<span class="attention">*</span></dt>
                <dd>
                    <span class="attention"><!--{$arrErr.shipping_addr01}--><!--{$arrErr.shipping_addr02}--></span>
                    <!--{assign var=key value="shipping_addr01"}-->
                    <input type="text" name="<!--{$key}-->"
                        value="<!--{$arrForm[$key].value|h}-->"
                        class="boxLong top data-role-none"
                        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
                        placeholder="Municipality name" />
                    <!--{assign var=key value="shipping_addr02"}-->
                    <input type="text" name="<!--{$key}-->"
                        value="<!--{$arrForm[$key].value|h}-->"
                        class="boxLong data-role-none"
                        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
                        placeholder="House number/building name" />
                </dd>

                <dt>Phone number&nbsp;<span class="attention">*</span></dt>
                <dd>
                    <!--{assign var=key1 value="shipping_tel01"}-->
                    <!--{assign var=key2 value="shipping_tel02"}-->
                    <!--{assign var=key3 value="shipping_tel03"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                    <input type="tel" name="<!--{$key1}-->"
                        value="<!--{$arrForm[$key1].value|h}-->"
                        maxlength="<!--{$arrForm[$key1].length}-->"
                        style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"
                        class="boxShort text data-role-none" />&nbsp;-&nbsp;
                    <input type="tel" name="<!--{$key2}-->"
                        value="<!--{$arrForm[$key2].value|h}-->"
                        maxlength="<!--{$arrForm[$key2].length}-->"
                        style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"
                        class="boxShort text data-role-none" />&nbsp;-&nbsp;
                    <input type="tel" name="<!--{$key3}-->"
                        value="<!--{$arrForm[$key3].value|h}-->"
                        maxlength="<!--{$arrForm[$key3].length}-->"
                        style="<!--{$arrErr[$key3]|sfGetErrorColor}-->"
                        class="boxShort text data-role-none" />
                </dd>
                <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                    <dd class="pb">
                        <a class="btn_more" href="javascript:fnModeSubmit('multiple', '', '');">Designate multiple delivery destinations</a>
                    </dd>
                <!--{/if}-->
            </div>

            <div class="btn_area">
                <p><input type="submit" value="Next" class="btn data-role-none" alt="Next" name="next" id="next" /></p>
            </div>
        </dl>
    </form>
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="Enter keywords" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
<!--▲コンテンツここまで -->
