<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript"><!--
(function() {
  var eccube = function() {}
  var ownersstore = function() {
    this.setup = function() {
      var $j = jQuery.noConflict();
      $j("#loading").ajaxStart(function(){
          $j(this).show();
      });
      $j("#loading").ajaxStop(function(){
          $j(this).hide();
      });
    }
  }
  eccube.prototype = {
    ownersstore: new ownersstore()
  }
  window.eccube = eccube;
})();
    
    
    



function loadList() {
    var $j = jQuery.noConflict();

    $j("#ownerssore_loading").ajaxStart(function(){
        $j(this).html("<img src='<!--{$TPL_DIR}-->img/ajax/loading.gif'>").show();
    });
    $j("#ownerssore_loading").ajaxStop(function(){
        $j(this).hide();
    });
    
    $j.post(
        '<!--{$smarty.const.URL_DIR}-->upgrade/index.php',
        {mode: 'products_list'},
        function(resp) {
            if (resp.body) {
                $j("#ownersstore_products_list").html(resp.body);
            } else {
                $j("#ownersstore_products_list").load(
                    "<!--{$smarty.const.URL_DIR}-->upgrade/api/error.html"
                );
            }
        },
        'json'
    )
}

//-->
</script>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="">
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								
								<!--サブタイトルここから-->
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル--><!--{$tpl_subtitle}--></span></td>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
									<tr><td colspan="3" bgcolor="#ffffff" height="5"></td></tr>
									<tr>
									    <td colspan="3" bgcolor="#ffffff" align="center">
									        <input type="button" onclick="loadList();" value="オーナーズストア購入商品の一覧を取得">
									    </td>
									</tr>
									<tr>
									    <td colspan="3" bgcolor="#ffffff" height="35" align="center">
									        <div id="ownerssore_loading"></div>
									    </td>
									</tr>
								</table>
								<!--サブタイトルここまで-->
								
								<!--購入商品一覧ここから-->
								<div id="ownersstore_products_list"></div>
								<!--購入商品一覧ここまで-->
								
								</td>
								<td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
