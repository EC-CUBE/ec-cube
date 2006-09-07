<style type="text/css">    
    div.dragged_elm {
        position:   absolute;
        border:     1px solid black;
        background: rgb(195,217,255);
        color:      #333;
        cursor:		hand;
        PADDING-RIGHT: 	2px;
        PADDING-LEFT: 	2px;
        PADDING-BOTTOM: 5px; 
        PADDING-TOP: 	5px;
        FONT-SIZE: 		10pt;
    }

    div.drop_target {
        border:      0px solid gray;
        position:    relative;
        text-align:  center;
        color:       #333;
    }

</style>
<script type="text/javascript">

function doPreview(){
	document.form1.mode.value="preview"
	document.form1.target = "_blank";
	document.form1.submit();
}
function fnTargetSelf(){
	document.form1.target = "_self";
}
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="page_id" value="<!--{$page_id}-->">
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt}-->">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg" >
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--メインエリア-->
			<tr>
				<td align="center">
				<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">

					<tr><td height="14"></td></tr>
					<tr>
						<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->レイアウト編集</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--▼レイアウト編集　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center"><strong>レイアウト編集</strong></td>
								<td bgcolor="#f2f1ec" align="center"><strong>未使用ブロック</strong></td>
							</tr>
							<tr class="fs12n">
								<!--▼レイアウト　ここから-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="450" border=0 cellspacing="1" cellpadding="" summary=" " bgcolor="ffffff">
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height = 50>
											<td bgcolor="#cccccc" align="center" colspan=3> ヘッダー部 </td>
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n">
											<!-- ★☆★ 左ナビテーブル ☆★☆ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" height="400" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center" id="layout">
															<div tid="LeftNavi" class="drop_target" id="t1" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ★☆★ 左ナビテーブル ☆★☆ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<!-- ★☆★ メイン上部テーブル ☆★☆ -->
													<thead>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainHead' height="100" id="layout">
															<div tid="MainHead" class="drop_target" id="t2" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
													</thead>
													<!-- ★☆★ メイン上部テーブル ☆★☆ -->
													<!-- ★☆★ メイン ☆★☆ -->
													<tr class="fs12n">
														<td height=198 align="center" name='Main'>メイン</td>
													</tr>
													<!-- ★☆★ メイン ☆★☆ -->
													<!-- ★☆★ メイン下部テーブル ☆★☆ -->
													<tfoot>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainFoot' height="100" id="layout">
															<div tid="MainFoot" class="drop_target" id="t4" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
													</tfoot>
													<!-- ★☆★ メイン下部テーブル ☆★☆ -->
												</table>
											</td>
											<!-- ★☆★ 右ナビテーブル ☆★☆ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center">
															<div tid="RightNavi" class="drop_target" id="t3" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ★☆★ 右ナビテーブル ☆★☆ -->
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height=50><td bgcolor="#cccccc" align="center" colspan=3>フッター部</td></tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
									</table>
								</td>
								<!--▲レイアウト　ここまで-->
				
								<!--▼未使用ブロック　ここから-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="140" border="0" cellspacing="" cellpadding="" summary=" " bgcolor="#ffffff">
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" >
												<div tid="Unused" class="drop_target" id="t5" style="width: 160px; height: 500px; border: 1px solid #cccccc;"></div>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" height="30">
												<input type='button' value='新規ブロック作成' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','','');"  />
											</td>
										</tr>
									</table>
								</td>
								<!--▲未使用ブロック　ここまで-->
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<input type='button' value='保存' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','','');"  />
									<input type='button' value='プレビュー' name='preview' onclick="doPreview();" <!--{if $page_id == "0" or $exists_page == "0" }-->DISABLED<!--{/if}--> />
								</td>
							</tr>
						</table>
						<!--▲レイアウト編集　ここまで-->
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>

						<!--▼ページ一覧　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>編集可能ページ</strong></td>
							</tr>

							<!--{foreach key=key item=item from=$arrEditPage}-->
							<tr class="fs12n" height=20>
								<td align="center" width=600 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<a href="<!--{$smarty.server.PHP_SELF}-->?page_id=<!--{$item.page_id}-->" ><!--{$item.page_name}--></a>
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.tpl_dir != ""}-->
										<input type='button' value='ページ編集' name='page_edit' onclick="location.href='./main_edit.php?page_id=<!--{$item.page_id}-->'"  />
									<!--{else}-->
										ページ編集できません
									<!--{/if}-->
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.edit_flg == 1}-->
									<input type='button' value='削除' name='del' onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','','');"  />
									<!--{/if}-->
								</td>
							</tr>
							<!--{/foreach}-->

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3>
									<input type='button' value='新規ページ作成' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"  />
								</td>
							</tr>
						</table>
						<!--▲ページ一覧　ここまで-->

						</td>
						<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>

				</table>
				</td>
			</tr>
			<!--メインエリア-->
		</table>
		</td>
	</tr>

