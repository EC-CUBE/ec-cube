<form name="form1" id="form1" method="post" action="">
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
          <input type="button" name="install" value="install" onclick="fnModeSubmit('install');" />
        <!--{else}-->
          <!--{if $plugins[data].enable == 1}-->
          <input type="button" name="disable" value="disable" onclick="fnModeSubmit('disable')" />
          <!--{else}-->
          <input type="button" name="enable" value="enable" onclick="" /> 
          <!--{/if}-->
          <input type="button" name="uninstall" value="uninstall" onclick="" />
        <!--{/if}-->
      </td>
      <td>
        <input type="button" name="preference" value="preference" />
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
