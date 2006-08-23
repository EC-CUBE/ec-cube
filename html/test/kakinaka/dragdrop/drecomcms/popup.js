/*
 * PopUp for tooltip
 *
 *
 * Copyright (c) 2003-2004 DRECOM CO.,LTD. All rights reserved.
 * 
 * info@drecom.co.jp
 * http://www.drecom.co.jp/
 */

var POPUP_ID       = 'popup';
var POPUP_HTML     = '<div id="'+POPUP_ID+'" class="popup" align="left"></div>';

var POPUP_OFFSET   = 15;
// div の大きさが可変のため、XBSLayer.LEFT_TOP しか指定できません
var POPUP_POSITION = XBSLayer.LEFT_TOP; // ポップアップの左上がマウス位置

var gPopUpLayer = null;


/**
 * ポップアップを表示する関数
 * 
 * @param theEvent イベントオブジェクト（必須）
 * @param text ポップアップに表示する内容（必須）
 *
 * @param offsetX X方向にこの距離だけイベントの位置から離す（オプション）
 * @param offsetY Y方向にこの距離だけイベントの位置から離す（オプション）
 * @param axisType ポップアップ領域の頂点のうち、どれを基準に表示するか。（オプション）
 * <ul>
 * <li>XBSLayer.LEFT_TOP     左上頂点を基準</li>
 * <li>XBSLayer.LEFT_BOTTOM  左下頂点を基準</li>
 * <li>XBSLayer.RIGHT_TOP    右上頂点を基準</li>
 * <li>XBSLayer.RIGHT_BOTTOM 右下頂点を基準</li>
 * </ul>
 * 
 */
function showPopup(theEvent, text, axisType, offsetX, offsetY)
{
	var position;
	var x, y;
	
	if (null == gPopUpLayer) {
		gPopUpLayer = XBSLayer.makeLayer(POPUP_ID);
	}
	gPopUpLayer.setInnerHTML(text);
	
	if (axisType == null) {
		axisType = POPUP_POSITION;
	}
	if (offsetX == null) {
		offsetX = POPUP_OFFSET;
	}
	if (offsetY == null) {
		offsetY = POPUP_OFFSET;
	}
	
	gPopUpLayer.popUpAtEventLocation(theEvent, axisType, offsetX, offsetY);
}

/**
  * hidePopup()
  * 
  * レイヤーでポップアップを表示する関数
  */
function hidePopup(e) 
{
	if (gPopUpLayer != null) {
		gPopUpLayer.setVisible(false);
	}
}
// ポップアップ用 html を出力しておく
document.write(POPUP_HTML);