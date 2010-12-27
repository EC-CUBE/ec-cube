<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="keyname" value="" />
<div id="system" class="contents-main">
  <div class="paging">
    <!--▼ページ送り-->
    <!--{$tpl_strnavi}-->
    <!--▲ページ送り-->
  </div>
  
  <!--▼メンバー一覧ここから-->
  <table class="list">
    <tr>
      <th>名前</th>
      <th>パス</th>
      <th>有効/無効</th>
      <th>設定</th>
    </tr>
    <!--{section name=data loop=$plugins}-->
    <!--▼メンバー<!--{$smarty.section.data.iteration}-->-->
    <tr>
      <td><!--{$plugins[data].plugin_name|escape}--></td>
      <td><!--{$plugins[data].plugin_name|escape}--></td>
      <td>
        <!--{if $plugins[data].create_date == null }-->
        <input type="hidden" name="plugin_name" value="<!--{$plugins[data].plugin_name}-->" />
           <a class="btn-normal" href="javascript:;" name="install" onclick="fnModeSubmit('install','','');">install</a>
        <!--{else}-->
          <!--{if $plugins[data].enable == 1}-->
           <a class="btn-normal" href="javascript:;" name="disable" onclick="fnModeSubmit('disable','','');">disable</a>
          <!--{else}-->
           <a class="btn-normal" href="javascript:;" name="enable" onclick="fnModeSubmit('enable','','');">enable</a>
          <!--{/if}-->
           <a class="btn-normal" href="javascript:;" name="uninstall" onclick="fnModeSubmit('uninstall','','');">uninstall</a>
        <!--{/if}-->
      </td>
      
      <td>
      <!--{if $plugins[data].create_date != null && $plugins[data].enable == 1}-->
        <input type="button" name="preference" value="preference" onclick="" />
        <!--{/if}-->
      </td>
      
    </tr>
    <!--▲メンバー<!--{$smarty.section.data.iteration}-->-->
    <!--{/section}-->
  </table>

  <div class="paging">
    <!--▼ページ送り-->
    <!--{$tpl_strnavi}-->
    <!--▲ページ送り-->
  </div>

</div>
</form>
