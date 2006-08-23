/**
 * XBS (Cross Browser Scripting) Utilities
 *
 * Copyright (c) 2004 DRECOM CO.,LTD. All rights reserved.
 * 
 * info@drecom.co.jp
 * http://www.drecom.co.jp/
 */

//---------------------------------------------------------------------------------
// Debugging 
//---------------------------------------------------------------------------------
/**
 * Assertion に失敗したときにこれを throw
 */
ASSERT_EXCEPTION = '*** Assertion Failur *** ';

/**
 * dprint, dinspect の出力をストップするときは true に設定
 */
BLOCK_DEBUG_PRINT  = true;


/**
 * v が undefined でなければ true
 */
function defined(v) { return (typeof v != 'undefined'); }

/**
 * オブジェクトのすべてのプロパティを dprint 関数で表示
 * 
 * @param オブジェクト
 */
function dinspect(anObj)
{
	if (null == anObj) return;
	for (key in anObj) {
		dprint(key + ": " + anObj[key]);
	}

}

function dprint(obj)
{
	if (BLOCK_DEBUG_PRINT) {
		return;
	}
	if (typeof dprint.window == "undefined") {
		dprint.window = window.open("/drecomcms/js/Console.html", 
			"Console",
			"width=300, height=600, scrollbars, resizable, menubar");
	}
	
	var t = dprint.window.document.getElementById('console');
	
	if (t != null) {
		t.value = t.value + "\n> " + obj.toString();
	}
// Takanori Ishiakwa 04/04/06
// -----------------------------------------
// Safari や Opera でうまく動作しないため
// TextArea バージョンに切り替えた
/*
	if (typeof dprint.window == "undefined") {
		dprint.window = window.open("", 
			"Console",
			"width=300, height=600, scrollbars, resizable, menubar");
		dprint.window.document.open("text/plain");	
	}
	var d = dprint.window.document;
	
	if (d == null) return;

	d.writeln("> " + obj.toString());
	
	// Opera: mime text/plain を指定しても HTML として解釈
	// されているようなので、改行させるためにタグを出力
	if (window.opera) d.writeln("<br>");
*/
}

// ---------------------------------------------------------
// XBSUtil
// ---------------------------------------------------------
// for the purpose of namespace
/**
 * ブラウザ互換を目指して、ユーティリティ群をまとめたオブジェクト
 * 
 * @author  Takanori Ishikawa
 * @version 1.0
 */
XBSUtil = new Object();

/**
 * 要素を含む document オブジェクトを返す。
 * 
 * @param document
 */
XBSUtil.getOwnerDocument = function(anElement) 
{
	if (anElement.document) {
		return anElement.document;
	} else if (anElement.ownerDocument) {
		return anElement.ownerDocument;
	}
	return null;
}


/**
 * Unicode code points collection
 * 
 * プログラム中で使う文字コードを集めたもの
 * 
 * @author Takanori Ishikawa
 * @version 1.0
 */
XBSCType = new Object();

// *** XBSCType 初期化 *** //
// 初期化テーブル
// key: property name
// value: string (length must be 1)
XBSCType.template_ = {
	CR:    '\r',
	LF:    '\n',
	TAB:   '\t',
	SPACE: ' ',
	LT:    '<',
	GT:    '>',
	SLASH: '/',
	COLON: ':',
	AMP:   '&',
	LBRACE:'{',
	RBRACE:'}'
};

for (var key in XBSCType.template_) {
	var c = XBSCType.template_[key];
	
	if (c.length != 1) {
		throw ASSERT_EXCEPTION + 'XBSCType.template_[' + key + '] length must be 1.';
	}
	c = c.charCodeAt(0);
	XBSCType[key] = c;
}
// 削除
delete XBSCType.template_;

/**
 * isspace
 */
XBSCType.isspace = function(c) 
{
	return (c == XBSCType.SPACE || c == XBSCType.TAB || c == XBSCType.CR || c == XBSCType.LF);
}


/**
 * DOM サポート具合によるブラウザ判別
 * 
 * <ul>
 * <li>IE4:   IE4 相当</li>
 * <li>IE5:   IE5 相当</li>
 * <li>NN4:   NN4 相当</li>
 * <li>NN6:   NN6 相当</li>
 * <li>OTHER: 上記以外</li>
 * </ul>
 */
OTHER = 0;
IE4   = 1;
IE5   = 2;
NN4   = 3;
NN6   = 4;
XBSUtil.DOM = document.all 
				? (document.getElementById ? IE5 : IE4)
				: (document.getElementById 
					? NN6
					: (document.layers ? NN4 : OTHER));

/**
 * テキスト操作 API のサポート具合によるブラウザ判別
 */
XBSUtil.Range = null;
if (document.selection != null && defined(document.selection.createRange)) {
	XBSUtil.Range = IE5;
}


// エラー時には 0 を返す parseInt(), 10 進数
XBSUtil.parseIntNoError = function(anObj) {
	var v = 0;	

	try {
		v = parseInt(anObj, 10);
	} catch (e) {

	}
	if ((typeof v != typeof 0) || isNaN(v)) {
		v = 0;
	}

	return v;
}

/**
 * DOM:
 * DocumentImplementation - feature list
 */
if (null == XBSUtil.DOM) {
	throw ASSERT_EXCEPTION + 'XBSUtil.DOM must be not null.';
}
XBSUtil.DOM.HTMLEvent = false;

