/**
 * 
 * サイドバー編集 JavaScript
 *
 * Copyright (c) 2003-2004 DRECOM CO.,LTD. All rights reserved.
 * 
 * info@drecom.co.jp
 * http://www.drecom.co.jp/
 */


//---------------------------------------------------------------------------------
// 設定
//---------------------------------------------------------------------------------

// *** 初期設定変数 *** //

var gSidefuncOrderForm = null;
var SIDEFUNC_ORDER_FORM_NAME = "SidefuncOrderForm";


// 2004-04-13  Takanori Ishikawa  
// ------------------------------------------------------------------------
//
// Mac の IE5 は座標関連の機能（だけじゃないけど）の多くが狂っている
// ので適当に対応
//
// 参考：
// Mac の食えない野郎ども
// http://www.din.or.jp/~hagi3/JavaScript/JSTips/ProbMac5.htm
// Microsoft
// http://www.microsoft.com/
//
var MAC_IE5_PITCH_X;
var MAC_IE5_PITCH_Y;

MAC_IE5_PITCH_X = MAC_IE5_PITCH_Y = 0;	/* BITCH */
if (XBSUtil.macIE && XBSUtil.macIE.major >= 5) {
	MAC_IE5_PITCH_X = -10;
	MAC_IE5_PITCH_Y = 0;
}


// *** CheckLine *** //
var CHECKLINE_ID = 'checkline';


// --- Panel ---
// 識別子
var PANEL_NAME_DISABLE	= 'DISABLE';	// 外機能BOXのID
var PANEL_NAME_LEFT		= 'LEFT';		// 内・左機能BOXのID
var PANEL_NAME_RIGHT	= 'RIGHT';		// 内・右機能BOXのID

//すべての Panel をプロパティにもつオブジェクト
// key は name
var gAllPanels         = new Object();

// 左からの順番
var gPanelNames         = [PANEL_NAME_DISABLE, PANEL_NAME_LEFT, PANEL_NAME_RIGHT];


// --- Box ---
var BOX_ID_PREFIX 		= 'f';			// Box の ID プリフィックス
var BoxConfig = {
	width: 		120,	// Box 幅
	height: 	20,		// Box 高さ
	padding: 	10,		// Box 間のスペース
	marginTop: 	5,		// 一番上のBoxと親パネルとのスペース
	marginLeft: 5		// Box と親パネルの左側のスペース
};

// Box 配置領域の最小高さ
var MIN_BOXIES_HEIGHT	= 200;

// すべての Box をプロパティにもつオブジェクト
var gAllBoxes           = new Object();

/**
 * パネルに格納できる最大 Box 数を記録したオブジェクト
 * 
 * key: パネル名 value: パネルに格納できる最大 Box 数
 * 
 */

BoxConfig.maxNBoxes = new Object();
BoxConfig.maxNBoxes[PANEL_NAME_DISABLE]	= OEMBlogGlobal.PANEL_DISABLE_MAX_NBOXES;	// 「使用しない機能」
BoxConfig.maxNBoxes[PANEL_NAME_LEFT]	= OEMBlogGlobal.PANEL_LEFT_MAX_NBOXES;		// 「使用する機能」左
BoxConfig.maxNBoxes[PANEL_NAME_RIGHT]	= OEMBlogGlobal.PANEL_RIGHT_MAX_NBOXES;		// 「使用する機能」右

/**
 * ページ読み込み時の配置データ
 */
gInitialBoxPositionsData = null;

//---------------------------------------------------------
// 配置など
//---------------------------------------------------------
/**
 * HTML の hidden input 要素から項目の配置などの情報を読み取るオブジェクト
 * 
 * @author Takanori Ishikawa
 * @version 2004/04/23
 */
SFSettings = new Object();

/**
 * HTML の hidden input 要素から設定値を読み取る
 */
