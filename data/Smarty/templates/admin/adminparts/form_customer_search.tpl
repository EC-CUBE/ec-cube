<tr>
    <th>会員ID</th>
    <td>
    <!--{assign var=key value="search_customer_id"}-->
    <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
    <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
    <th>都道府県</th>
    <td>
        <!--{assign var=key value="search_pref"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
        <select class="top" name="<!--{$key}-->">
            <option value="" selected="selected" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}-->>都道府県を選択</option>
            <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
        </select>
    </td>
</tr>
<tr>
    <th>お名前</th>
    <td>
            <!--{assign var=key value="search_name"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
            <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
    </td>
    <th>お名前(フリガナ)</th>
    <td>
        <!--{assign var=key value="search_kana"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
        <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
    </td>
</tr>
<tr>
    <th>性別</th>
    <td>
        <!--{assign var=key value="search_sex"}-->
        <!--{html_checkboxes name=$key options=$arrSex separator="&nbsp;" selected=$arrForm[$key].value}-->
    </td>
    <th>誕生月</th>
    <td>
        <!--{assign var=key value="search_birth_month"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select>月
    </td>
</tr>
<tr>
    <th>誕生日</th>
    <td colspan="3">
    <!--{assign var=errkey1 value="search_b_start_year"}-->
    <!--{assign var=errkey2 value="search_b_end_year"}-->
        <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><span class="attention"><!--{$arrErr[$errkey1]}--><!--{$arrErr[$errkey2]}--></span><br /><!--{/if}-->
        <!--{assign var=key value="search_b_start_year"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">----</option>
            <!--{html_options options=$arrBirthYear selected=$arrForm[$key].value}-->
        </select>年
        <!--{assign var=key value="search_b_start_month"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select>月
        <!--{assign var=key value="search_b_start_day"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
        </select>日～
        <!--{assign var=key value="search_b_end_year"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">----</option>
            <!--{html_options options=$arrBirthYear selected=$arrForm[$key].value}-->
        </select>年
        <!--{assign var=key value="search_b_end_month"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select>月
        <!--{assign var=key value="search_b_end_day"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
        </select>日
    </td>
</tr>
<tr>
    <th>メールアドレス</th>
    <td colspan="3">
    <!--{assign var=key value="search_email"}-->
    <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
    <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}-->/>
    </td>
</tr>
<tr>
    <th>携帯メールアドレス</th>
    <td colspan="3">
        <!--{assign var=key value="search_email_mobile"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}-->/></td>
</tr>
<tr>
    <th>電話番号</th>
    <td colspan="3">
        <!--{assign var=key value="search_tel"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
        <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" /></td>
</tr>
<tr>
    <th>職業</th>
    <td colspan="3">
        <!--{assign var=key value="search_job"}-->
        <!--{html_checkboxes name=$key options=$arrJob separator="&nbsp;" selected=$arrForm[$key].value}--></td>
</tr>
<tr>
    <th>購入金額</th>
    <td>
        <!--{assign var=key1 value="search_buy_total_from"}-->
        <!--{assign var=key2 value="search_buy_total_to"}-->
        <!--{if $arrErr[$key1] || $arrErr[$key2]}--><span class="attention">
            <!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span><br />
        <!--{/if}-->
        <input type="text" name="<!--{$key1}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key1].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円 ～
        <input type="text" name="<!--{$key2}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key2].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円
    </td>
    <th>購入回数</th>
    <td>
        <!--{assign var=key1 value="search_buy_times_from"}-->
        <!--{assign var=key2 value="search_buy_times_to"}-->
    <!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}-->
        <span class="attention"><!--{$arrErr.buy_times_from}--><!--{$arrErr.buy_times_to}--></span><br />
    <!--{/if}-->
    <input type="text" name="<!--{$key1}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key1].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 回 ～
    <input type="text" name="<!--{$key2}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key2].value|h}-->" size="6" class="box6" <!--{if $arrErr[$key1] || $arrErr[$key2]}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 回</td>
</tr>
<tr>
    <th>登録・更新日</th>
    <td colspan="3">
    <!--{assign var=errkey1 value="search_start_year"}-->
    <!--{assign var=errkey2 value="search_end_year"}-->
        <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><span class="attention"><!--{$arrErr[$errkey1]}--><!--{$arrErr[$errkey2]}--></span><br /><!--{/if}-->
        <!--{assign var=key value="search_start_year"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">----</option>
            <!--{html_options options=$arrRegistYear selected=$arrForm[$key].value}-->
        </select>年
        <!--{assign var=key value="search_start_month"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select>月
        <!--{assign var=key value="search_start_day"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
        </select>日～
        <!--{assign var=key value="search_end_year"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">----</option>
            <!--{html_options options=$arrRegistYear selected=$arrForm[$key].value}-->
        </select>年
        <!--{assign var=key value="search_end_month"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select>月
        <!--{assign var=key value="search_end_day"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
        </select>日
    </td>
</tr>
<tr>
    <th>最終購入日</th>
    <td colspan="3">
    <!--{assign var=errkey1 value="search_buy_start_year"}-->
    <!--{assign var=errkey2 value="search_buy_end_year"}-->
        <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><span class="attention"><!--{$arrErr[$errkey1]}--><!--{$arrErr[$errkey2]}--></span><br /><!--{/if}-->
        <!--{assign var=key value="search_buy_start_year"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">----</option>
            <!--{html_options options=$arrRegistYear selected=$arrForm[$key].value}-->
        </select>年
        <!--{assign var=key value="search_buy_start_month"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select>月
        <!--{assign var=key value="search_buy_start_day"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
        </select>日～
        <!--{assign var=key value="search_buy_end_year"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">----</option>
            <!--{html_options options=$arrRegistYear selected=$arrForm[$key].value}-->
        </select>年
        <!--{assign var=key value="search_buy_end_month"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
        </select>月
        <!--{assign var=key value="search_buy_end_day"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected="selected">--</option>
            <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
        </select>日
    </td>
</tr>
<tr>
    <th>購入商品名</th>
    <td>
        <!--{assign var=key value="search_buy_product_name"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"/>
        </span>
    </td>
    <th>購入商品コード</th>
    <td>
        <!--{assign var=key value="search_buy_product_code"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
    </td>
</tr>
<tr>
    <th>カテゴリ</th>
    <td colspan="3">
        <!--{assign var=key value="search_category_id"}-->
        <select name="<!--{$key}-->" <!--{if $arrErr[$errkey]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="">選択してください</option>
            <!--{html_options options=$arrCatList selected=$arrForm[$key].value}-->
        </select>
    </td>
</tr>
