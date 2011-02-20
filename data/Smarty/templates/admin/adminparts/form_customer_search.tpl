    <tr>
      <th>顧客ID</th>
      <td><!--{if $arrErr.customer_id}--><span class="attention"><!--{$arrErr.customer_id}--></span><br /><!--{/if}--><input type="text" name="customer_id" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.customer_id|h}-->" size="30" class="box30" <!--{if $arrErr.customer_id}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
      <th>都道府県</th>
      <td>
        <!--{if $arrErr.pref}--><span class="attention"><!--{$arrErr.pref}--></span><br /><!--{/if}-->
        <select class="top" name="pref">
          <option value="" selected="selected" <!--{if $arrErr.name}--><!--{sfSetErrorStyle}--><!--{/if}-->>都道府県を選択</option>
          <!--{html_options options=$arrPref selected=$arrForm.pref}-->
        </select>
      </td>
    </tr>
    <tr>
      <th>顧客名</th>
      <td><!--{if $arrErr.name}--><span class="attention"><!--{$arrErr.name}--></span><br /><!--{/if}--><input type="text" name="name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.name|h}-->" size="30" class="box30" <!--{if $arrErr.name}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
      <th>顧客名(カナ)</th>
      <td><!--{if $arrErr.kana}--><span class="attention"><!--{$arrErr.kana}--></span><br /><!--{/if}--><input type="text" name="kana" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.kana|h}-->" size="30" class="box30" <!--{if $arrErr.kana}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
    </tr>
    <tr>
      <th>性別</th>
      <td><!--{html_checkboxes name="sex" options=$arrSex separator="&nbsp;" selected=$arrForm.sex}--></td>
      <th>誕生月</th>
      <td><!--{if $arrErr.birth_month}--><span class="attention"><!--{$arrErr.birth_month}--></span><br /><!--{/if}-->
        <select name="birth_month" style="<!--{$arrErr.birth_month|sfGetErrorColor}-->" >
          <option value="" selected="selected">--</option>
          <!--{html_options options=$objDate->getMonth() selected=$arrForm.birth_month}-->
        </select>月
      </td>
    </tr>
    <tr>
      <th>誕生日</th>
      <td colspan="3">
        <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><span class="attention"><!--{$arrErr.b_start_year}--><!--{$arrErr.b_end_year}--></span><br /><!--{/if}-->
        <select name="b_start_year" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.b_start_year}-->
        </select>年
        <select name="b_start_month" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.b_start_month}-->
        </select>月
        <select name="b_start_day" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.b_start_day}-->
        </select>日～
        <select name="b_end_year" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.b_end_year}-->
        </select>年
        <select name="b_end_month" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.b_end_month}-->
        </select>月
        <select name="b_end_day" <!--{if $arrErr.b_start_year || $arrErr.b_end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.b_end_day}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th>メールアドレス</th>
      <td colspan="3"><!--{if $arrErr.email}--><span class="attention"><!--{$arrErr.email}--></span><!--{/if}--><input type="text" name="email" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.email|h}-->" size="60" class="box60" <!--{if $arrErr.email}--><!--{sfSetErrorStyle}--><!--{/if}-->/></td>
    </tr>
    <tr>
      <th>携帯メールアドレス</th>
      <td colspan="3"><!--{if $arrErr.email_mobile}--><span class="attention"><!--{$arrErr.email_mobile}--></span><!--{/if}--><input type="text" name="email_mobile" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.email_mobile|h}-->" size="60" class="box60" <!--{if $arrErr.email_mobile}--><!--{sfSetErrorStyle}--><!--{/if}-->/></td>
    </tr>
    <tr>
      <th>電話番号</th>
      <td colspan="3"><!--{if $arrErr.tel}--><span class="attention"><!--{$arrErr.tel}--></span><br /><!--{/if}--><input type="text" name="tel" maxlength="<!--{$smarty.const.TEL_LEN}-->" value="<!--{$arrForm.tel|h}-->" size="60" class="box60" /></td>
    </tr>
    <tr>
      <th>職業</th>
      <td colspan="3"><!--{html_checkboxes name="job" options=$arrJob separator="&nbsp;" selected=$arrForm.job}--></td>
    </tr>
    <tr>
      <th>購入金額</th>
      <td><!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><span class="attention"><!--{$arrErr.buy_total_from}--><!--{$arrErr.buy_total_to}--></span><br /><!--{/if}--><input type="text" name="buy_total_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_total_from|h}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円 ～ <input type="text" name="buy_total_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_total_to|h}-->" size="6" class="box6" <!--{if $arrErr.buy_total_from || $arrErr.buy_total_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 円</td>
      <th>購入回数</th>
      <td><!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><span class="attention"><!--{$arrErr.buy_times_from}--><!--{$arrErr.buy_times_to}--></span><br /><!--{/if}--><input type="text" name="buy_times_from" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_times_from|h}-->" size="6" class="box6" <!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 回 ～ <input type="text" name="buy_times_to" maxlength="<!--{$smarty.const.INT_LEN}-->" value="<!--{$arrForm.buy_times_to|h}-->" size="6" class="box6" <!--{if $arrErr.buy_times_from || $arrErr.buy_times_to}--><!--{sfSetErrorStyle}--><!--{/if}--> /> 回</td>
    </tr>
    <tr>
      <th>登録・更新日</th>
      <td colspan="3">
        <!--{if $arrErr.start_year || $arrErr.end_year}--><span class="attention"><!--{$arrErr.start_year}--><!--{$arrErr.end_year}--></span><br /><!--{/if}-->
        <select name="start_year" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.start_year}-->
        </select>年
        <select name="start_month" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.start_month}-->
        </select>月
        <select name="start_day" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.start_day}-->
        </select>日～
        <select name="end_year" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">----</option>
          <!--{html_options options=$arrYear selected=$arrForm.end_year}-->
        </select>年
        <select name="end_month" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.end_month}-->
        </select>月
        <select name="end_day" <!--{if $arrErr.start_year || $arrErr.end_year}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.end_day}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th>最終購入日</th>
      <td colspan="3">
        <!--{if $arrErr.buy_start_year || $arrErr.buy_end_year}--><span class="attention"><!--{$arrErr.buy_start_year}--><!--{$arrErr.buy_end_year}--></span><br /><!--{/if}-->
        <select name="buy_start_year" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
          <option value="" selected="selected">----</option>
          <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR)  selected=$arrForm.buy_start_year}-->
        </select>年
        <select name="buy_start_month" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.buy_start_month}-->
        </select>月
        <select name="buy_start_day" style="<!--{$arrErr.buy_start_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.buy_start_day}-->
        </select>日～
        <select name="buy_end_year" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
          <option value="" selected="selected">----</option>
          <!--{html_options options=$objDate->getYear($smarty.const.RELEASE_YEAR)  selected=$arrForm.buy_end_year}-->
        </select>年
        <select name="buy_end_month" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrMonth selected=$arrForm.buy_end_month}-->
        </select>月
        <select name="buy_end_day" style="<!--{$arrErr.buy_end_year|sfGetErrorColor}-->">
          <option value="" selected="selected">--</option>
          <!--{html_options options=$arrDay selected=$arrForm.buy_end_day}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th>購入商品名</th>
      <td>
        <!--{if $arrErr.buy_product_name}--><span class="attention"><!--{$arrErr.buy_product_name}--></span><!--{/if}-->
        <span style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->">
        <input type="text" name="buy_product_name" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$arrForm.buy_product_name|h}-->" size="30" class="box30" style="<!--{$arrErr.buy_product_name|sfGetErrorColor}-->"/>
        </span>
      </td>
      <th>購入商品コード</th>
      <td>
        <!--{if $arrErr.buy_product_code}--><span class="attention"><!--{$arrErr.buy_product_code}--></span><!--{/if}-->
        <input type="text" name="buy_product_code" value="<!--{$arrForm.buy_product_code}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" style="<!--{$arrErr.buy_product_code|sfGetErrorColor}-->" >
      </td>
    </tr>
    <tr>
      <th>カテゴリ</th>
      <td colspan="3">
        <select name="category_id" style="<!--{if $arrErr.category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
          <option value="">選択してください</option>
          <!--{html_options options=$arrCatList selected=$arrForm.category_id}-->
        </select>
      </td>
    </tr>