SFSettings.loadBoxPositions = function()
{
	var positions = new Object();
	var orders       = new Array();
	var panelNames   = new Array();	
	
	// hiddenからbox_positionを取得
	positions[PANEL_NAME_DISABLE]	= new Array();
	positions[PANEL_NAME_LEFT]		= new Array();
	positions[PANEL_NAME_RIGHT]		= new Array();
	for (var i = 0; i < gSidefuncOrderForm.elements.length; ++i) {
		var elem = gSidefuncOrderForm.elements[i];
		
		if(Box.ORDER_PROPERTY_NAME == elem.name) {
			orders[orders.length] = elem.value;
		} else if(Box.POSITION_PROPERTY_NAME == elem.name) {
			var idx = XBSUtil.parseIntNoError(elem.value);
			panelNames[panelNames.length] = gPanelNames[idx];
		} else if (Box.ID_PROPERTY_NAME == elem.name) {
			;
		}
	}
	for (var i = 0; i < orders.length; i++) {
		var pnm = panelNames[i];
		var bid = i;
		var ord = orders[i];
		
		positions[pnm][bid] = ord;
	}
	
	orders     = null;
	panelNames = null;
	return positions;
}

/**
 * 現在の状態を単一のオブジェクトにエンコードして返す。
 */
SFSettings.getObjectForPersistent = function()
{
	var orders = new Array();
	var pidxes = new Array();
	var result = '';
	
	if (gSidefuncOrderForm == null) {
		return result;
	}
	for (var i = 0; i < gSidefuncOrderForm.elements.length; ++i) {
		var elem = gSidefuncOrderForm.elements[i];
		
		if(Box.ORDER_PROPERTY_NAME == elem.name) {
			orders[orders.length] = elem.value;
		} else if(Box.POSITION_PROPERTY_NAME == elem.name) {
			pidxes[pidxes.length] = XBSUtil.parseIntNoError(elem.value);
		}
	}
	result += pidxes.join('');
	result += orders.join('');
	pidxes = null;
	orders = null;
	
	return result;
}

//---------------------------------------------------------
// ContentsChangedListener
//---------------------------------------------------------
/**
 * サイドバーの変更を監視するオブジェクト
 * ContentsChangedListener のサブクラス
 * 
 * @author Takanori Ishikawa
 * @version 2004/04/23
 */
function SFChangedListener(/* Optional */ aName) /* extends ContentsChangedListener */
{
}
SFChangedListener.prototype = new ContentsChangedListener();
SFChangedListener.prototype.setChanged = function(b) 
{
	var persistent = SFSettings.getObjectForPersistent();
	
	// 2004-04-23  Takanori Ishikawa 
	// ------------------------------------------------------------------------
	// ここで prototype のメソッドを呼び出してもうまくいかない。

	this.changed = (gInitialBoxPositionsData != persistent);
}

//---------------------------------------------------------
// Drag & Drop: SFDragManager (Sidebar Function DragManager)
//---------------------------------------------------------
/**
 * ドラッグ処理の有効化、途中の状態を保持するためのオブジェクト
 * ドラッグ処理は一度にひとつしか起こらないので、いまのところ
 * Singleton として実装
 * 
 * For example:
 * <pre>
 * // prepare for dragging, enable event handler.
 * SFDragManager.enableEventHandler();
 * </pre>
 *
 * @author  Takanori Ishikawa
 * @version 1.0
 */
SFDragManager = new Object();

// *** Event Handler *** //
SFDragManager.ondrop = null;  // 配置が変更された


// ドラッグ処理に利用する document のイベントハンドラを設定
SFDragManager.enableEventHandler = function ()
{
	document.onmousemove = function(e) {
		if (SFDragManager.box != null)
			SFDragManager.moveBox(e);
	};
	
	document.onmouseup = function(e) {
		if (SFDragManager.box != null)
			SFDragManager.downBox(e);
	};
	
	for (var key in gAllBoxes) {
		var box = gAllBoxes[key];
		var src = box ? box.getHTMLElement() : null;
		
		if (src != null) {
			src.onmousedown = SFDragManager.pickUpBox;
		}
	}
}

