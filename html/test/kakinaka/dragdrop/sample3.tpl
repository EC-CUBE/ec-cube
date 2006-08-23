<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Drag and Drop Sample</title>
    <style type="text/css">    
    body {
        font-size:10px;
        font-family: Verdana, Helvetica;
    }

    div.dragged_elm {
        position:   absolute;
        z-index:    100;
        border:     1px solid black;
        background: rgb(195,217,255);
        text-align: center;
        color:      #333;
        cursor:		hand;
    }

    div.drop_target {
        position:    absolute;
        border:        1px solid black;
        background:    rgb(46, 180, 87);
        text-align:    center;
        color:        #333;
    }

	#check_line { 
		background-color: #ff3; 
		text-align: center; 
		position: absolute; 
		z-index: 1; 
		top: 407px;
		left: 108px; 
		width: 100px;
		height: 0.5px; 
		visibility: visible 
	}
    
    </style>
    
</head>
<body>

<div target_id="B2" did="A1" class="dragged_elm" id="d1" 
	 style="left:350px; top:0px; filter: alpha(opacity=100); opacity: 1; z-index: 2; width: 50px; height: 50px;">A1
</div>
<div target_id="B2" did="A2" class="dragged_elm" id="d2" 
	 style="left:350px; top:0px; filter: alpha(opacity=100); opacity: 1; z-index: 2; width: 50px; height: 50px;">A2
</div>
<div target_id="B3" name="test" did="A3" class="dragged_elm" id="d3" 
	 style="left:0px; top:130px; filter: alpha(opacity=100); opacity: 1; z-index: 2; width: 50px; height: 50px;">A3
</div>

<div tid="B1" class="drop_target" id="t1" style="left:100px; top:150px; width: 100px; height: 100px;">Drop Here.<br>B1</div>
<div tid="B2" class="drop_target" id="t2" style="left:500px; top:150px; width: 100px; height: 100px;">Drop Here!<br>B2</div>

<div tid="B3" class="drop_target" id="t3" style="left:300px; top:450px; width: 200px; height: 100px;">Drop Here!<br>B3</div>

<div class="check_line" id="check_line"></div>
<div id=checkline style="VISIBILITY: visible; WIDTH: 130px; POSITION: absolute; HEIGHT: 1px"><HR color=#ff5555></DIV>
<input type="text" name="text">

</body>
</html>

<script type="text/javascript">

// サイズ管理クラスの定義
function SC_Size() {
	this.id = '';				// ID
	this.left = 0;				// 配置するY軸座標
	this.top = 0;				// 配置するX軸座標
	this.width = blocWidth;		// オブジェクトの幅
	this.height = blocHeight;	// オブジェクトの高さ
	this.target_id = '';		// 配置場所（左ナビとか）
	this.margin = 10;			// 上のオブジェクトとの幅
	this.obj;
};

// 変数宣言
var blocHeight = 50;		// ブロックの高さ
var blocWidth = 50;			// ブロックの幅

var gDragged = "";

var arrObj = new Object();	// ブロックオブジェクト格納用
arrObj['B1'] = Array();
arrObj['B2'] = Array();
arrObj['B3'] = Array();

var objCheckLine = "";
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

        try {
           
            node.filters["alpha"].opacity = val*100;

        }
        catch (e) {
        }
       
    } else if (node.style.opacity) {

        node.style.opacity = val;

    }
   
}

// Zindexを変更する（前面表示切替）
function setZindex(node, val) {
	node.style.zIndex = val;
}

// 値を取得
function getAttrValue ( elm, attrname ) {
    return elm.attributes[ attrname ].nodeValue;
}

// 値をセット
function setAttrValue ( elm, attrname, val ) {
    elm.attributes[ attrname ].nodeValue = val;
}

// オブジェクトのX座標を取得
function getX ( elm ) {
   return parseInt(elm.style.left);
}

// オブジェクトのY座標を取得
function getY ( elm ) {
    return parseInt(elm.style.top);
}

// X座標を取得
function getEventX ( evt ) {
    return evt.pageX ? evt.pageX : evt.clientX;
}

// Y座標を取得
function getEventY ( evt ) {
    return evt.pageY ? evt.pageY : evt.clientY;
}