</table>
<!--★★メインコンテンツ★★-->		

<!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
<div align=center target_id="<!--{$item.target_id}-->" did="<!--{$smarty.foreach.bloc_loop.iteration}-->" class="dragged_elm" id="<!--{$item.target_id}-->"
	 style="left:350px; top:0px; filter: alpha(opacity=100); opacity: 1; z-index: 2; width: 120px;">
	 <!--{$item.name}-->
</div>
<input type="hidden" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->">
<input type="hidden" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->">
<input type="hidden" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->">
<input type="hidden" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->">
<!--{/foreach}-->
</form>
<script type="text/javascript">

// サイズ管理クラスの定義
function SC_Size() {
	this.id = '';				// ID
	this.left = 0;				// 配置するY軸座標
	this.top = 0;				// 配置するX軸座標
	this.width = 0;				// オブジェクトの幅
	this.height = 0;			// オブジェクトの高さ
	this.target_id = '';		// 配置場所（左ナビとか）
	this.margin = 10;			// 上のオブジェクトとの幅
	this.obj;
};

// 変数宣言
var defUnused = 500;	// 未使用領域のデフォルトの高さ
var defNavi   = 400;	// 左右ナビのデフォルトの高さ
var defMainNavi  = 100;	// メイン上下のデフォルトの高さ
var defMain   = 200;	// メインのデフォルトの高さ

var NowMaxHeight = 0;		// 現在の最大の高さ
var MainHeight = 200;

var marginUnused 	= 688;	// 未使用領域の左マージン
var marginLeftNavi  = 180;	// 左ナビの左マージン
var marginRightNavi = 512;	// 右ナビの左マージン
var marginMain		= 348;	// メイン上下の左マージン
var marginMainFootTop= 595;	// メイン下の上マージン

var gDragged = "";			// ドラッグ中オブジェクト
var gDropTarget = "";		// ドラッグ開始時のDropTarget

var arrObj = new Object();	// ブロックオブジェクト格納用

var mouseFlg = false;

var all_elms;				// divタグオブジェクト格納用

// ウィンドウサイズ
var scrX;
var scrY;

// イベントの関連付けを行う
function addEvent( elm, evtType, fn, useCapture) {

    if (elm.addEventListener) {
        elm.addEventListener(evtType, fn, useCapture);
        return true;

    }
    else if (elm.attachEvent) {

        var r = elm.attachEvent('on' + evtType, fn);
        return r;

    }
    else {
        elm['on'+evtType] = fn;

    }
    
}


// イベントの関連付けを解除
function removeEvent( elm, evtType, fn, useCapture) {

    if (elm.removeEventListener) {

        elm.removeEventListener(evtType, fn, useCapture);
        return true;

    }
    else if (elm.detachEvent) {

        var r = elm.detachEvent('on' + evtType, fn);
        return r;

    }
    else {

        elm['on'+evtType] = fn;

    }
   
}