// ドラッグ処理に利用する document のイベントハンドラを解除
SFDragManager.disbleEventHandler= function()
{
	document.onmousemove	= '';
	document.onmouseup		= '';
}


//ドラッグ中のオブジェクト

SFDragManager.destination = null;	/* 移動先 Panel */
SFDragManager.source      = null;	/* 移動元 Panel */
SFDragManager.box         = null;	/* ドラッグ中の Panel */

/**
 * サイドバー項目の変更を監視するオブジェクトを返す。
 */
SFDragManager.getChangedListener = function()
{
	if (SFDragManager.getChangedListener.$instance == null) {
		
		SFDragManager.getChangedListener.$instance = 
			new SFChangedListener("[Sidebar Function Order]");
	}
	return SFDragManager.getChangedListener.$instance;
}
SFDragManager.setUpContentsChangedListener = function()
{
	// 2004-04-23  Takanori Ishikawa  
	// ------------------------------------------------------------------------
	// サイドバーの配置が変更されたかどうかを記録
	
	var listener = SFDragManager.getChangedListener();
	
	listener.listenEvent(SFDragManager, 'ondrop');
	OEMBlogGlobal.watchOtherLinks(document, listener);
}

/**
  * setOnLoad()
  *
  * ページ読み込み時の初期処理
  */
SFDragManager.loaded = false;
SFDragManager.onload = function() 
{
	// フォーム初期化
	gSidefuncOrderForm = UtilKit.getPropertyNotNull(document, SIDEFUNC_ORDER_FORM_NAME);
	
	// 各 Panel, Box オブジェクト生成
	var boxPositions = SFSettings.loadBoxPositions();
	gInitialBoxPositionsData = SFSettings.getObjectForPersistent();
	for (var i = 0; i < gPanelNames.length; i++) {
		var nm = gPanelNames[i];
		var panel;
		
		panel = new Panel(nm, boxPositions[nm]);
		
		for(var key in panel.box_position) {
			gAllBoxes[key] = new Box(key);
		}
		
		// Box の初期配置
		panel.setBoxPosition();
		Panel[nm] = panel;
		
		gAllPanels[nm] = panel;
	}
	
	
	// ドラッグ処理の準備
	SFDragManager.setUpContentsChangedListener();
	SFDragManager.enableEventHandler();
	
	// onload もイベント
	resetPositionsInEventHandler();
	window.onresize = resetPositionsInEventHandler;
	
	SFDragManager.loaded = true;
	// 2004-04-20  Takanori Ishikawa  
	// ------------------------------------------------------------------------
	// Opera では onload 時点でレイヤーの再描画が間に合わない。
	// この関数を使って、無理やり再描画する。
	if (window.opera) {
		SFDragManager.loaded = false;
		setTimeout("refreshDisplay()", 0.8*1000);
	}
	
}


//---------------------------------------------------------
// イベント処理
//---------------------------------------------------------
// CheckLine
/**
 *  ドラッグ中の Box が挿入される箇所を指し示すためのライン 
 *
 * @return XBSLayer
 * @see xbs.js 
 */
function getCheckLineLayer()
{
	if (null == getCheckLineLayer.layer) {
		getCheckLineLayer.layer = XBSLayer.makeLayer(CHECKLINE_ID);
	}
	return getCheckLineLayer.layer;
}
function getPanelAtPosition(x, y)
{
	var panel = null;
	
	for (var key in gAllPanels) {
		panel = gAllPanels[key];
			
		if (x > panel.panel_section['left']  &&
			x < panel.panel_section['right'] &&
			y > panel.panel_section['top']   &&
			y < panel.panel_section['under']) 
		{ break; }
	}
	return panel;
}

// 2004-04-13  Takanori Ishikawa  
// ------------------------------------------------------------------------
// Mac IE ではイベントハンドラの中で座標関係の操作を行うとうまくいかないので
// すこし遅らせて実行
function resetPositionsInEventHandler()
{
	if (XBSUtil.macIE && XBSUtil.macIE.major >= 5) {
		setTimeout("resetPositions()", 1*1000);	
	} else {
		resetPositions();
	}
}