// オブジェクトの幅を取得
function getWidth ( elm ) {
    return parseInt( elm.style.width );
}

// オブジェクトの高さを取得
function getHeight ( elm ) {
    return parseInt( elm.style.height );
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
	    setZindex ( gDragged , 0);
    
	    addEvent ( document, 'mousemove', onMouseMove, false );
	    addEvent ( document, 'mouseup', onMouseUp, false );
	    mouseFlg = true;
	}
}


// マウスムーブイベント
function onMouseMove(evt) {

    // ドラッグ中はラインを表示
    objCheckLine.style.visibility = "visible";  
    
	// 現在の座標を取得
	var x = getEventX ( evt );					// マウス座標 X
	var y = getEventY ( evt );					// マウス座標 Y
    var nowleft = getEventX ( evt ) - gDeltaX;	// オブジェクト座標 LEFT
    var nowtop = getEventY ( evt ) - gDeltaY;	// オブジェクト座標 TOP

    // オブジェクトを移動
    moveElm ( gDragged, nowleft, nowtop );
    
    // ウィンドウサイズを取得
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");
    
    // マウスカーソルがウィンドウの外に出た場合には元に戻す
	if (x > scrX-5 || x < 5 || y > scrY-5 || y < 5) {
		// 元の位置に戻す
        moveElm ( gDragged, gOrgX, gOrgY );
        setAttrValue ( gDragged, 'target_id', gtarget_id );
	}
	
    for ( var i = 0; i < all_elms.length; i++ ) {
    	// drop_target上にきた場合にのみ処理を行う
	    if ( isEventOnElm ( evt, all_elms[i].id ) ) {
            if ( all_elms[i].attributes['tid'] ) {
	            var tid = getAttrValue ( all_elms[i], 'tid' );

				// target_id の書き換え
		        setAttrValue ( gDragged, 'target_id', tid );

/*
			    // ライン引き
				objCheckLine.style.width = all_elms[i].style.width;
				
				objCheckLine.style.left = all_elms[i].style.left;
				
				if (arrObj[tid].length == 0) {
					objCheckLine.style.top = all_elms[i].style.top;
				}else if ( y > parseInt(arrObj[tid][arrObj[tid].length-1].top) ) {
					objCheckLine.style.top = parseInt(all_elms[i].style.top) + parseInt(all_elms[i].style.height) - parseInt(arrObj[tid][arrObj[tid].length-1].margin) + 'px';
				}else{

				for ( var j=0; j < arrObj[tid].length; j++ ) {
				
					if (gDragged != arrObj[tid][j].obj) {
						if (y < parseInt(arrObj[tid][j].top)) {
							objCheckLine.style.top = parseInt(arrObj[tid][j].top) + parseInt(arrObj[tid][j].height) + parseInt(arrObj[tid][j].margin);
							break;
						}
					}
				}
*/
				

				//objCheckLine.style.top = parseInt(nowtop) + parseInt(gDragged.style.height) / 2 + 'px';
				//objCheckLine.style.top = y;

				// 配列の再作成
				fnCreateArr(1, y, x);
				// 配列の並び替え
				fnChangeObj(tid);				
		    }
		}
    }
}


