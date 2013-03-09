<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm" />
<!--{* ▼登録テーブルここから *}-->
<div id="basis" class="contents-main">

<h2>税金管理</h2>

    <!--{if count($arrTaxrule) > 0}-->
    <table class="list">
        <col width="5%" />
        <col width="20%" />
        <col width="20%" />
        <col width="35%" />
        <col width="10%" />
        <col width="10%" />
        <tr>
            <th>ID</th>
            <th>消費税率</th>
            <th>課税規則</th>
            <th>適用日時</th>
            <th class="edit">編集</th>
            <th class="delete">削除</th>
        </tr>
        <!--{section name=cnt loop=$arrTaxrule}-->
        <tr style="background:<!--{if $tpl_tax_rule_id != $arrTaxrule[cnt].tax_rule_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
            <!--{assign var=tax_rule_id value=$arrTaxrule[cnt].tax_rule_id}-->
            <td><!--{$tax_rule_id|h}--></td>
            <td><!--{$arrTaxrule[cnt].name|h}--></td>
            <td class="center">
            <!--{if $tpl_tax_rule_id != $arrTaxrule[cnt].tax_rule_id}-->
                <a href="?" onclick="fnModeSubmit('pre_edit', 'tax_rule_id', <!--{$arrTaxrule[cnt].tax_rule_id}-->); return false;">編集</a>
            <!--{else}-->
            編集中
            <!--{/if}-->
            </td>
            <td class="center">
            <!--{if $arrTaxrule[cnt].tax_rule_id == 0}-->
                -
            <!--{else}-->
                <a href="?" onclick="fnModeSubmit('delete', 'tax_rule_id', <!--{$arrTaxrule[cnt].tax_rule_id}-->); return false;">削除</a>
            <!--{/if}-->
            </td>
        </tr>
        <!--{/section}-->
    </table>
    <!--{/if}-->
    <table id="basis-tax-func">
        <tr>
            <th>消費税率<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.tax_rate}--></span>
                <input type="text" name="tax_rate" value="<!--{$arrForm.tax_rate|h}-->" maxlength="<!--{$smarty.const.PERCENTAGE_LEN}-->" size="6" class="box6" style="<!--{if $arrErr.tax_rate != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> ％
            </td>
        </tr>
        <tr>
            <th>課税規則<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.tax_rule}--></span>
                <!--{html_radios name="calc_rule" options=$arrTAXCALCRULE selected=$arrForm.calc_rule}-->
            </td>
        </tr>
        <tr>
            <th>適用日時<span class="attention"> *</span></th>
            <td><!--{assign var=key value="apply_date_year"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                <option value="" selected="selected">------</option>
                <!--{html_options options=$arrYear selected=$arrForm[$key]|h}-->
                </select>年
                <!--{assign var=key value="apply_date_month"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                <option value="" selected="selected">----</option>
                <!--{html_options options=$objDate->getMonth() selected=$arrForm[$key]|h}-->
                </select>月
                <!--{assign var=key value="apply_date_day"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                <option value="" selected="selected">----</option>
                <!--{html_options options=$objDate->getDay() selected=$arrForm[$key]|h}-->
                </select>日
                <!--{assign var=key value="apply_date_hour"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                <option value="" selected="selected">----</option>
                <!--{html_options options=$objDate->getHour() selected=$arrForm[$key]|h}-->
                </select>時
                <!--{assign var=key value="apply_date_minutes"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" <!--{if $arrErr[$key] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                <option value="" selected="selected">----</option>
                <!--{html_options options=$arrMinutes selected=$arrForm[$key]|h}-->
                </select>分
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'confirm', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
</div>

<!--{* ▲登録テーブルここまで *}-->
</form>