// *** Reset *** //
/**
  * すべての Box を再配置
  */
function resetPositions() 
{
	var panel;
	
	for (var key in gAllPanels) {
		panel = gAllPanels[key];
		if (panel != null) {
			panel.setSections(); 
			panel.setBoxPosition(); 
		}
	}
}

/**
 * すべての Box を一度消し、すぐにすべて表示
 */
function refreshDisplay() 
{
	var panel;
	
	for (var key in gAllPanels) {
		panel = gAllPanels[key];
		if (panel != null) {
			panel.setBoxVisible(false);
			panel.setBoxVisible(true);		
		}
	}
	SFDragManager.loaded = true;
}

// -----------------------------------------------------------------
// ボックスのドラッグ＆ドロップ
// -----------------------------------------------------------------

/**
  * pickUpBox()
  * theEvent: イベント
  *
  * Boxを持ち上げた(クリックされた)時の処理をする関数
  */
SFDragManager.pickUpBox = function(theEvent) {
	if (null == theEvent) {
		theEvent = window.event;
	}
	if (SFDragManager.box != null || SFDragManager.loaded == false)
		return;
	
	// マウス位置取得
	var mouse_x	    = XBSEvent.getMouseX(theEvent);
	var mouse_y	    = XBSEvent.getMouseY(theEvent);
	var pickedPanel = getPanelAtPosition(mouse_x, mouse_y);

	if (null == pickedPanel) 
		return;

	// 選択されたBoxの並び順番号と id を取得
	var beforeId = pickedPanel.orderIndexOfBoxAt(mouse_x, mouse_y);
	var funcId	= UtilKit.getKeyForValue(pickedPanel.box_position, beforeId);
	
	if (null == funcId)
		return;
	
	//Boxエレメント取得・処理
	var selected = new XBSLayer(BOX_ID_PREFIX + funcId);
	if (null == selected) 
		return;

	selected.setZIndex(selected.getZIndex() +1);

	//選択されたBoxのX,Y座標を保存
	before_box_x	= selected.getX() + MAC_IE5_PITCH_X;
	before_box_y	= selected.getY() + MAC_IE5_PITCH_Y;

	//マウスとBox原点の差
	box_to_mouse_x	= mouse_x - before_box_x;
	box_to_mouse_y	= mouse_y - before_box_y;
	
	// ドラッグ開始
	SFDragManager.box = selected;
	SFDragManager.source = pickedPanel;
	before_order_id = beforeId;
	function_id = funcId;
}

/**
 * 
 * Boxを移動する関数
 * 
 * @param theEvent mousemove イベント
 */
SFDragManager.moveBox = function(theEvent) {
	var selected = SFDragManager.box;
	
	if (null == selected || SFDragManager.loaded == false)
		return;
	if (null == theEvent) {
		theEvent = window.event;
	}
	//
	// ドラッグ中、ウィンドウから出る時リセットして終わる
	//
	if (false == Box.canMoveWithEvent(theEvent)) {
		gAllBoxes[function_id].setPosition(before_box_x, before_box_y);
		clearMove();
		
		return;
	}

	var mouse_x  = XBSEvent.getMouseX(theEvent);
	var mouse_y  = XBSEvent.getMouseY(theEvent);
	var curPanel = getPanelAtPosition(mouse_x, mouse_y);
	if (null == curPanel) 
		return;
	
	if (curPanel.canDropAtPosition(SFDragManager.source, selected, mouse_x, mouse_y)) {
		// チェックライン表示
		curPanel.showCheckLineAtPosition(mouse_x, mouse_y);
	}

	//Box移動
	move_to_x = mouse_x - box_to_mouse_x;
	move_to_y = mouse_y - box_to_mouse_y;
	
	selected.setLeftTopPosition(move_to_x, move_to_y);
}

/**
 * Box を下ろした時の処理
 * 
 * @param theEvent イベント
 */