if (document.implementation) {
	XBSUtil.DOM.HTMLEvent = document.implementation.hasFeature('HTMLEvents', '2.0');
}


/**
 * Mac: AppleWebKit
 * 
 * Apple の WebKit framework の上に構築されたブラウザなら
 * ここで XBSUtil.appleWebKit オブジェクトを生成。
 * 
 * @field major WebKit メジャーバージョン番号 (Number)
 * @field minor WebKit マイナーバージョン番号 (Number)
 */
XBSUtil.appleWebKit = null;
var _webKitMatch = navigator.userAgent.match(/AppleWebKit\/(\d+)\.?(\d+)?/i);
if (_webKitMatch != null) {
	var o = new Object();
	
	o.major = XBSUtil.parseIntNoError(_webKitMatch[1]);
	o.minor = XBSUtil.parseIntNoError(_webKitMatch[2]);	
	XBSUtil.appleWebKit = o;
}

/**
 * Mac IE
 * 
 * Mozilla/4.0 (compatible; MSIE 5.0; Mac_PowerPC)
 */
/**
 * Mac: IE
 * 
 * IE の Mac 版なら
 * ここで XBSUtil.macIE オブジェクトを生成。
 * 
 * @field major IE メジャーバージョン番号 (Number)
 * @field minor IE マイナーバージョン番号 (Number)
 */
//
// 参考： 
// Mac の食えない野郎ども
// http://www.din.or.jp/~hagi3/JavaScript/JSTips/ProbMac5.htm
// Microsoft
// http://www.microsoft.com/
//
XBSUtil.macIE = null;
if (navigator.userAgent.match(/Mac/i)) {
	var _macIEMatch;
	
	_macIEMatch = navigator.userAgent.match(/MSIE\s+(\d+)\.?(\d+)?/i);
	if (_macIEMatch) {
		var o = new Object();

		o.major = XBSUtil.parseIntNoError(_macIEMatch[1]);
		o.minor = XBSUtil.parseIntNoError(_macIEMatch[2]);	
		XBSUtil.macIE = o;
	}
}
/**
 * Opera
 *
 * Opera ならここで XBSUtil.opera オブジェクトを生成。
 * 
 * @field major opera メジャーバージョン番号 (Number)
 * @field minor opera マイナーバージョン番号 (Number)
 */
XBSUtil.opera = null;
if (window.opera) {
	var o = new Object();
	
	o.major = 6;
	if (document.documentElement)
		o.major = 7;
	
	o.minor = 0;
	XBSUtil.opera = o;
}


// ---------------------------------------------------------
// XBSLayer
// ---------------------------------------------------------
/**
 * div 要素などをレイヤーとして扱うためのオブジェクト
 * 
 * For example:
 * <pre>
 *    var layer = new XBSLayer(anIdOfDivElement);
 *    // some operation...
 * </pre>
 *
 * @author  Takanori Ishikawa
 * @version 1.0
 * @see     document.layer
 * @see     style
 * 
 * @param     uniqId  div 要素の id 属性
 * @param     doc     document オブジェクト（オプション：デフォルトはwindow.document）
 */
function XBSLayer(uniqId, doc)
{
	if (uniqId) {
		this.setLayerImp(XBSLayer.getLayerImpById(uniqId, doc));
	}
}
/**
 * 指定された div 要素がなければ例外を投げる
 * 
 * どうせ、たいした数のレイヤーはないのでキャッシュ
 */
XBSLayer.makeLayer = function(uniqId, doc)
{
	var lyer = null;
	
	if (XBSLayer.makeLayer.$cache == null) {
		XBSLayer.makeLayer.$cache = new Object();
	}
	
	lyer = XBSLayer.makeLayer.$cache[uniqId];
	if (null == lyer || (doc && doc != XBSUtil.getOwnerDocument(lyer.getLayerImp()))) {
		lyer = new XBSLayer(uniqId, doc);
		
		if (null == lyer || null == lyer.getLayerImp()) {
			//b throw ASSERT_EXCEPTION + 'div element (id:' + uniqId + ') is required.';
		}
		XBSLayer.makeLayer.$cache[uniqId] = lyer;
	}
	
	return lyer;
}

/* ---- class properties ---- */

// Visibility constants
/**
 * レイヤーの可視属性を表す定数
 */
XBSLayer.VISIBLE   = 1;
XBSLayer.HIDDEN    = 2;
XBSLayer.INHERIT   = 3;
XBSLayer.UNDEFINED = 4;

/**
 * レイヤーの操作に使う実装オブジェクトを得る
 *
 * @param     anId  div 要素の id 属性
 * @param     doc   document オブジェクト（オプション：デフォルトはwindow.document）
 * @return    DOM: element.style, NN: layer
 */
XBSLayer.getLayerImpById = function(anId, doc) 
{
	var imp = null;
	
	if (null == doc) {
		doc = document;
	}
	if (doc.getElementById || doc.all) {
		imp = doc.getElementById 
				? doc.getElementById(anId)
				: doc.all(anId);
	} else if (doc.layers) {
		imp = doc.layers[anId];
	}
	return imp;
}
XBSLayer.getStyleObjectWithLayerImp = function(imp)
{
	if (document.getElementById || document.all) {
		return imp.style;
	} else {
		return imp;
	}
}

/**
 * 親要素を得る
 *
 * @param     imp  実装オブジェクト
 * @return    親要素
 */
