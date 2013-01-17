<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="MY page/Details of e-mail history"}-->
<div id="window_area">
<table class="form">
    <tr>
        <td><!--{$tpl_subject|h}--></td>
    </tr>
    <tr>
        <td><!--{$tpl_body|h|nl2br}--></td>
    </tr>
</table>
</div>
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