// マウスカーソルを変更
function setCursor ( elm, curtype ) {
	elm.style.cursor = curtype;
}

// オブジェクトの透明度を変更   
function setOpacity(node,val) {

    if (node.filters) {
		node.filters["alpha"].opacity = val*100;
    } else if (node.style.opacity) {
        node.style.opacity = val;
    }
}

// Zindexを変更する（前面表示切替）
function setZindex(node, val) {
	node.style.zIndex = val;
//	alert(val);
}

// 値を取得
function getAttrValue ( elm, attrname ) {
	//alert(elm.nodeValue);
	
	//alert(typeof(elm.attributes[ attrname ]));
	
	//alert(elm.attributes[ attrname ].nodeValue);
	
	if (typeof(elm.attributes[ attrname ]) != 'undefined') {
	    return elm.attributes[ attrname ].nodeValue;
	}
	
}

// 値をセット
function setAttrValue ( elm, attrname, val ) {
    elm.attributes[ attrname ].nodeValue = val;
}

// オブジェクトのX座標を取得
function getX ( elm ) {
//   return parseInt(elm.style.left);
	return parseInt(elm.offsetLeft);
}

// オブジェクトのY座標を取得
function getY ( elm ) {
	return parseInt(elm.offsetTop);
//    return parseInt(elm.style.top);
}

// X座標を取得
function getEventX ( evt ) {
    return evt.clientX ? evt.clientX : evt.pageX;
}

// Y座標を取得
function getEventY ( evt ) {
    return evt.clientY ? evt.clientY : evt.pageY;
}

// オブジェクトの幅を取得
function getWidth ( elm ) {
    return parseInt( elm.style.width );
}

// オブジェクトの高さを取得
function getHeight ( elm ) {
//    return parseInt( elm.style.height );
    return parseInt( elm.offsetHeight );
}

// ページの可視領域のX座標を取得する
function getPageScrollX()
{
	var x = 0;

	if (document.body && document.body.scrollLeft != null) {
		x = document.body.scrollLeft;
	} else if (document.documentElement && document.documentElement.scrollLeft != null) {
		x = document.documentElement.scrollLeft;
	} else if (window.scrollX != null) {
		x = window.scrollX;
	} else if (window.pageXOffset != null) {
		x = window.pageXOffset;
	}
	
	return x;
}

// ページの可視領域のY座標を取得する
function getPageScrollY()
{
	var y = 0;
	
	if (document.body && document.body.scrollTop != null) {
		y = document.body.scrollTop;
	} else if (document.documentElement && document.documentElement.scrollTop != null) {
		y = document.documentElement.scrollTop;
	} else if (window.scrollY != null) {
		y = window.scrollY;
	} else if (window.pageYOffset != null) {
		y = window.pageYOffset;
	}
	
	return y;
}


// オブジェクトの座標をセット
function moveElm ( elm, x, y ) {
    elm.style.left = x + 'px';
    elm.style.top = y + 'px';
}

// マウスダウンイベント
function onMouseDown (evt) {

    var target = evt.target ? evt.target : evt.srcElement;
    var x = getEventX ( evt );
    var y = getEventY ( evt );

    //
    // Save Information to Globals
    //
  	if (mouseFlg == false) {
    
	    gDragged = target;
	
	    gDeltaX = x - getX(gDragged);
	    gDeltaY = y - getY(gDragged);
	
	    gDraggedId = getAttrValue ( gDragged, 'did' );
	    setCursor ( gDragged, 'move' );
	
	    gOrgX = getX ( gDragged );
	    gOrgY = getY ( gDragged );
	    gtarget_id = getAttrValue ( gDragged, 'target_id' );
	
	    //
	    // Set
	    //
	   
	    // ドラッグ中は半透明
	    setOpacity ( gDragged, 0.6 );
	
	    // ドラッグ中は最前面表示
	    setZindex ( gDragged , 100);
	    
	    addEvent ( document, 'mousemove', onMouseMove, false );
	    addEvent ( document, 'mouseup', onMouseUp, false );

	    // ドラッグを開始したときは高さを一度初期化する。
	    NowMaxHeight = defNavi;
	    	    
	    mouseFlg = true;
	}
}


