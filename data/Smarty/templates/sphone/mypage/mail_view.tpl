<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="MYページ/メール履歴詳細"}-->

<table class="form">
  <tr>
    <td class="mailtd"><!--{$tpl_subject|h}--></td>
  </tr>
  <tr>
    <td class="mailtd"><!--{$tpl_body|h|nl2br}--></td>
  </tr>
</table>

<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->