SFDragManager.downBox = function(theEvent)
{
	if (SFDragManager.loaded == false) {
		return;
	}
	if (null == theEvent) {
		theEvent = window.event;
	}
	// チェックラインを消す
	getCheckLineLayer().setVisible(false);

	// マウス位置取得
	var mouse_x = XBSEvent.getMouseX(theEvent);
	var mouse_y = XBSEvent.getMouseY(theEvent);
	
	// -------------------------------------
	// 移動領域内か外かチェック
	// -------------------------------------
	// 移動先のパネルを取得し、ドラッグ情報を更新
	// ドラッグ前後のパネル
	var beforePanel = SFDragManager.source;
	var afterPanel  = getPanelAtPosition(mouse_x, mouse_y);
	
	SFDragManager.destination = afterPanel;
	if (null == beforePanel) {
		throw ASSERT_EXCEPTION + 'SFDragManager.source must be not null';
	}
	
	var droppedBox = gAllBoxes[function_id];
	var after_order_id = afterPanel.orderIndexOfMovingAt(mouse_x, mouse_y);
	
	// ドロップできない場合、Box を戻して、ドラッグ処理を終了する
	if (false == afterPanel.canDropAtPosition(beforePanel, droppedBox, mouse_x, mouse_y)) {
		droppedBox.setPosition(before_box_x, before_box_y);
		clearMove();
		return;
	}

	// 移動するBoxをパネルから削除し、順番を整理する
	delete beforePanel.box_position[function_id];	
	for (key in beforePanel.box_position) {
		if (before_order_id < beforePanel.box_position[key]) {
			--beforePanel.box_position[key];
		}
	}

	//after_order_idの処理
	if (!(beforePanel.equals(afterPanel) && before_order_id <= after_order_id)) {
		++after_order_id;
	}


	// 最大のインデックスを更新
	if (after_order_id > afterPanel.getMaxOrderIndex() + 1) {
		after_order_id = afterPanel.getMaxOrderIndex() + 1;
	}

	//挿入する間をつくり、挿入する
	for (key in afterPanel.box_position) {
		if (after_order_id <= afterPanel.box_position[key]) {
			++afterPanel.box_position[key];
		}
	}
	afterPanel.box_position[function_id] = after_order_id;

	//Box再配置
	if (false == beforePanel.equals(afterPanel)) {
		beforePanel.setBoxPosition();
	}
	afterPanel.setBoxPosition();

	// HTMLのhidden値変更
	for (var nm in gAllPanels) {
		var p = gAllPanels[nm];

		for (key in p.box_position) {
			gSidefuncOrderForm.elements['order'][key].value = p.box_position[key];
			gSidefuncOrderForm.elements['position'][key].value = p.getPosition();
		}
	}

	// 移動の解除
	clearMove();
	if (SFDragManager.ondrop != null && typeof SFDragManager.ondrop == 'function') {
		SFDragManager.ondrop();
	}	
}


/**
  * clearMove()
  *
  * Box移動を終了する関数
  */
function clearMove() {
	var selected = SFDragManager.box;
	
	selected.setZIndex(selected.getZIndex() -1);
	SFDragManager.box = null;
}

//---------------------------------------------------------------------------------
// Panel
//---------------------------------------------------------------------------------
/**
  * 個々のドラッグ可能な項目を保持する領域
  * 
  * いまのところ、左から「使用しない機能」「使用する機能・左」「使用する機能・右」
  *
  * @param aPanelName    パネルの名前
  * @param aPositionArray Boxの配置情報配列
  *
  * @author Yusuke Saito
  * @version 2003/10/10
  * 
  * @author Takanori Ishikawa
  * @version 2004/04/20
  */
function Panel(aPanelName, aPositionArray)
{
	this.name			   = aPanelName;						// パネルの名前(divのid)
	this.box_position	   = aPositionArray;					// Boxの配置情報配列
	this.box_limit_number  = BoxConfig.maxNBoxes[aPanelName];	// パネル内の最大Box数
	
	this.position = -1;
	for (var i = 0; i < gPanelNames.length; i++) {
		if (this.name == gPanelNames[i]) {
			this.position = i;
			break;
		}
	}
	
	// パネル・ボックス・移動の各領域を設定
	this.setSections();
}