XBSLayer.getParentOfLayerImp = function(imp)
{
	var parent = null;
	
	if (null == imp) {
		;
	} else if (defined(imp.parentElement)) {
		parent = imp.parentElement;
	} else if (defined(imp.offsetParent)) {
		parent = imp.offsetParent;
	}

	return parent;
}


/**
 * レイヤーの操作に使う実装オブジェクトの可視属性を返す。
 *
 * @param     imp  実装オブジェクト
 * @return    可視属性を表す定数
 * <ul>
 * <li>XBSLayer.VISIBLE</li>
 * <li>XBSLayer.HIDDEN</li>
 * <li>XBSLayer.INHERIT</li>
 * <li>XBSLayer.UNDEFINED</li>
 * </ul>
 */
XBSLayer.getVisibilityWithLayerImp = function(imp)
{
	var v;
	
	if (XBSUtil.DOM != NN4) {
		imp = imp.style;
	}
	v = imp.visibility;
	if (document.all) {
		if (v == '') return XBSLayer.INHERIT;
	} else if (!document.getElementById && document.layers) {
		if (v == 'show') return XBSLayer.VISIBLE;
		if (v == 'hide') return XBSLayer.HIDDEN;
	}
	
	if (v == 'visible') return XBSLayer.VISIBLE;
	if (v == 'hidden') return XBSLayer.HIDDEN;
	if (v == 'inherit') return XBSLayer.INHERIT;
		
	return XBSLayer.UNDEFINED;
}
XBSLayer.setVisibilityWithLayerImp = function(imp, anEnum)
{
	var v = '';
	
	if (!document.getElementById && document.layers) {
		if (anEnum == XBSLayer.VISIBLE) v = 'show';
		if (anEnum == XBSLayer.HIDDEN) v = 'hide';
	} else {
		if (anEnum == XBSLayer.VISIBLE) v = 'visible';
		if (anEnum == XBSLayer.HIDDEN) v = 'hidden';
		if (anEnum == XBSLayer.INHERIT) v = 'inherit';
	}
	
	if (XBSUtil.DOM != NN4) {
		imp = imp.style;
	}
	return imp.visibility = v;
}
/**
 * レイヤーの display 属性
 * 
 * @return レイヤーが領域を持つ場合は true
 */
XBSLayer.isBlockWithLayerImp = function(imp)
{
	var b = true;
	
	if (imp.style.display != null) {
		b = (imp.style.display != 'none');
	}
	return b;
}
XBSLayer.setBlockWithLayerImp = function(imp, b)
{
	if (imp.style != null) {
		imp.style.display = b ? 'block' : 'none';
	}
}

// Position & Size


// zIndex
XBSLayer.getZIndexWithLayerImp = function(imp)
{
	if (XBSUtil.DOM != NN4)
		imp = imp.style;
	
	return imp.zIndex;
}
XBSLayer.setZIndexWithLayerImp = function(imp, z)
{
	if (XBSUtil.DOM != NN4)
		imp = imp.style;
	
	imp.zIndex = z;
}


// Position
/**
 * 位置指定に使う定数
 */
XBSLayer.LEFT_TOP     = 1;
XBSLayer.LEFT_BOTTOM  = 2;
XBSLayer.RIGHT_TOP    = 3;
XBSLayer.RIGHT_BOTTOM = 4;

/**
 * ユーティリティ： 指定されたイベントの位置にレイヤーを移動、表示
 * 
 * @param imp div 要素
 * @param theEvent イベントオブジェクト
 * @param axisType 座標を指定する位置
 * <ul>
 * <li>XBSLayer.LEFT_TOP</li>
 * <li>XBSLayer.LEFT_BOTTOM</li>
 * <li>XBSLayer.RIGHT_TOP</li>
 * <li>XBSLayer.RIGHT_BOTTOM</li>
 * </ul>
 *
 * @param offsetX この距離だけイベントの位置から離す (x 座標)
 * @param offsetY この距離だけイベントの位置から離す (y 座標)
 * 
 */
XBSLayer.popUpWithLayerImpAtEventLocation = function(imp, theEvent, axisType, offsetX, offsetY)
{
	var left   = XBSEvent.getMouseX(theEvent) + offsetX;
	var top    = XBSEvent.getMouseY(theEvent) + offsetY;
		
	XBSLayer.initPositionStyle(imp);
	XBSLayer.setPositionWIthLayerImp(imp, left, top, axisType);
	XBSLayer.setVisibilityWithLayerImp(imp, XBSLayer.VISIBLE);
}

/**
 * レイヤーの位置指定
 * 
 * @param imp div 要素
 * @param x x 座標
 * @param y y 座標
 * @param axisType 座標を指定する位置 (default: LEFT_TOP)
 * <ul>
 * <li>XBSLayer.LEFT_TOP</li>
 * <li>XBSLayer.LEFT_BOTTOM</li>
 * <li>XBSLayer.RIGHT_TOP</li>
 * <li>XBSLayer.RIGHT_BOTTOM</li>
 * </ul>
 */
XBSLayer.setPositionWIthLayerImp = function(imp, x, y, axisType)
{
	var left = x;
	var top  = y;
	var w = XBSLayer.getWidthWithLayerImp(imp);
	var h = XBSLayer.getHeightWithLayerImp(imp);
	
	if (XBSLayer.LEFT_BOTTOM == axisType) {
		top -= h;
	} else if (XBSLayer.RIGHT_TOP == axisType) {
		left -= w;
	} else if (XBSLayer.RIGHT_BOTTOM == axisType) {
		top -= h;
		left -= w;
	}
	if (left < 0) left = 0;
	if (top < 0) top = 0;
	
	XBSLayer.setLeftTopPositionWithLayerImp(imp, left, top)
}
/**
 * レイヤーの位置（左上）を指定。
 * 
 * @param imp div 要素
 * @param top 左上 x 座標
 * @param left 左上 y 座標
 */
