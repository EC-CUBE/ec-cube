<!--{if $cybs_disp}-->
<script type="text/javascript">
// 決済処理(与信取消/売上/返金)を行う
function doCybsApp(app, name) {
    var msg = name + '処理を実行します。よろしいですか？';
    if (window.confirm(msg)) {
        fnModeSubmit('cybs_do_ics_application','cybs_app', app);
    }
}
</script>
<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr class="fs12n">
        <td bgcolor="#f2f1ec" width="717" colspan="4">▼サイバーソース</td>
    </tr>
    <!--{if $cybs_result != ''}-->
    <tr class="fs12n">
        <td bgcolor="#f2f1ec" width="110">結果</td>
        <td bgcolor="#ffffff"><span class="red"><!--{$cybs_result}--></span></td>
    </tr>
    <!--{/if}-->
    <tr class="fs12n">
        <td bgcolor="#f2f1ec" width="110">ステータス</td>
        <td bgcolor="#ffffff">
            <!--{assign var=key value="cybs_auth_status"}-->
            <span class="red12"><!--{$arrErr[$key]}--></span>
            <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <!--{html_options options=$arrCybsAuthStatus selected=$arrCybsMemo.memo06}-->
            </select>
            <input type="button" value="変更" onClick="fnModeSubmit('cybs_change_auth_status','','');return false;">
        </td>
    </tr>
    <tr class="fs12n">
        <td bgcolor="#f2f1ec" width="110">処理</td>
        <td bgcolor="#ffffff">
        <input type="hidden" name="cybs_app" value="">
        <!--{if $arrCybsMemo.memo06 == $smarty.const.MDL_CYBS_AUTH_STATUS_AUTH}-->
        <input type="button" value="与信取消" onClick="doCybsApp('<!--{$smarty.const.MDL_CYBS_APP_REVERSAL}-->', '与信取消');return false;">　
        <input type="button" value="売上" onClick="doCybsApp('<!--{$smarty.const.MDL_CYBS_APP_BILL}-->', '売上');return false;">　
        <input type="button" value="返金" disabled="disabled">
        <!--{elseif $arrCybsMemo.memo06 == $smarty.const.MDL_CYBS_AUTH_STATUS_AUTHCANCEL}-->
        <input type="button" value="与信取消" disabled="disabled">　
        <input type="button" value="売上" disabled="disabled">　
        <input type="button" value="返金" disabled="disabled">
        <!--{elseif $arrCybsMemo.memo06 == $smarty.const.MDL_CYBS_AUTH_STATUS_CAPTURE}-->
        <input type="button" value="与信取消" disabled="disabled">　
        <input type="button" value="売上" disabled="disabled">　
        <input type="button" value="返金" onClick="doCybsApp('<!--{$smarty.const.MDL_CYBS_APP_CREDIT}-->', '返金');return false;">　
        <!--{elseif $arrCybsMemo.memo06 == $smarty.const.MDL_CYBS_AUTH_STATUS_RETURN}-->
        <input type="button" value="与信取消" disabled="disabled">　
        <input type="button" value="売上" disabled="disabled">　
        <input type="button" value="返金" disabled="disabled">
        <!--{/if}-->
        </td>
    </tr>
</table>
<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
    <tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
</table>
<!--{/if}-->