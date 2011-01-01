<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="MYページ/メール履歴詳細"}-->

<table class="form">
  <tr>
    <th>件名</th>
    <td><!--{$tpl_subject|h}--></td>
  </tr>
  <tr>
    <th>本文</th>
    <td><!--{$tpl_body|h|nl2br}--></td>
  </tr>
</table>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