XBSLayer.setLeftTopPositionWithLayerImp = function(imp, left, top)
{	
	if (XBSUtil.DOM == NN4) {
		imp.moveTo(left, top);
	}else if (imp.style != null) {	
		imp.style.left = left; imp.style.top = top;
	}
}
/**
 * レイヤーの位置情報を初期化。ブラウザによってはこの関数を
 * 他の位置関係関数よりも前に呼ばないと結果が不定になる。
 * 
 * @param imp div
 */
XBSLayer.initPositionStyle = function(imp)
{
	if (document.layers) {
		return;
	} else if (typeof imp.style.left == "string") {
		imp.style.left = imp.offsetLeft + 'px';
		imp.style.top  = imp.offsetTop  + 'px';
	} else if (typeof imp.style.pixelLeft != "undefined") {
		imp.style.pixelLeft = imp.offsetLeft;
		imp.style.pixelTop  = imp.offsetTop;
	}
}

XBSLayer.getLeftWithLayerImp = function(imp)
{
	var x = 0;

	if (XBSUtil.DOM == NN6 || XBSUtil.DOM == IE5) {
		x = imp.offsetLeft;
	} else if (XBSUtil.DOM == IE4) {
		x = imp.style.pixelLeft;
	} else if (XBSUtil.DOM == NN4) {
		x = imp.clip.left;
	}
	return x;
}

XBSLayer.getTopWithLayerImp = function(imp)
{
	var y = 0;
	if (XBSUtil.DOM == NN6 || XBSUtil.DOM == IE5) {
		y = imp.offsetTop;
	} else if (XBSUtil.DOM == IE4) {
		y = imp.style.pixelTop;
	} else if (XBSUtil.DOM == NN4) {
		y = imp.clip.top;
	}
	return y;
}

// Size
XBSLayer.getWidthWithLayerImp = function(imp) 
{
	var w = 0;
	if (XBSUtil.DOM == NN6 || XBSUtil.DOM == IE5) {
		w = imp.offsetWidth;
	} else if (XBSUtil.DOM == IE4) {
		w = imp.style.pixelWidth;
	} else if (XBSUtil.DOM == NN4) {
		w = imp.clip.width;
	}
	return w;
}
XBSLayer.getHeightWithLayerImp = function(imp)
{
	var h = 0;
	if (XBSUtil.DOM == NN6 || XBSUtil.DOM == IE5) {
		h = imp.offsetHeight;
	} else if (XBSUtil.DOM == IE4) {
		h = imp.style.pixelHeight;
	} else if (XBSUtil.DOM == NN4) {
		h = imp.clip.height;
	}
	return h;
}
XBSLayer.setWidthWithLayerImp = function(imp, w) 
{
	if (XBSUtil.DOM == NN6 || XBSUtil.DOM == IE5) {
		imp.style.width = w;	
	} else if (XBSUtil.DOM == IE4) {
		imp.style.pixelWidth = w;
	} else if (XBSUtil.DOM == NN4) {
		imp.resizeTo(w, imp.clip.height);

	}
}
XBSLayer.setHeightWithLayerImp = function(imp, h)
{
	if (XBSUtil.DOM == NN6 || XBSUtil.DOM == IE5) {
		imp.style.height = h;	
	} else if (XBSUtil.DOM == IE4) {
		imp.style.pixelHeight = h;
	} else if (XBSUtil.DOM == NN4) {
		imp.resizeTo(imp.clip.width, h);
	}
}

// HTML
XBSLayer.getInnerHTMLWithLayerImp = function(imp)
{
	if (defined(imp.innerHTML)) {
		return imp.innerHTML;
	}
	return '';
}
XBSLayer.setInnerHTMLWithLayerImp = function(imp, htmlText)
{
	if (defined(imp.innerHTML)) {
		imp.innerHTML = htmlText;
	} else if (document.layers != null) {
		imp.document.open();
		imp.document.write(htmlText);
		imp.document.close();
	}
}

// Cursor
XBSLayer.setCursorWithLayerImp = function(imp, aName) 
{
	if (imp.style != null && defined(imp.style.cursor)) {
		imp.style.cursor = aName;
	}
}



/* ---- Instance properties ---- */
/**
 * 親要素の XBSLayer を返す
 */
XBSLayer.prototype.getParent = function()
{
	var imp = XBSLayer.getParentOfLayerImp(this.getLayerImp());
	if (null == imp) {
		return null;
	}
	var lyer = new XBSLayer();
	
	lyer.setLayerImp(imp);
	return lyer;
}
XBSLayer.prototype.getID = function() { 
	return this.getLayerImp().id;
}
XBSLayer.prototype.hasID = function() {
	var id = this.getID();
	
	return id != null && id != '';
}
XBSLayer.prototype.isValid = function() {
	return (this.getLayerImp() != null);
}
// Visibility
XBSLayer.prototype.getVisibility = function()
{
	return XBSLayer.getVisibilityWithLayerImp(this.getLayerImp());
}
XBSLayer.prototype.setVisibility = function(aEnum)
{
	XBSLayer.setVisibilityWithLayerImp(this.getLayerImp(), aEnum);
}

