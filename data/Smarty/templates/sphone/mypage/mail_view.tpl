<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="MYページ/メール履歴詳細"}-->

<div data-role="page" data-theme="d" id="index frame_outer">
<!--▼CONTENTS-->
   <dl class="view_detail">
      <dt><!--{$tpl_subject|h}--></dt>
      <dd><!--{$tpl_body|h|nl2br}--></dd>
   </dl>
</div>
<!--▲CONTENTS -->


<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->