// マウスムーブイベント
function onMouseMove(evt) {

	// 現在の座標を取得
	var x = getEventX ( evt ) + document.body.scrollLeft;					// マウス座標 X
	var y = getEventY ( evt ) + document.body.scrollTop;					// マウス座標 Y
    var nowleft = getEventX ( evt ) - gDeltaX;	// オブジェクト座標 LEFT
    var nowtop = getEventY ( evt ) - gDeltaY;	// オブジェクト座標 TOP

    // オブジェクトを移動
    moveElm ( gDragged, nowleft, nowtop );
	
    for ( var i = 0; i < all_elms.length; i++ ) {
    	// drop_target上にきた場合にのみ処理を行う
	    if ( isEventOnElm ( evt, all_elms[i].id ) ) {	    
            if ( all_elms[i].attributes['tid'] ) {
	            var tid = getAttrValue ( all_elms[i], 'tid' );
	            
	            // 背景色の変更 未使用領域は変更しない
	            all_elms[i].style.background="#ffffdd";
	            
				// target_id の書き換え
		        setAttrValue ( gDragged, 'target_id', tid );

				//objCheckLine.style.top = parseInt(nowtop) + parseInt(gDragged.style.height) / 2 + 'px';
				//objCheckLine.style.top = y;

				// 配列の再作成
				fnCreateArr(1, y, x);
				// 配列の並び替え
				fnChangeObj(tid);
		    }
		}else{
			if ( all_elms[i].attributes['tid'] && all_elms[i].style.background!="#ffffff") {
				// 背景色の変更
				all_elms[i].style.background="#ffffff";
			}
		}
    }
}

// マウスアップイベント       
function onMouseUp(evt) {
	// イベントの関連付け解除
	if (mouseFlg == true) {
	    removeEvent ( document, 'mousemove', onMouseMove, false );
	    removeEvent ( document, 'mouseup', onMouseUp, false );
	    mouseFlg = false;
	}

    if ( !isOnDropTarget (evt) ) {
		// 元の位置に戻す
        moveElm ( gDragged, gOrgX, gOrgY );
        setAttrValue ( gDragged, 'target_id', gtarget_id );

		// 配列の再作成
		fnCreateArr(1, gOrgY, gOrgX);
    }
    
    // hidden要素の書き換え
	var did = getAttrValue( gDragged, 'did' );
	var target_id = "target_id_"+did;
	document.form1[target_id].value = getAttrValue( gDragged, 'target_id' );
	
	// 半透明、マウスポインタ、最前面処理を戻す
    setOpacity( gDragged, 1);
    setCursor ( gDragged, 'hand' );
    setZindex ( gDragged , 1);
    
    // 並び替え
	fnSortObj();
	
	// 背景色を戻す
	for ( var i = 0; i < all_elms.length; i++ ) {
    	// drop_target上にきた場合にのみ処理を行う
	    if ( isEventOnElm ( evt, all_elms[i].id ) && all_elms[i].attributes['tid']) {
			// 背景色の変更
			all_elms[i].style.background="#ffffff";
		}
    }
}