/**
 * レイヤーが可視かどうかを返す。
 *
 * @return    boolean
 */
XBSLayer.prototype.isVisible  = function()
{
	return this.getVisibility() == XBSLayer.HIDDEN ? false : true;
}
XBSLayer.prototype.setVisible = function(/* boolean */ flag)
{
	this.setVisibility(flag ? XBSLayer.VISIBLE : XBSLayer.HIDDEN);
}
XBSLayer.prototype.toggleVisibility = function()
{
	this.setVisible(false == this.isVisible())
}
/**
 * レイヤーの display 属性
 * 
 * @return レイヤーが領域を持つ場合は true
 */
XBSLayer.prototype.isBlock  = function()  
{ return XBSLayer.isBlockWithLayerImp(this.getLayerImp()); }
XBSLayer.prototype.setBlock = function(b) 
{ XBSLayer.setBlockWithLayerImp(this.getLayerImp(), b); }


/**
 * レイヤーの z 座標
 */
XBSLayer.prototype.getZIndex = function()
{
	return XBSLayer.getZIndexWithLayerImp(this.getLayerImp());
}
XBSLayer.prototype.setZIndex = function(z)
{
	XBSLayer.setZIndexWithLayerImp(this.getLayerImp(), z);
}


XBSLayer.prototype.getStyleObject = function() 
{
	return XBSLayer.getStyleObjectWithLayerImp(this.imp);
}
XBSLayer.prototype.getLayerImp = function() { return this.imp; }
XBSLayer.prototype.setLayerImp = function(imp) { this.imp = imp; }

// Position & Size
XBSLayer.prototype.popUpAtEventLocation = function(theEvent, axisType, offsetX, offsetY)
{ XBSLayer.popUpWithLayerImpAtEventLocation(this.getLayerImp(), theEvent, axisType, offsetX, offsetY); }

/**
 * レイヤーの位置指定
 * 
 * @param x x 座標
 * @param y y 座標
 * @param axisType 座標を指定する位置
 * <ul>
 * <li>XBSLayer.LEFT_TOP</li>
 * <li>XBSLayer.LEFT_BOTTOM</li>
 * <li>XBSLayer.RIGHT_TOP</li>
 * <li>XBSLayer.RIGHT_BOTTOM</li>
 * </ul>
 */
XBSLayer.prototype.setPosition = function(x, y, axisType)
{ XBSLayer.setPositionWIthLayerImp(this.getLayerImp(), x, y, axisType); }
XBSLayer.prototype.setLeftTopPosition = function(left, top) 
{ 
	XBSLayer.setLeftTopPositionWithLayerImp(this.getLayerImp(), left, top); 
}

XBSLayer.prototype.getX = function() { return XBSLayer.getLeftWithLayerImp(this.getLayerImp()); }
XBSLayer.prototype.getY = function() { return XBSLayer.getTopWithLayerImp(this.getLayerImp()); }
/**
 * 座標を絶対値で取得
 */
XBSLayer.prototype.getAbsolute_ = function(fnName)
{
	var ret = 0;
	var lyer = this;
	
	while (lyer != null && lyer.isValid()) {
		ret += lyer[fnName]();	
		lyer = lyer.getParent();
	}
	return ret;
	
}
XBSLayer.prototype.getAbsoluteX = function()
{
	return this.getAbsolute_("getX");
}
XBSLayer.prototype.getAbsoluteY = function()
{
	return this.getAbsolute_("getY");
}

XBSLayer.prototype.getWidth  = function() { return XBSLayer.getWidthWithLayerImp(this.getLayerImp()); }
XBSLayer.prototype.getHeight = function() { return XBSLayer.getHeightWithLayerImp(this.getLayerImp()); }
XBSLayer.prototype.setWidth  = function(w) { return XBSLayer.setWidthWithLayerImp(this.getLayerImp(), w); }
XBSLayer.prototype.setHeight = function(h) { return XBSLayer.setHeightWithLayerImp(this.getLayerImp(), h); }

// HTML
XBSLayer.prototype.getInnerHTML = function()
{
	return XBSLayer.getInnerHTMLWithLayerImp(this.getLayerImp());
}
XBSLayer.prototype.setInnerHTML= function(htmlText)
{
	XBSLayer.setInnerHTMLWithLayerImp(this.getLayerImp(), htmlText);
}

// Cursor
XBSLayer.prototype.setCursor = function(aName) { XBSLayer.setCursorWithLayerImp(this.getLayerImp(), aName); }

//---------------------------------------------------------
// XBSDocument
//---------------------------------------------------------
// object for namespace purpose
/**
 * ドキュメント情報の取得
 * 
 * @author  Takanori Ishikawa
 * @version 1.0
 */
XBSDocument = new Object();


/**
 * ドキュメント全体の大きさ
 * 
 * @return 幅、高さ
 */
XBSDocument.getWidth = function()
{
	var w = 0;
	
	if (window.innerWidth != null) {
		w = window.innerWidth;
	
	// 2004-04-13  Takanori Ishikawa  
	// ------------------------------------------------------------------------
	// IE: 0 が返ってくる
	/*
	} else if (document.documentElement && document.documentElement.clientWidth != null) {
		w = document.documentElement.clientWidth;
	*/
	} else if (document.body && document.body.clientWidth != null) {
		w = document.body.clientWidth;
	} 
	return w;
}