// マウスアップイベント       
function onMouseUp(evt) {

	// 半透明、マウスポインタ、最前面処理を戻す
    setOpacity( gDragged, 1);
    setCursor ( gDragged, 'hand' );
    setZindex ( gDragged , 2);

	// イベントの関連付け解除
	if (mouseFlg == true) {
	    removeEvent ( document, 'mousemove', onMouseMove, false );
	    removeEvent ( document, 'mouseup', onMouseUp, false );
	    mouseFlg = false;
	}

    if ( isOnDropTarget (evt) ) {
    
	    // 並び替え
		fnSortObj();

    }
    else {
		// 元の位置に戻す
        moveElm ( gDragged, gOrgX, gOrgY );
        setAttrValue ( gDragged, 'target_id', gtarget_id );
    }
    
        
    // ドラッグ中はラインを表示
    objCheckLine.style.visibility = "hidden";    
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


///////////////////////////////////////////////////////////////////////////////

       
function isEventOnElm (evt, drop_target_id) {

    var evtX = getEventX(evt);
    var evtY = getEventY(evt);
   
    var drop_target = document.getElementById( drop_target_id );

    var x = getX ( drop_target );
    var y = getY ( drop_target );

    var width = getWidth ( drop_target );
    var height = getHeight ( drop_target );

    return evtX > x && evtY > y && evtX < x + width && evtY < y + height;
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
    
    //
    // Assign Event Handlers
    //
   
    // divタグを取得
    all_elms = document.getElementsByTagName ( 'div' );
    
	// 配列作成
	fnCreateArr(0);

    // 並び替え
	fnSortObj();
	
}


// オブジェクトの並び替えを行う
function fnSortObj(){
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
	arrObjtmp['B1'] = Array();
	arrObjtmp['B2'] = Array();
	arrObjtmp['B3'] = Array();

    for ( var i = 0; i < all_elms.length; i++ ) {
	    if ( objCheckLine == "" && getAttrValue ( all_elms[i], 'id' ) == 'checkline' ) {
	    	objCheckLine = all_elms[i];
	    }

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
			//arrObjtmp[target_id][len].width = getWidth ( all_elms[i] );
			//arrObjtmp[target_id][len].height = getHeight ( all_elms[i] );

			// ドラッグ中のオブジェクトが存在すれば、どのオブジェクトだけマウスポインタの座標を指定する。
			if (gDragged != "") {
				if (did != getAttrValue ( gDragged, 'did' )) {
					// top は常にオブジェクトの中心を取得するようにする
					arrObjtmp[target_id][len].top = (parseInt(all_elms[i].style.top) + arrObjtmp[target_id][len].height / 2 );
					arrObjtmp[target_id][len].left = all_elms[i].style.left;
				}else {
					arrObjtmp[target_id][len].top = top;
					arrObjtmp[target_id][len].left = left;
				}
			} else {
				// top は常にオブジェクトの中心を取得するようにする
				arrObjtmp[target_id][len].top = (parseInt(all_elms[i].style.top) + arrObjtmp[target_id][len].height / 2 );
				arrObjtmp[target_id][len].left = all_elms[i].style.left;
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
	
	for ( var j = 0; j < arrObj[tid].length; j++ ) {
		// 配置する座標の取得
	    var left = parseInt(all_elms[cnt].style.left) + parseInt(all_elms[cnt].style.width) / 2 - parseInt(arrObj[tid][j].width) / 2;
	    if (j == 0){
	    	var top = getY ( all_elms[cnt] ) + arrObj[tid][j].margin;
	    }else{
	    	var top = arrObj[tid][j-1].top + blocHeight + arrObj[tid][j].margin ;
	    }

		// 座標を保持
		arrObj[tid][j].top = top;
		arrObj[tid][j].left = left;
		
		// 配置を行う
		moveElm ( arrObj[tid][j].obj, left ,top );
		
		// 高さ計算
		target_height = target_height + arrObj[tid][j].margin + arrObj[tid][j].height;
	}
	
	// ドロップターゲットの幅調整
	if ( parseInt(all_elms[cnt].style.height) <= target_height || target_height > 100 ) {
		all_elms[cnt].style.height = target_height+20;
	} else {
		all_elms[cnt].style.height = 100;
	}
	
}

//ウインドウサイズ取得
function GetWindowSize(type){
    var ua = navigator.userAgent;       	// ユーザーエージェント
    var nWidth, nHeight;                   // サイズ
    var nHit = ua.indexOf("MSIE");     	// 合致した部分の先頭文字の添え字
    var bIE = (nHit >=  0);                // IE かどうか
    var bVer6 = (bIE && ua.substr(nHit+5, 1) == "6");  // バージョンが 6 かどうか
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
    // ウィンドウサイズを取得
	var moveX = GetWindowSize("width") - scrX;
	var moveY = GetWindowSize("height") - scrY;
	
	for ( var i = 0; i < all_elms.length; i++) {
		if (all_elms[i].style.left != "" ) {
			all_elms[i].style.left = parseInt(all_elms[i].style.left) + moveX / 2 + 'px';
		}
	}
	
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");
}

// 画面のロードイベントに関連付け
addEvent ( window, 'load', init, false );
</script>