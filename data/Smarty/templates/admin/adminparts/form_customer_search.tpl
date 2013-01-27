<tr>
    <th><!--{t string="tpl_Member ID_01"}--></th>
    <td colspan="3">
    <!--{assign var=key value="search_customer_id"}-->
    <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
    <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>

</tr>
<tr>
    <th><!--{t string="tpl_Name_02"}--></th>
    <td colspan="3">
            <!--{assign var=key value="search_name"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
            <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_Gender_01"}--></th>
    <td>
        <!--{assign var=key value="search_sex"}-->
        <!--{html_checkboxes name=$key options=$arrSex separator="&nbsp;" selected=$arrForm[$key].value}-->
    </td>
    <th><!--{t string="tpl_Birth month_01"}--></th>
    <td>
        <!--{assign var=key value="search_birth_month"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select><!--{t string="c_Month_01"}-->
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_Birthday_01"}--></th>
    <td colspan="3">
        <!--{if $arrErr.search_b_start_year || $arrErr.search_b_end_year}-->
        <span class="attention"><!--{$arrErr.search_b_start_year}--></span>
        <span class="attention"><!--{$arrErr.search_b_endy_ear}--></span>
        <!--{/if}-->
        <input id="datepickercustomersearch_b_start"
               type="text"
               value="" <!--{if $arrErr.search_b_start_year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <input type="hidden" name="search_b_start_year" value="<!--{$arrForm.search_b_start_year.value|h}-->" />
        <input type="hidden" name="search_b_start_month" value="<!--{$arrForm.search_b_start_month.value|h}-->" />
        <input type="hidden" name="search_b_start_day" value="<!--{$arrForm.search_b_start_day.value|h}-->" />
        <!--{t string="-"}-->
        <input id="datepickercustomersearch_b_end"
               type="text"
               value="" <!--{if $arrErr.search_b_end_year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <input type="hidden" name="search_b_end_year" value="<!--{$arrForm.search_b_end_year.value|h}-->" />
        <input type="hidden" name="search_b_end_month" value="<!--{$arrForm.search_b_end_month.value|h}-->" />
        <input type="hidden" name="search_b_end_day" value="<!--{$arrForm.search_b_end_day.value|h}-->" />
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_E-mail address_01"}--></th>
    <td colspan="3">
    <!--{assign var=key value="search_email"}-->
    <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
    <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}-->/>
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_Mobile e-mail address_01"}--></th>
    <td colspan="3">
        <!--{assign var=key value="search_email_mobile"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}-->/></td>
</tr>
<tr>
    <th><!--{t string="tpl_Telephone number_01"}--></th>
    <td colspan="3">
        <!--{assign var=key value="search_tel"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
        <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" /></td>
</tr>
<tr>
    <th><!--{t string="tpl_Occupation_02"}--></th>
    <td colspan="3">
        <!--{assign var=key value="search_job"}-->
        <!--{html_checkboxes name=$key options=$arrJob separator="&nbsp;" selected=$arrForm[$key].value}--></td>
</tr>
<tr>
    <th><!--{t string="tpl_Purchase amount_01"}--></th>
    <td>
        <!--{assign var=key1 value="search_buy_total_from"}-->
        <!--{assign var=key2 value="search_buy_total_to"}-->
        <!--{if $arrErr[$key1] || $arrErr[$key2]}--><span class="attention">
            <!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span><br />
        <!--{/if}-->
        <!--{t string="currency_prefix"}-->
        <input type="text" name="<!--{$key1}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key1].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 
        <!--{t string="currency_suffix"}-->
        <!--{t string="-"}-->
        <!--{t string="currency_prefix"}-->
        <input type="text" name="<!--{$key2}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key2].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 
        <!--{t string="currency_suffix"}-->
    </td>
    <th><!--{t string="tpl_Purchase frequency_01"}--></th>
    <td>
        <!--{assign var=key1 value="search_buy_times_from"}-->
        <!--{assign var=key2 value="search_buy_times_to"}-->
        <!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}-->
            <span class="attention"><!--{$arrErr.buy_times_from}--><!--{$arrErr.buy_times_to}--></span><br />
        <!--{/if}-->
        <!--{t string="times_prefix"}-->
        <input type="text" name="<!--{$key1}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key1].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 
        <!--{t string="times_suffix"}--> 
        <!--{t string="-"}-->
        <!--{t string="times_prefix"}-->
        <input type="text" name="<!--{$key2}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key2].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 
        <!--{t string="times_suffix"}-->
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_Registration/update date_01"}--></th>
    <td colspan="3">
        <!--{if $arrErr.search_start_year || $arrErr.search_end_year}-->
        <span class="attention"><!--{$arrErr.search_start_year}--></span>
        <span class="attention"><!--{$arrErr.search_endy_ear}--></span>
        <!--{/if}-->
        <input id="datepickercustomersearch_start"
               type="text"
               value="" <!--{if $arrErr.search_start_year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <input type="hidden" name="search_start_year" value="<!--{$arrForm.search_start_year.value|h}-->" />
        <input type="hidden" name="search_start_month" value="<!--{$arrForm.search_start_month.value|h}-->" />
        <input type="hidden" name="search_start_day" value="<!--{$arrForm.search_start_day.value|h}-->" />
        <!--{t string="-"}-->
        <input id="datepickercustomersearch_end"
               type="text"
               value="" <!--{if $arrErr.search_end_year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <input type="hidden" name="search_end_year" value="<!--{$arrForm.search_end_year.value|h}-->" />
        <input type="hidden" name="search_end_month" value="<!--{$arrForm.search_end_month.value|h}-->" />
        <input type="hidden" name="search_end_day" value="<!--{$arrForm.search_end_day.value|h}-->" />
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_Final purchase date_01"}--></th>
    <td colspan="3">
        <!--{if $arrErr.search_buy_start_year || $arrErr.search_buy_end_year}-->
        <span class="attention"><!--{$arrErr.search_buy_start_year}--></span>
        <span class="attention"><!--{$arrErr.search_buy_end_year}--></span>
        <!--{/if}-->
        <input id="datepickercustomersearch_buy_start"
               type="text"
               value="" <!--{if $arrErr.search_buy_start_year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <input type="hidden" name="search_buy_start_year" value="<!--{$arrForm.search_buy_start_year.value|h}-->" />
        <input type="hidden" name="search_buy_start_month" value="<!--{$arrForm.search_buy_start_month.value|h}-->" />
        <input type="hidden" name="search_buy_start_day" value="<!--{$arrForm.search_buy_start_day.value|h}-->" />
        <!--{t string="-"}-->
        <input id="datepickercustomersearch_buy_end"
               type="text"
               value="" <!--{if $arrErr.search_buy_end_year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <input type="hidden" name="search_buy_end_year" value="<!--{$arrForm.search_buy_end_year.value|h}-->" />
        <input type="hidden" name="search_buy_end_month" value="<!--{$arrForm.search_buy_end_month.value|h}-->" />
        <input type="hidden" name="search_buy_end_day" value="<!--{$arrForm.search_buy_end_day.value|h}-->" />
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_Purchased product name_01"}--></th>
    <td>
        <!--{assign var=key value="search_buy_product_name"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/>
        </span>
    </td>
    <th><!--{t string="tpl_Purchased product code_01"}--></th>
    <td>
        <!--{assign var=key value="search_buy_product_code"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
    </td>
</tr>
<tr>
    <th><!--{t string="tpl_Category_01"}--></th>
    <td colspan="3">
        <!--{assign var=key value="search_category_id"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value=""><!--{t string="tpl_Please make a selection_01"}--></option>
            <!--{html_options options=$arrCatList selected=$arrForm[$key].value}-->
        </select>
    </td>
</tr>