XBSDocument.getHeight = function()
{
	var h = 0;
	
	if (window.innerHeight != null) {
		h = window.innerHeight;
	// 2004-04-13  Takanori Ishikawa  
	// ------------------------------------------------------------------------
	// IE: 0 が返ってくる
	/*
	} else if (document.documentElement && document.documentElement.clientHeight != null) {
		h = document.documentElement.clientHeight;
	*/
	} else if (document.body && document.body.clientHeight != null) {
		h = document.body.clientHeight;
	} 
	return h;
}

/**
 * ドキュメントの可視領域の左上座標
 * 
 * @return x, y
 */
XBSDocument.getPageOffsetX = function()
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
XBSDocument.getPageOffsetY = function()
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

//---------------------------------------------------------
//XBSEvent
//---------------------------------------------------------
// object for namespace purpose
/**
 * イベントオブジェクトの操作
 * 
 * @author  Takanori Ishikawa
 * @version 1.0
 */
XBSEvent = new Object();

// *** Key code *** //
/**
 * Key code (unicode)
 * 
 * @see http://www.unicode.org
 */
// 0008;<control>;Cc;0;BN;;;;;N;BACKSPACE;;;;
XBSEvent.BACKSPACE_KEY = 0x08;
// 0009;<control>;Cc;0;S;;;;;N;CHARACTER TABULATION;;;;
XBSEvent.TAB_KEY = 0x09;
// 000A;<control>;Cc;0;B;;;;;N;LINE FEED (LF);;;;
XBSEvent.LINE_FEED_KEY = 0x0A;
// 000D;<control>;Cc;0;B;;;;;N;CARRIAGE RETURN (CR);;;;
XBSEvent.NEW_LINE_KEY = 0x0D;


// Modifier keys
XBSEvent.ALT_KEY_MASK     = 1;
XBSEvent.SHIFT_KEY_MASK   = 1 << 1;
XBSEvent.CONTROL_KEY_MASK = 1 << 2;
XBSEvent.META_KEY_MASK    = 1 << 3;

/**
  * getMouseX(e), getMouseY(e)
  *
  * マウスがイベントを起こした時に
  * X座標、Y座標を返す関数
  * 
  * @param theEvent マウスイベント
  * @return 座標
  */
XBSEvent.getMouseX = function(theEvent)
{
	var x = 0;
	
	if (null == theEvent) {
		theEvent = window.event;
	}
	// Safari は clientX も絶対座標で返してくる
	if (XBSUtil.appleWebKit != null) {
		return theEvent.pageX
	}
	
	if (theEvent.clientX != null) {
		x = XBSDocument.getPageOffsetX() + theEvent.clientX;
	} else if (theEvent.pageX != null) {
		x = theEvent.pageX;
	} else if (window.opera != null) {  // 念のため。動作確認したバージョンではこない
		x = (document.documentElement ? window.pageXOffset : 0) + theEvent.clientX;
	}
	
	return x;
}
XBSEvent.getMouseY = function(theEvent) {
	var y = 0;
	
	if (null == theEvent) {
		theEvent = window.event;
	}
	
	// Safari は clientY も絶対座標で返してくる
	if (XBSUtil.appleWebKit != null) {
		return theEvent.pageY;
	}
	
	if (theEvent.clientY != null) {
		y = XBSDocument.getPageOffsetY() + theEvent.clientY;
	} else if (theEvent.pageY != null) {
		y = theEvent.pageY;
	} else if (window.opera != null) {  // 念のため。動作確認したバージョンではこない
		y = (document.documentElement ? window.pageYOffset : 0) + theEvent.clientY;
	}
	
	return y;
}


/**
 * Key Event の Unicode コードポイントを得る
 * 
 * @return code, なければ null
 */
XBSEvent.getKeyCode = function(theEvent) 
{
	if (theEvent.which != null) {
		return theEvent.which;
	} else if (theEvent.keyCode != null) {
		return theEvent.keyCode;
	}
	return null;
}
XBSEvent.getTarget = function(theEvent) 
{
	if (theEvent.target != null) {
		return theEvent.target;
	} else if (theEvent.srcElement != null) {
		return theEvent.srcElement;
	}
	return null;
}


XBSEvent.getModifierFlags = function(theEvent)
{
	var flags = 0;
	

	if (defined(theEvent.ctrlKey)) {
		if (theEvent.altKey) flags |= XBSEvent.ALT_KEY_MASK;
		if (theEvent.shiftKey) flags |= XBSEvent.SHIFT_KEY_MASK;
		if (theEvent.ctrlKey) flags |= XBSEvent.CONTROL_KEY_MASK;
		if (theEvent.metaKey) flags |= XBSEvent.META_KEY_MASK;
	} else if (defined(theEvent.modifiers)) {
		var m = theEvent.modifiers;
		
		if (Event.ALT_MASK & m) flags |= XBSEvent.ALT_KEY_MASK;
		if (Event.SHIFT_KEY_MASK & m) flags |= XBSEvent.SHIFT_KEY_MASK;
		if (Event.CONTROL_MASK & m) flags |= XBSEvent.CONTROL_KEY_MASK;
		if (Event.META_MASK & m) flags |= XBSEvent.META_KEY_MASK;
	}
	return flags;
}


//---------------------------------------------------------------------------------
// Utilities
//---------------------------------------------------------------------------------
UtilKit = new Object();

