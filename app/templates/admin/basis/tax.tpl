<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="tax_rule_id" value="<!--{$tpl_tax_rule_id|h}-->" />
<!--{* ▼登録テーブルここから *}-->
<div id="basis" class="contents-main">

<!--{* 軽減税率対応 軽減税率が無いとなれば、税率設定より下に移動しても良いかと思います。 *}-->
<h2>個別税率設定</h2>

    <table id="basis-tax-func">
        <tr>
            <th>商品別税率機能<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.product_tax_flg}--></span>
                <span style="<!--{$arrErr.product_tax_flg|sfGetErrorColor}-->">
                <!--{html_radios name="product_tax_flg" options=$arrEnable selected=$arrForm.product_tax_flg.value}-->
                </span>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="eccube.fnFormModeSubmit('form1', 'param_edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>


<h2>共通税率設定</h2>
    <span class="attention"><!--{$arrErr.tax_rule_id}--></span>

    <table id="basis-tax-func">
        <tr>
            <th>消費税率<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.tax_rate}--></span>
                <input type="text" name="tax_rate" value="<!--{$arrForm.tax_rate.value|h}-->" maxlength="<!--{$smarty.const.PERCENTAGE_LEN}-->" size="6" class="box6" style="<!--{$arrErr.tax_rate|sfGetErrorColor}-->" /> ％
            </td>
        </tr>
        <tr>
            <th>課税規則<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.calc_rule}--></span>
                <span style="<!--{$arrErr.calc_rule|sfGetErrorColor}-->">
                <!--{html_radios name="calc_rule" options=$arrTAXCALCRULE selected=$arrForm.calc_rule.value}-->
                </span>
            </td>
        </tr>
        <!--{if $tpl_tax_rule_id != "0"}-->
        <tr>
            <th>適用日時<span class="attention"> *</span></th>
            <td><span class="attention"><!--{$arrErr.apply_date}--></span>
                <!--{assign var=key1 value="apply_date_year"}-->
                <!--{assign var=key2 value="apply_date_month"}-->
                <!--{assign var=key3 value="apply_date_day"}-->
                <!--{assign var=key4 value="apply_date_hour"}-->
                <!--{assign var=key5 value="apply_date_minutes"}-->
                
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <span class="attention"><!--{$arrErr[$key3]}--></span>
                <span class="attention"><!--{$arrErr[$key4]}--></span>
                <span class="attention"><!--{$arrErr[$key5]}--></span>

                <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                <option value="" selected="selected">------</option>
                <!--{html_options options=$arrYear selected=$arrForm[$key1].value|h}-->
                </select>年
                <select name="<!--{$key2}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->">
                <option value="" selected="selected">----</option>
                <!--{html_options options=$objDate->getMonth() selected=$arrForm[$key2].value|h}-->
                </select>月
                <select name="<!--{$key3}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
                <option value="" selected="selected">----</option>
                <!--{html_options options=$objDate->getDay() selected=$arrForm[$key3].value|h}-->
                </select>日
                <select name="<!--{$key4}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                <option value="" selected="selected">----</option>
                <!--{html_options options=$objDate->getHour() selected=$arrForm[$key4].value|h}-->
                </select>時
                <select name="<!--{$key5}-->" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->">
                <option value="" selected="selected">----</option>
                <!--{html_options options=$arrMinutes selected=$arrForm[$key5].value|h}-->
                </select>分
            </td>
        </tr>
        <!--{else}-->
        <input type="hidden" name="apply_date_year" value="<!--{$arrForm.apply_date_year.value|h}-->" />
        <input type="hidden" name="apply_date_month" value="<!--{$arrForm.apply_date_month.value|h}-->" />
        <input type="hidden" name="apply_date_day" value="<!--{$arrForm.apply_date_day.value|h}-->" />
        <input type="hidden" name="apply_date_hour" value="<!--{$arrForm.apply_date_hour.value|h}-->" />
        <input type="hidden" name="apply_date_minutes" value="<!--{$arrForm.apply_date_minutes.value|h}-->" />
        <!--{/if}-->
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="eccube.fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
    <!--{if count($arrTaxrule) > 0}-->
    <table class="list">
        <col width="22%" />
        <col width="23%" />
        <col width="35%" />
        <col width="10%" />
        <col width="10%" />
        <tr>
            <th>消費税率</th>
            <th>課税規則</th>
            <th>適用日時</th>
            <th class="edit">編集</th>
            <th class="delete">削除</th>
        </tr>
        <!--{section name=cnt loop=$arrTaxrule}-->
        <tr style="background:<!--{if $tpl_tax_rule_id != $arrTaxrule[cnt].tax_rule_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
            <td class="center"><!--{$arrTaxrule[cnt].tax_rate|h}--></td>
            <td class="center"><!--{assign var=calc_rule value=$arrTaxrule[cnt].calc_rule}-->
                <!--{$arrTAXCALCRULE[$calc_rule]}--></td>
            <td class="center">
                <!--{if $arrTaxrule[cnt].tax_rule_id == 0}-->
                基本税率設定
                <!--{else}-->
                <!--{$arrTaxrule[cnt].apply_date|h}-->
                <!--{/if}-->
            </td>
            <td class="center">
            <!--{if $tpl_tax_rule_id != $arrTaxrule[cnt].tax_rule_id}-->
                <a href="?" onclick="eccube.setModeAndSubmit('pre_edit', 'tax_rule_id', '<!--{$arrTaxrule[cnt].tax_rule_id}-->'); return false;">編集</a>
            <!--{else}-->
                編集中
            <!--{/if}-->
            </td>
            <td class="center">
            <!--{if $arrTaxrule[cnt].tax_rule_id == 0}-->
                -
            <!--{else}-->
                <a href="?" onclick="eccube.setModeAndSubmit('delete', 'tax_rule_id', '<!--{$arrTaxrule[cnt].tax_rule_id}-->'); return false;">削除</a>
            <!--{/if}-->
            </td>
        </tr>
        <!--{/section}-->
    </table>
    <!--{/if}-->
</div>

<!--{* ▲登録テーブルここまで *}-->
</form>
