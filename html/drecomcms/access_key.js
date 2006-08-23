/*
 * Access Key Utilities
 *
 *
 * Copyright (c) 2003-2004 DRECOM CO.,LTD. All rights reserved.
 * 
 * info@drecom.co.jp
 * http://www.drecom.co.jp/
 */

/**
 * 保存ショートカットを有効にするかどうか。
 * ショートカットよりも先に、ブラウザの保存ダイアログが出る場合は無効にする。
 * 
 * @return 保存ショートカットを有効にするかどうか
 */
function shortCutSupported()
{
	return (document.all != null && null == XBSUtil.opera);
}

function access_key_onkeydown_hock(theEvent) 
{
	if (false == shortCutSupported()) {
		return true;
	}

	if (theEvent == null) {
		theEvent = window.event;
	}
	var willCancel = false;
	var k = XBSEvent.getKeyCode(theEvent);
	var m = XBSEvent.getModifierFlags(theEvent);
	if (m & XBSEvent.CONTROL_KEY_MASK){
		if (k == 83) {  // 's'
			execSave != null ? execSave() : void(0);
			willCancel = true;
		}
	} else if (m & XBSEvent.ALT_KEY_MASK){
		if (k == 80) {  // 'p'
			execPrint != null ? execPrint() : void(0);
			willCancel = true;
		}
	}
	if (willCancel) {
		theEvent.returnValue = false;
		return false;
	}
	
	return true;
}