/**
 * 指定されたオブジェクトのプロパティに関数を *** 追加 *** する。
 * 以前に別の関数が登録されていればそれを呼び出してから、登録された関数を呼び出す。
 * 
 * NOTE: 渡せる引数はとりあえず 5 つまでとした。これだけあれば十分なので。
 * 
 * @param anObject オブジェクト
 * @param funcName プロパティ名
 * @param func     関数本体
 */
UtilKit.addhook = function(anObject, funcName, func)
{
	var prev = anObject[funcName];
	var fns  = new Array();
	if (typeof prev == typeof UtilKit.addhook) {
		fns[fns.length] = prev;
	}
	fns[fns.length] = func;
	
	// dprint("UtilKit.addhook(" + anObject + ", " + funcName + ") nfunc = " + fns.length);
	anObject[funcName] = function() {
		// dprint("funcName: " + funcName + ": " + this);
		for (var i = 0; i < fns.length; i++) {
			// dprint(fns[i]);
			
			// 2004-04-20  Takanori Ishikawa  
			// -------------------------------------------------------------------
			// 関数内で引数の個数をチェックしている可能性もあるので、本当は引数個数で分岐すべきかも
			// ただ、その場合、引数が足りない場合なども考慮すると面倒くさいので手抜き
			 fns[i](
				arguments[0],
				arguments[1],
				arguments[2],
				arguments[3],
				arguments[4]);
			/*
			var f = fns[i]; var args = arguments;
			
			switch (fns.length) {
			case 0: f(); break;
			case 1: f(args[0]); break;
			case 2: f(args[0], args[1]); break;
			case 3: f(args[0], args[1], args[2]); break;
			default: ;
			}
			*/
		}
	};
}

/**
 * オブジェクトのプロパティを返す。
 * null の場合は例外を投げる
 * 
 * @param obj  object
 * @param name property name
 */
UtilKit.getPropertyNotNull = function(obj, name)
{
	var v = obj[name];
	
	if (null == v) {
		throw ASSERT_EXCEPTION + "object: " + obj + " name: " + name + ' must be nut null.';
	}
	
	return v;	
}

/**
 * オブジェクトを論理値に変換
 */
UtilKit.parseBoolean = function(v)
{
	if (typeof v == typeof true) {
		return v;
	} else if (typeof v == typeof "") {
		if ('true' == v) 
			return true;
		else if ('false' == v) 
			return false;
		else
			return true;
	} else if (null == v) {
		return false;
	} else {
		return true;
	}
}
/**
 * オブジェクトのプロパティから、それの登録されているキーを検索
 * 
 * @param anObject オブジェクト
 * @param aValue   プロパティ値
 */
UtilKit.getKeyForValue = function(anObject, aValue)
{
	for (var key in anObject) {
		if (aValue == anObject[key]) {
			return key;
		}
	}
	return null;
}

UtilKit.getBgColorById = function(anId, doc)
{
	var imp;
	var style;
	
	if (doc == null) {
		doc = document;
	}
	imp = XBSLayer.getLayerImpById(anId, doc);
	style = imp ? XBSLayer.getStyleObjectWithLayerImp(imp) : null;
	
	if (style == null) {
		return null;
	}
	return UtilKit.normalizeRGBColorRep(style.backgroundColor);
}
UtilKit.setBgColorById = function(anId, aColor, doc)
{
	var imp;
	var style;
	
	if (doc == null) {
		doc = document;
	}
		
	imp = XBSLayer.getLayerImpById(anId, doc);
	style = imp ? XBSLayer.getStyleObjectWithLayerImp(imp) : null;
	
	if (style == null) {
		return null;
	}
	style.backgroundColor = aColor;
}

/**
 * NN, Opera は背景色を rgb(r, g, b) という形式で
 * 渡してくるので、それを #RGB に変換
 */
UtilKit.normalizeRGBColorRep = function(aColor)
{
	var m = aColor.match(/rgb\((\d+),\s*(\d+),\s+(\d+)\)/);
	
	if (null == m) {
		return aColor;
	}
	var s = "#";
	
	for (var i = 1; i <= 3; i++) {
		var n = m[i];
		
		n = XBSUtil.parseIntNoError(n);
		n = n.toString(16);
		if (n.length == 1){
			n = '0' + n;
		}
		s += n;
	}
	return s;
}

/**
 * InitialFirstResponder:
 *   ユーザが最初に入力すべきフォーム要素
 * 
 * InitialFirstResponder を設定する関数を返す。
 * この関数は実行されると、window.initialFirstResponder にそのフォーム要素を格納し、
 * そのフォーム要素を focus() する。
 * window.onload で使われることを想定
 * 
 */
UtilKit.makeInitialFirstResponder = function(formName, elementName)
{
	return function() {
		var frm = document.forms[formName];
	
		if (frm == null) return;
		frm = frm[elementName];
		if (frm == null) return;
		
		window.initialFirstResponder = frm;
		setTimeout("window.initialFirstResponder.focus()", 500);
	};
}


//---------------------------------------------------------------------------------
// ContentsChangedListener
//---------------------------------------------------------------------------------
/**
 * 
 * ページの内容が変更されたかどうかを記録
 * 
 * @author Takanori Ishikawa
 * @version 2004/04/20
 */
