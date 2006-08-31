<!--▼検索条件ここから-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="/img/side/title_search.jpg" width="166" height="35" alt="検索条件"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<!--検索フォーム-->
		<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="search_form" id="search_form" method="get" action="/products/list.php">
		<input type="hidden" name="mode" value="search">
			<tr><td height="10"></td></tr>
			<tr>
				<td><img src="/img/side/search_cat.gif" width="104" height="10" alt="商品カテゴリから選ぶ"></td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
					<select name="category_id">
					<option label="すべての商品" value="">全ての商品</option>
					<!--{html_options options=$arrCatList selected=$category_id}-->
					</select>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td><img src="/img/side/search_name.gif" width="66" height="10" alt="商品名を入力"></td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td><input type="text" name="name" size="18" class="box18" maxlength="50" value="<!--{$smarty.get.name|escape}-->"/></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center">
					<input type="image" onMouseover="chgImgImageSubmit('/img/side/button_search_on.gif',this)" onMouseout="chgImgImageSubmit('/img/side/button_search.gif',this)" src="/img/side/button_search.gif" width="51" height="22" alt="検索" border="0" name="search">
				</td>
			</tr>
		</form>
		</table>
		<!--検索フォーム-->
		</td>
		<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="/img/side/flame_bottom03.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--▲検索条件ここまで-->