/**
 * Box の Y offset
 * 
 * @param anIndex order index
 */
Panel.getBoxSectionOffsetY = function(anIndex)
{
	if (Panel.box_section_y == null) {  // Box 位置設定
		var nboxs = BoxConfig.maxNBoxes[PANEL_NAME_DISABLE];
		Panel.box_section_y = new Array(nboxs);
		
		for (var i = 0; i < nboxs; ++i) {
			Panel.box_section_y[i] = (BoxConfig.height + BoxConfig.padding) * i + BoxConfig.marginTop;
		}
	}
	return Panel.box_section_y[anIndex];
}

Panel.prototype.getPosition = function() {
	return this.position;
}

/**
 * description
 */
Panel.prototype.toString = function()
{
	return '[Panel] ' + this.name;
}

/**
 * 比較
 * 
 * @param aPanel パネル
 */
Panel.prototype.equals = function(aPanel)
{
	// 2004-04-23  Takanori Ishikawa 
	// ------------------------------------------------------------------------
	// instanceof は IE 6 でも未サポート	
	if (aPanel == null || /*false == (aPanel instanceof Panel)*/ typeof aPanel.name == 'undefined') {
		return false;
	}	
	return this.name == aPanel.name;
}


/**
  * setSections()
  * 
  * ウィンドウを原点としたパネルの４辺のX,Y座標を設定する関数
  * left:左辺X right:右辺X top:上辺Y under:下辺Y
  */
Panel.prototype.setSections = function() {

	// 親レイヤーの位置取得
	this.setUpPanelSections();

	//Box領域の設定
	this.setBoxSection();

	//Box移動先領域の設定
	this.setMoveSection();
}


/**
  * setUpPanelSections()
  * 
  * ウィンドウを原点としたパネルの４辺のX,Y座標を設定する関数
  * left:左辺X right:右辺X top:上辺Y under:下辺Y
  */
Panel.prototype.setUpPanelSections = function() 
{
	var psec    = new Object;
	var lyer = XBSLayer.makeLayer(this.name);
	
	psec['left'] = MAC_IE5_PITCH_X; 
	psec['top']  = MAC_IE5_PITCH_Y;
	
	psec['right'] = lyer.getWidth();
	psec['under'] = lyer.getHeight();
	while (lyer != null && lyer.isValid() && lyer.hasID()) {
		
		psec['left'] += lyer.getX();	
		psec['top']  += lyer.getY();
		lyer = lyer.getParent();
	}
	psec['right'] += psec['left'];
	psec['under'] += psec['top'];

	this.panel_section = psec;
}

/**
  * setBoxSection()
  * 
  * Boxの収まる領域を設定する関数
  * box_section[並び順番号] => (left => 左辺X, right => 右辺X, top => 上辺Y, under => 下辺Y)
  */
Panel.prototype.setBoxSection = function() {

	//Box領域の設定
	this.box_section_left_x		= this.panel_section['left'] + BoxConfig.marginLeft;
	this.box_section_right_x	= this.box_section_left_x + BoxConfig.width

	this.box_section			= new Array(this.box_limit_number);

	for(i = 0; i < this.box_limit_number; ++i) {
		this.box_section[i]				= new Array(4);
		this.box_section[i]['left']		= this.box_section_left_x;
		this.box_section[i]['right']	= this.box_section_right_x;
		this.box_section[i]['top']		= Panel.getBoxSectionOffsetY(i) + this.panel_section['top'];
		this.box_section[i]['under']	= this.box_section[i]['top'] + BoxConfig.height;
	}
}

/**
 * パネルの中で一番下の Box のインデックスを返す
 * 
 * @return 一番下の Box のインデックス。
 */
Panel.prototype.getMaxOrderIndex = function()
{
	var max = -1;
	for(key in this.box_position) {
		max = (max < this.box_position[key]) ? this.box_position[key] : max;
	}
	return Number(max);
}