// DropTarget上にオブジェクトが来たかを判断する
function isOnDropTarget ( evt ) {
   
    for ( var i=0; i<all_elms.length; i++ ) {
        if ( isEventOnElm ( evt, all_elms[i].id ) ) {
            if ( all_elms[i].attributes['tid'] ) {
                return true;
            }
        }
    }
    return false;
}
function isEventOnElm (evt, drop_target_id) {

	if (drop_target_id == '') {
		return '';
	}

    var evtX = getEventX(evt) + getPageScrollX();
    var evtY = getEventY(evt) + getPageScrollY();
    
    var drop_target = document.getElementById( drop_target_id );

	drp_left = getX( drop_target );
	drp_top = getY( drop_target );

    var x = drp_left;
    var y = drp_top;

	var width = getWidth ( drop_target );
	var height = getHeight ( drop_target );
    
//	alert(evtX +" / "+ x +" / "+ evtY +" / "+ y +" / "+ width +" / "+ height);

    return evtX > x && evtY > y && evtX < x + width && evtY < y + height;
}

// オブジェクトの並び替えを行う
function fnSortObj(){
	fnSetTargetHeight();
    for ( var cnt = 0; cnt < all_elms.length; cnt++ ) {

		// classが drop_target の場合のみ処理を行う
        if ( getAttrValue ( all_elms[cnt], 'class' ) == 'drop_target' ) {
        	var tid = getAttrValue ( all_elms[cnt], 'tid' );
			
			// 配列の並び替え
			fnChangeObj(tid);
			
			// 配置
			fnSetObj( tid, cnt );
        }
	}
}

// 配列の作成
function fnCreateArr( addEvt , top , left ){

	var arrObjtmp = new Object();
	arrObjtmp['LeftNavi'] = Array();
	arrObjtmp['RightNavi'] = Array();
	arrObjtmp['MainHead'] = Array();
	arrObjtmp['MainFoot'] = Array();
	arrObjtmp['Unused'] = Array();

	for ( var i = 0; i < all_elms.length; i++ ) {
 

		// classが dragged_elm の場合のみ処理を行う
		if ( getAttrValue ( all_elms[i], 'class' ) == 'dragged_elm' ) {
        
			// マウスダウンイベントと関連付けを行う
			if (addEvt == 0) {
	        	addEvent ( all_elms[i], 'mousedown', onMouseDown, false );
			}

			var target_id = getAttrValue ( all_elms[i], 'target_id' );	
			var len = arrObjtmp[target_id].length;
			var did = getAttrValue ( all_elms[i], 'did' );
			
			arrObjtmp[target_id][len] = new SC_Size();
			arrObjtmp[target_id][len].id = did;
			arrObjtmp[target_id][len].obj = all_elms[i];
			arrObjtmp[target_id][len].width = getWidth( all_elms[i] );
			arrObjtmp[target_id][len].height = getHeight( all_elms[i] );

			// ドラッグ中のオブジェクトが存在すれば、そのオブジェクトだけマウスポインタの座標を指定する。
			if (gDragged != "") {
				if (did != getAttrValue ( gDragged, 'did' )) {
					// top は常にオブジェクトの中心を取得するようにする
					alert(arrObjtmp[target_id][len].top);
					arrObjtmp[target_id][len].top = (parseInt(getX( all_elms[i] )) + arrObjtmp[target_id][len].height / 2 );
					arrObjtmp[target_id][len].left = getY( all_elms[i] );
				}else {
					arrObjtmp[target_id][len].top = top;
					arrObjtmp[target_id][len].left = left;
				}
			} else {
				// top は常にオブジェクトの中心を取得するようにする
				arrObjtmp[target_id][len].top = (parseInt(getX( all_elms[i] )) + arrObjtmp[target_id][len].height / 2 );
				arrObjtmp[target_id][len].left = getY( all_elms[i] );
			}
		}
    }
    arrObj = arrObjtmp;
}

// 配列の並び替え (バブルソートで並び替えを行う) 
function fnChangeObj( tid ){
	for ( var i = 0; i < arrObj[tid].length-1; i++ ) {
    	for ( var j = arrObj[tid].length-1; j > i; j-- ) {
			if ( arrObj[tid][j].top < arrObj[tid][i].top ) {
				var arrTemp = new Array();
				arrTemp = arrObj[tid][j];
				arrObj[tid][j] = arrObj[tid][i];
				arrObj[tid][i] = arrTemp;
			}
		}
	}
}

