<ul id="navi-plugin-menu" class="level1">
</ul>
<script type="text/javascript">
//<![CDATA[
var host = (("https:" == document.location.protocol) ? "<!--{$smarty.const.SSL_URL}-->" : "<!--{$smarty.const.SITE_URL}-->");
var pluginURL = host + '<!--{$smarty.const.USER_DIR}--><!--{$smarty.const.PLUGIN_DIR}-->';
$(function(){
        $.ajax({
    url: pluginURL + 'plugins.xml',
    type: 'GET',
    dataType: 'xml',
    timeout: 2000,
    error: function(){
        alert("xmlファイルの読み込みに失敗しました");
    },
    success: function(xml){
        $(xml).find("plugin").each(function(){
            var item_text = $(this).find("name").text();
            var item_path = $(this).find("path").text();

            $("<li id='navi-plugin-index'></li>")
               .html("<a href='javascript:;'><span>" + item_text + "</span></a>")
               .appendTo('ul#navi-plugin-menu')
               .click(function() {
                   win03(pluginURL + item_path 
                          + '/index.php', 'plugins', '800','600');
                   return false;
               });
        });
        $("li.plugin_menu").html("");
    }
    });
});
//]]>
</script>