/**
 * 指定された位置に最も近い、Box を挿入可能な箇所にチェックラインを表示する。
 * 
 * @param x x 座標
 * @param y y 座標
 */
Panel.prototype.showCheckLineAtPosition = function(x, y)
{
	if (typeof x != typeof 0 || typeof y != typeof 0) {
		throw ASSERT_EXCEPTION + 'x or y arg';
	}
	
	var moveOrderId   = this.orderIndexOfMovingAt(x, y);
	var maxOrderIndex = this.getMaxOrderIndex();
	
	if (null == moveOrderId)
		return;
	
	if (maxOrderIndex < moveOrderId) {
		moveOrderId = maxOrderIndex;
	}
	if (moveOrderId < this.box_limit_number) {
		// チェックライン移動
		var check_line_left = this.panel_section['left'];
		var check_line_top = this.move_section[moveOrderId]['top'];

		// 一旦チェックラインを消す
		getCheckLineLayer().setVisible(false);
		getCheckLineLayer().setLeftTopPosition(check_line_left, check_line_top);
		getCheckLineLayer().setVisible(true);
	}
}


/**
  * setMoveSection()
  * 
  * Boxを移動できる領域を設定する関数
  * move_section[並び順番号] => (left => 左辺X, right => 右辺X, top => 上辺Y, under => 下辺Y)
  */
Panel.prototype.setMoveSection = function() {

	//移動領域の設定
	this.move_section_left_x	= this.panel_section['left'];
	this.move_section_right_x	= this.move_section_left_x + BoxConfig.width + (BoxConfig.marginLeft * 2);
	this.move_section			= new Array(this.box_limit_number + 1);

	this.move_section[-1]			= new Array(4);
	this.move_section[-1]['left']	= this.move_section_left_x;
	this.move_section[-1]['right']	= this.move_section_right_x;
	this.move_section[-1]['top']	= this.panel_section['top'] - 7;
	this.move_section[-1]['under']	= this.panel_section['top'] + (BoxConfig.marginTop / 2) + BoxConfig.height;

	for(i = 0; i < this.box_limit_number; ++i) {
		this.move_section[i]			= new Array(4);
		this.move_section[i]['left']	= this.move_section_left_x;
		this.move_section[i]['right']	= this.move_section_right_x;
		this.move_section[i]['top']		= this.move_section[i - 1]['under'];
		this.move_section[i]['under']	= this.move_section[i]['top'] + BoxConfig.height + BoxConfig.padding;
	}
}



Panel.prototype.canDropAtPosition = function(aSourcePanel, aBox, x, y)
{
	var orderIndex = this.orderIndexOfMovingAt(x, y);
	var maxIndex = this.getMaxOrderIndex() + 1;

	// パネルから外れた、
	// パネルの最大 Box 数を超える = false
	return (orderIndex != null && (this.equals(aSourcePanel) || this.box_limit_number > maxIndex));
}

/**
 * 移動中、挿入される箇所のインデックスを返す。
 * 
 * @param x x 座標
 * @param y y 座標
 * @return 挿入されるインデックス、なければ null
 */
Panel.prototype.orderIndexOfMovingAt = function(x, y)
{
	return this.findOrderIndexOf_(this.move_section, x, y);
}
Panel.prototype.orderIndexOfBoxAt = function(x, y)
{
	return this.findOrderIndexOf_(this.box_section, x, y);
}
// private
Panel.prototype.findOrderIndexOf_ = function(sectionDataArray, x, y)
{
	for (var i = -1; i < sectionDataArray.length; i++) {
		var sec = sectionDataArray[i];
		if (sec == null)
			continue;
		
		if (x >= sec['left'] && x <= sec['right'] &&
			y >= sec['top']  && y <= sec['under']) {
	
			return i
		}
	}
	return null;
}

/**
  * setBoxPosition()
  *
  * Boxを再配置する関数
  */
