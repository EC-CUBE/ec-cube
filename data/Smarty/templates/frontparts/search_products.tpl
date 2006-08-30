				<table cellspacing="0" cellpadding="0" summary=" " id="search">
					<tr>
						<td bgcolor="#0e3192" height="3" colspan="3"></td>
					</tr>
					<tr>
						<td bgcolor="#cccccc" width="1"></td>
						<td align="center">
						
						<table width="168" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td><img src="../img/left/search_title.jpg" width="168" height="32" alt="¾¦ÉÊ¸¡º÷" /></td>
							</tr>
						</table>
						
						<table width="150" cellspacing="0" cellpadding="0" summary=" " border="0">
							<tr><td height="10"></td></tr>
							<!--¾¦ÉÊ¸¡º÷¥Õ¥©¡¼¥à-->
							<form name="search_form" id="search_form" method="get" action="/products/list.php">
							<input type="hidden" name="mode" value="search">
							<tr>
								<td align="center">
									<select name="category_id">
									<!--{html_options options=$arrCatList selected=$category_id}-->
									</select>
								</td>
								<td>
									<img src="../img/_.gif" width="3" height="22"/><td align="center"><a href="#" onclick="document.search_form.submit();return false;"><img src="../img/left/search_button_mini.gif" width="35" height="22" alt="¸¡º÷" /></a></td>
								</td>								
							</tr>							
							<tr><td height="5"></td></tr>
							<!--{*
							<tr>
								<td align="center"><input type="text" name="name" size="18" class="box18" maxlength="50" value="<!--{$smarty.get.name|escape}-->"/></td>
							</tr>
							*}-->
							</form>
							<!--¾¦ÉÊ¸¡º÷¥Õ¥©¡¼¥à-->
						</table>
						</td>
						<td bgcolor="#cccccc" width="1"></td>
					</tr>
					<tr>
						<td colspan="3"><img src="../img/left/search_bottom.gif" width="170" height="10" alt="" /></td>
					</tr>
					<tr><td height="15"></td></tr>
				</table>