// 配置
function fnSetObj( tid, cnt ){
	var target_height = 0;
	
	drp_left = getX(all_elms[cnt]); //all_elms[cnt].offsetLeft;
	drp_top = getY(all_elms[cnt]); //all_elms[cnt].offsetTop;

	for ( var j = 0; j < arrObj[tid].length; j++ ) {
		// 配置する座標の取得
	    var left = parseInt(drp_left) + parseInt(all_elms[cnt].style.width) / 2 - parseInt(arrObj[tid][j].width) / 2;
	    if (j == 0){
	    	var top = drp_top + arrObj[tid][j].margin;
	    }else{
	    	var top = arrObj[tid][j-1].top + arrObj[tid][j].margin + arrObj[tid][j-1].height
	    }

		// 座標を保持
		arrObj[tid][j].top = top;
		arrObj[tid][j].left = left;

		// 配置を行う
		moveElm ( arrObj[tid][j].obj, left ,top);

		// 高さ計算
		target_height = target_height + arrObj[tid][j].margin + arrObj[tid][j].height;

		// hiddenの値を書き換え
		var top_id = "top_" + arrObj[tid][j].id;
		document.form1[top_id].value = top;

	}
}

// ドロップターゲットの高さ調整
function fnSetTargetHeight(){

	var NaviHeight = defNavi;
	var MainHeadHeight = defMainNavi;
	var MainFootHeight = defMainNavi;
	var UnusedHeight = defUnused;

	// 高さ計算
    for ( var cnt = 0; cnt < all_elms.length; cnt++ ) {
		var target_height = 0;
    
		// classが drop_target の場合のみ処理を行う
        if ( getAttrValue ( all_elms[cnt], 'class' ) == 'drop_target' ) {
        	var tid = getAttrValue ( all_elms[cnt], 'tid' );

			for ( var j = 0; j < arrObj[tid].length; j++ ) {
				target_height = target_height + arrObj[tid][j].margin + arrObj[tid][j].height;
			}

			// 下の幅
			target_height = target_height + 20;

			// 左右ナビ、未使用領域の高さを保持
			if (tid == 'LeftNavi' || tid == 'RightNavi' || tid == 'Unused') {
				if (NaviHeight < target_height) {
					NaviHeight = target_height;
				}
			}

			// メイン上部領域の高さを保持
			if (tid == 'MainHead') {
				if (target_height > defMainNavi) {
					MainHeadHeight = target_height;
				}
			}

			// メイン下部領域の高さを保持
			if (tid == 'MainFoot') {
				if (target_height > defMainNavi) {
					MainFootHeight = target_height;
				}
			}	
        }
	}

	// メイン領域の高さを保持
//	alert(NaviHeight+"/"+MainHeadHeight+"/"+MainFootHeight);
	MainHeight = NaviHeight - ( MainHeadHeight + MainFootHeight );
	if (MainHeight < defMain) {
		MainHeight = defMain;
	}

	// メイン部分のほうが大きい場合には左右ナビも大きくする
	if (NaviHeight < MainHeadHeight + MainFootHeight + MainHeight) {
		NaviHeight = MainHeadHeight + MainFootHeight + MainHeight;	
	}
	// 変更
    for ( var cnt = 0; cnt < all_elms.length; cnt++ ) {
    	var target_height = 0;

		// classが drop_target の場合のみ処理を行う
        if ( getAttrValue ( all_elms[cnt], 'class' ) == 'drop_target' ) {
        	var tid = getAttrValue ( all_elms[cnt], 'tid' );
        	
        	// tidによって処理を分ける
			if (tid == 'LeftNavi' || tid == 'RightNavi') {
				target_height = NaviHeight;
			}else if (tid == 'MainHead' ) {
				target_height = MainHeadHeight;
			}else if (tid == 'MainFoot') {
				target_height = MainFootHeight;
			}else if (tid == 'Unused'){
				target_height = NaviHeight+100;
			}

			all_elms[cnt].style.height = target_height;
		}
	}
	
	// メインテーブルの高さも変更
    for (var i = 0; i < all_td.length; i++) {
    	name = getAttrValue ( all_td[i], 'name' );
		if (name == 'Main') {
			all_td[i].height = MainHeight-2;
		}
    }
}