Panel.prototype.setBoxPosition = function() {
	for (key in this.box_position) {
		gAllBoxes[key].setPosition(
			this.panel_section['left'] + BoxConfig.marginLeft,
			this.panel_section['top']  + Panel.getBoxSectionOffsetY(this.box_position[key]) );
	}
}

Panel.prototype.setBoxVisible = function(flag)
{
	for (key in this.box_position) {
		gAllBoxes[key].setVisible(flag);
	}
}



//---------------------------------------------------------------------------------
// Box
//---------------------------------------------------------------------------------
/**
 * 個々のドラッグで移動可能な項目
 * 
 * @param anId HTML 要素の id 属性
 * 
 * @author Yusuke Saito
 * @version 2003/10/10
 * 
 * @author Takanori Ishikawa
 * @version 2004/04/21
 */
function Box(anId)
{	
	this.uniqID     = anId;
	this.srcElement = XBSLayer.getLayerImpById(BOX_ID_PREFIX + anId);

	this.setUpSize();
	this.x = this.y = 0;	
}

Box.prototype.getFormID = function() { return this.uniqID; }
Box.prototype.getHTMLElement = function() { return this.srcElement; }

/**
 * HTML の hidden input 要素で渡されるプロパティの名前
 */
Box.POSITION_PROPERTY_NAME = "position";
Box.ORDER_PROPERTY_NAME    = "order";
Box.NAME_PROPERTY_NAME     = "sidefuncName";
Box.AUTHOR_PROPERTY_NAME   = "authorCd";
Box.ID_PROPERTY_NAME       = "sidefuncId";


/**
 * ドラッグ処理の最中に呼ばれる。
 * 
 * @param theEvent mousemove
 * @return ドラッグ処理をキャンセルする場合は、false を返す。
 */
Box.MOVABLE_INSET = 5;	/* ドキュメントの端 - Box.MOVABLE_INSET = ドラッグ可能な領域 */
Box.canMoveWithEvent = function(theEvent) 
{
	// 相対的な位置がほしいので、XBSDocument.getPageOffsetX/Y() を引く
	var mouse_x = XBSEvent.getMouseX(theEvent) - XBSDocument.getPageOffsetX();
	var mouse_y = XBSEvent.getMouseY(theEvent) - XBSDocument.getPageOffsetY();
	
	if (mouse_x < Box.MOVABLE_INSET || mouse_y < Box.MOVABLE_INSET) {
		return false;
	}
	
	var maxX = XBSDocument.getWidth() - Box.MOVABLE_INSET;
	var maxY = XBSDocument.getHeight() - Box.MOVABLE_INSET;
	
	if (mouse_x > maxX || mouse_y > maxY) {
		return false;
	}
	
	return true;
}

/**
  * setUpSize()
  * 
  * Boxの大きさを設定する関数
  */
Box.prototype.setUpSize = function() 
{
	var imp = this.getHTMLElement();
	
	// style.height でないと opera でうまくいかない
	if (XBSUtil.DOM == NN6 || XBSUtil.DOM == IE5) {
		imp.style.height = BoxConfig.height;	
		imp.style.width = BoxConfig.width;	
	} else {
		XBSLayer.setHeightWithLayerImp(imp, BoxConfig.height);
		XBSLayer.setWidthWithLayerImp(imp, BoxConfig.width);
	}
	XBSLayer.setCursorWithLayerImp(imp, 'move');
}

/**
  * Boxの位置を設定
  * 
  * @param left x
  * @param top  y
  */
Box.prototype.setPosition = function(left, top) 
{
	var imp = this.getHTMLElement();
	XBSLayer.initPositionStyle(imp);
	XBSLayer.setLeftTopPositionWithLayerImp(imp, left, top);
	this.x = left; this.y = top;
}
/**
 * Box の可視属性を設定
 */
Box.prototype.setVisible = function(flag)
{
	var imp = this.getHTMLElement();
	XBSLayer.setVisibilityWithLayerImp(imp, flag ? XBSLayer.VISIBLE : XBSLayer.HIDDEN);
}