function ContentsChangedListener(/* Optional */ aName)
{
	this.name = aName;
	if (this.name == null) {
		this.name = '[undefined listener]';
	}
	this.changed = false;
}
ContentsChangedListener.prototype.toString = function()
{ return '[ContentsChangedListener] ' + this.name; }

ContentsChangedListener.prototype.isChanged = function() { return this.changed; }
ContentsChangedListener.prototype.setChanged = function(b) {
	if (this.changed != b) {
		//alert(this + " changed: " + b);
	}
	this.changed = b; 
}

/**
 * イベントを登録し、登録したイベントが発生したときに
 * 変更フラグを true にする。
 * 
 * 主に html 要素の onchange イベントなどを捕捉し、
 * 変更フラグを立てるのに使う。
 */
ContentsChangedListener.prototype.listenEvent = function(anObject, eventName)
{
//	2004-04-20  Takanori Ishikawa  
//	------------------------------------------------------------------------
//	this は関数内部で実行時に解釈されるため、一度変数に入れて
//	静的スコープに束縛する必要がある（解放されない？）
	var me = this
	var callback = function(){ me.setChanged(true); };
	
	UtilKit.addhook(anObject, eventName, callback);
}


//---------------------------------------------------------------------------------
// Common
//---------------------------------------------------------------------------------
/**
 * アプリケーション全体の設定
 * 
 * @author  Takanori Ishikawa
 * @version 1.0
 */
OEMBlogGlobal = new Object();

/**
 * 記事編集で Blog ポータル・カテゴリの選択をクッキーに保存するか
 */
OEMBlogGlobal.saveTbCategoryToCookie = false;

/**
 * 記事編集でタグ編集機能（タグごと削除、移動など）を HTML タグでも有効にするか
 */
OEMBlogGlobal.enableTagEditingOnHTML = false;


/**
 * 項目設定のディスクロージャトライアングルなどでアニメーションを有効にするか
 */
OEMBlogGlobal.enableAnimationFeedback = true;

/**
 * 画像をアップロードするディレクトリのパス
 * jsp 側で適宜更新される。
 */
OEMBlogGlobal.uploadImageDirectory = null;

/**
 * アップロードできる画像ファイル名の形式
 */
OEMBlogGlobal.IMAGE_FILE_PATTERN = /{(左:|右:)?([a-zA-Z0-9-_.!~'()]+\.[a-zA-Z]+)}/;

/**
 * サイドバー：配置できる項目の最大数
 * 
 * @see sidefunc_order.js BoxConfig.maxNBoxes
 */
OEMBlogGlobal.PANEL_DISABLE_MAX_NBOXES	= 50;	// 「使用しない機能」
OEMBlogGlobal.PANEL_LEFT_MAX_NBOXES		= 50;	// 「使用する機能」左
OEMBlogGlobal.PANEL_RIGHT_MAX_NBOXES	= 50;	// 「使用する機能」右


/**
 * 
 * 記事編集、新規作成では以下の文字列を含むリンクをクリックした場合、
 * まだ変更が保存されていなければ確認ダイアログを出す。
 * 
 * @see entry_write_edit.js, entry_save_confirm.jsp
 * @see OEMBlogGlobal.watchOtherLinks
 */
OEMBlogGlobal.DOCUMENT_EDITED_WARNING = "変更は保存されていません。<br>次のページへ移ってもよろしいですか？"
OEMBlogGlobal.WATCH_OTHER_LINK_LIST = [
					/* 上 */
					'MyPage',			/* マイページ */
					'EntryWrite',		/* 記事新規作成 */
					'EntryList',		/* 記事一覧 */
					'TopicList',		/* リンクリスト */
					'SidefuncOrder',	/* サイドバー */
					'DesignChange',		/* デザイン */
					'BlogSetup',		/* 設定 */
					
					/* 左 */
					'SidefuncOrder',	/* 機能選択・並び替え */
					'SidefuncSetup',	/* 表示名変更 */
					'SidefuncEditList',		/* 機能追加 */
					'LinksList',		/* リンク集  編集 */
					'CategoryList'		/* カテゴリ  編集 */
					];


/**
 * テキストの置換などで使用する改行コード
 */
/** TODO: 仕様にしたがって、判別コードを書くこと。とりあえず、CRLF にしておく */
OEMBlogGlobal.lineSeparator = "\r\n";


OEMBlogGlobal.makeWatchOtherLinkFunction = function(aListener, prevFunc)
{
	return function(e) {
		var ret = true;
			
		if (prevFunc != null) {
			ret = prevFunc(e);
		}
		if (aListener.isChanged() == false)
			return ret;
			
		return (htmlConfirm(OEMBlogGlobal.DOCUMENT_EDITED_WARNING))
	};
}
OEMBlogGlobal.watchOtherLinks = function(aDocument, aListener)
{
	var linkArray = aDocument.links;
	var nms = OEMBlogGlobal.WATCH_OTHER_LINK_LIST;
	var link;
	var count = 0;
	
	for (var i = 0; i < linkArray.length; i++) {
		
		link = linkArray[i];
		if (null == link) continue;
		
		for (var j = 0; j < nms.length; j++) {
			if (link.href.indexOf(nms[j]) != -1) {
				var prevFunc = link.onclick;
				
				if (typeof prevFunc != 'function') {
					prevFunc = null;
				}
				link.onclick = OEMBlogGlobal.makeWatchOtherLinkFunction(aListener, prevFunc);
				
				count++;
				break;
			}
		}
		if (count == nms.length) {
			break;
		}
	}
}