//ウインドウサイズ取得
function GetWindowSize(type){
    var ua = navigator.userAgent;       										// ユーザーエージェント
    var nWidth, nHeight;                  										// サイズ
    var nHit = ua.indexOf("MSIE");     											// 合致した部分の先頭文字の添え字
    var bIE = (nHit >=  0);                										// IE かどうか
    var bVer6 = (bIE && ua.substr(nHit+5, 1) == "6");  							// バージョンが 6 かどうか
    var bStd = (document.compatMode && document.compatMode=="CSS1Compat");		// 標準モードかどうか

	switch(type){
		case "width":
			if(bIE){
				if (bVer6 && bStd) {
					return document.documentElement.clientWidth;
				} else {
					return document.body.clientWidth;
				}
			}else if(document.layers){
				return(innerWidth);
			}else{
				return(-1);
			}
		break;
		case "height":
			if(bIE){
				if (bVer6 && bStd) {
					return document.documentElement.clientHeight;
				} else {
					return document.body.clientHeight;
				}
				return(document.body.clientHeight);
			}else if(document.layers){
				return(innerHeight);
			}else{
				return(-1);
			}
		break;
		default:
			return(-1);
		break;
	}
}

// ウィンドウサイズが変更になったときは全てのオブジェクトも移動する
function fnMoveObject() {

    // ウィンドウの幅変更比率を取得
	var moveX = GetWindowSize("width") - scrX;
	var BlankX = ( GetWindowSize("width") - 878 ) / 2
	
	for ( var i = 0; i < all_elms.length; i++) {
		if (all_elms[i].style.left != "" ) {

			var elm_class = getAttrValue ( all_elms[i], 'class' );

			if (elm_class == 'drop_target') {
				var tid = getAttrValue ( all_elms[i], 'tid' );
				
				if (tid == 'LeftNavi') {
					LeftMargin = marginLeftNavi;
				}else if (tid == 'RightNavi') {
					LeftMargin = marginRightNavi;
				}else if (tid == 'MainHead' || tid == 'MainFoot') {
					LeftMargin = marginMain;
				}else{
					LeftMargin = marginUnused;
				}

				if (BlankX > 0) {
					all_elms[i].style.left = BlankX + LeftMargin + 'px';
				}else{
					all_elms[i].style.left = LeftMargin + 'px';
				}
			}
		}
	}
	
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");
	
	fnSortObj();
}

// 初期処理
function init () {
    document.body.ondrag = function () { return false; };
    document.body.onselectstart = function () { return false; };
    
    // ウィンドウサイズを取得
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");    
    
	// ウィンドウサイズ変更イベントに関連付け
    window.onresize = fnMoveObject;

    // divタグを取得
    all_elms = document.getElementsByTagName ( 'div' );
    
	// tdタグを取得
	all_td = document.getElementsByTagName ( 'td' );

/****************************************************************************************************
	// ブロックの高さを取得する
	for ( var i = 0; i < all_elms.length; i++) {
		var elm_class = getAttrValue ( all_elms[i], 'class' );
		
		if (elm_class == 'dragged_elm') {
//			all_elms[i].style.height = all_elms[i].offsetHeight;
			//alert(all_elms[i].offsetHeight);
		}
	}
****************************************************************************************************/
	// 配列作成
	fnCreateArr(0);
	
    // 並び替え
	fnMoveObject();
	
	<!--{$complate_msg}-->
}

// 画面のロードイベントに関連付け
addEvent ( window, 'load', init, false );
</script>
