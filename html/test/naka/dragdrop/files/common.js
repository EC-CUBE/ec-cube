/*
 * ���� JavaScript
 *
 *
 * Copyright (c) 2003 DRECOM CO.,LTD. All rights reserved.
 * 
 * info@drecom.co.jp
 * http://www.drecom.co.jp/
 */

/**
 * �֥饦�������ΰ���������
 */
function getWidth(win)
{
	var tmp_width;
    
	if (win.document.documentElement) { 
		tmp_width = win.document.documentElement.clientWidth;
		if (tmp_width > 0) {
			return tmp_width;
		}
	}
    if (win.document.all) {
    	tmp_width = win.document.body.clientWidth;
		return tmp_width;
	}
	return win.innerWidth;
}
       
/**
 * �֥饦�������ΰ�ι⤵�����
 */
function getHeight(win)
{
	var tmp_height;
    
	if (win.document.documentElement) {
		tmp_height = win.document.documentElement.clientHeight;
		if (tmp_height > 0) {
			return(tmp_height);
		}
	}
    if (win.document.all) {
		return(win.document.body.clientHeight);
	}
	return(win.innerHeight);
}

/**
 * Window���濴�˰�ư
 */
function moveWindowToCenter(win) {
	x = (screen.width  - getWidth(win)) / 2;
	y = (screen.height - getHeight(win)) / 2;
	win.moveTo(x,y);
}

/**
 * ��˼�����ɽ�����륦����ɥ��򳫤�
 */
function openModalWindow(url, name, height, width, scrollbars, resizable, status)
{
	ModalWindow = openWindow(url, name, height, width, scrollbars, resizable, status);
	onfocus = function onFocus(){
		if (null !=ModalWindow && !ModalWindow.closed) {
			try {
				ModalWindow.focus();
			} catch(e) {
				document.onmousemove = null;
			}
		} else {
			document.onmousemove = null;
		}
	}
	
	document.onmousemove = onfocus;
	
	return ModalWindow;
}

/**
 * ������ɥ��򳫤�
 */
function openWindow(url, name, height, width, scrollbars, resizable, status, left, top)
{
	var window_condition = "height=" + height + ",width=" + width + 
					((left) ? ",left=" + left : "") + ((top) ? ",top=" + top : "") +
					",scrollbars=" + scrollbars + ",resizable=" + resizable + ",toolbar=no,status=" + status;

	subWindow = window.open(url, name, window_condition);
	// �ݥåץ��åץ֥�å����ʤɤǥ֥�å����줿����JavaScript���顼�ˤʤ�ʤ��褦��
	if (subWindow) {
		subWindow.focus();
	}

	return subWindow;
}

/**
 * ������ɥ�����˳���
 */
function openWindowBack(url, name, height, width, scrollbars, resizable, status)
{
	var window_condition = "height=" + height + ",width=" + width + 
					",scrollbars=" + scrollbars + ",resizable=" + resizable + ",toolbar=no,status=" + status +
					",left=" + window.screen.width;
	subWindow = window.open(url, name, window_condition);
	subWindow.blur();
	window.focus();
	subWindow.moveTo(0,0);
	return subWindow;
}

/**
 * Esc�����ǥ�����ɥ����Ĥ��륤�٥��
 * 
 * �����֥�����ɥ���onload��
 *   document.body.onkeypress = subwinCloseOnEsc;
 * �Τ褦�˻ȤäƤ���������
 */
function subwinCloseOnEsc(ev)
{
	ev || (ev = window.event);
	if (ev.keyCode == 27) {
		window.close();
		return false;
	}
	return true;
}



// 2004-06-09 Takanori Ishikawa 
// -----------------------------------------------------------
// htmlAlert(), htmlConfirm() �� html �򤽤Τޤ�
// ���Ϥ��������˥���������褦�ˤ�����

// sanitize html message
function sanitize_msg(msg)
{
	msg = msg.replace(/</g, '&lt;');
	msg = msg.replace(/>/g, '&gt;');
	msg = msg.replace(/&lt;br\s*&gt;/g, '<br>');

	return msg;
}
// alert
function htmlAlert(msg, height, width)
{
	height = (null == height) ? 150: height;
	width  = (null == width) ? 300: width;
	msg = sanitize_msg(msg);
	
	if (defined(window.showModalDialog)) {
		showModalDialog("/drecomcms/html/message_box.html", sanitize_msg(msg), "dialogHeight: " + height + "px; dialogWidth: " + width + "px; edge: Raised; center: Yes; help: No; resizable: No; status: No;");
  	} else {
		dialogArguments = msg;
		ModalWindow = open("/drecomcms/html/message_box.html", "sub_alert","height=" + height + ",width=" + width + 
					",scrollbars=no,resizable=no,toolbar=no,status=no,alwaysRaised=yes");
		onfocus = function onFocus(){
			if (null !=ModalWindow && !ModalWindow.closed) {
				ModalWindow.focus();
			}
		}
		document.onmousemove = onfocus;
		return ModalWindow;
	}
}
// confirm
function htmlConfirm(msg, height, width)
{
	height = (null == height) ? 150: height;
	width  = (null == width) ? 300: width;
	if (defined(window.showModalDialog)) {
		var value = showModalDialog("/drecomcms/html/confirm.html", sanitize_msg(msg), "dialogHeight: " + height + "px; dialogWidth: " + width + "px; edge: Raised; center: Yes; help: No; resizable: No; status: No;");
		if (typeof value == "undefined") {
			value = false;
		} 
		return value;
	} else {
		return window.confirm(msg.replace(/<br[^>]*\/?>/, '\n'));
	}
}

/**
  * �������������ؿ�
  * year:  ǯ
  * month: ��
  * day:   ��
  */
function getDayOfWeek(year, month, day) {

	DateOfWeek	= new Date();
	DateOfWeek.setYear(year);
	DateOfWeek.setMonth(month - 1);
	DateOfWeek.setDate(day);
	day_of_week_number	= DateOfWeek.getDay();

	days = new Array('��','��','��','��','��','��','��');
	return days[day_of_week_number];
}


// -----------------------------------------------------------
// �ݡ����롢�إå�
// -----------------------------------------------------------
/**
 * INPUT FORM ���طʿ��ѹ�
 */
function focusForm(obj,flag){
	if (obj == null || obj.style == null) {
		return;	
	}
	obj.style.backgroundColor = flag ? '#FFFFCC' : '#FFFFFF' 
}
function Skinup(fnr){
	win = window.open('/contents/skinpop_'+fnr,'skin','width=455,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes');
	win.focus();
}
function imgSwap(name,gifPath) {
	document.images[name].src = gifPath ;
}


// -----------------------------------------------------------
// ����
// -----------------------------------------------------------
/**
 * ������ɽ��������Ĵ��
 */
function adjustImageSize(imgElementName, widthMax, heightMax) {
	var imgs = document.getElementsByName(imgElementName);
	if (imgs) {
		if (!imgs.length) {
			if (widthMax && imgs.width > widthMax) {
				imgs.height = imgs.height * widthMax / imgs.width;
				imgs.width = widthMax;
			}
			if (heightMax && imgs.height > heightMax) {
				imgs.width = imgs.width * heightMax / imgs.height;
				imgs.height = heightMax;
			}
		} else {
			for (i=0; i<imgs.length; i++) {
				if (widthMax && imgs[i].width > widthMax) {
					imgs[i].height = imgs[i].height * widthMax / imgs[i].width;
					imgs[i].width = widthMax;
				}
				if (heightMax && imgs[i].height > heightMax) {
					imgs[i].width = imgs[i].width * heightMax / imgs[i].height;
					imgs[i].height = heightMax;
				}
			}
		}
	}
}

/**
 * ��ʸ��HTML��������곰������URL("http://��")��PickUp
 */
function getExternalImageList(orgHtml) {

	var extImageList = new Array();
	var list = new Array();
	var n = 0;	
	var exp;
	
	exp = new RegExp("<img [^>]*src=[\"']?http://[^\"']*[\"']?[^>]*>", 'ig');
	list = orgHtml.match(exp);
	if (list) {
		for (var i = 0; i < list.length; i++) {
			var str = "";
			exp = new RegExp("src=[\"']?");
			list[i].match(exp);
			str = RegExp.rightContext;
			exp = new RegExp("[\"'\\s>]|/>");
			if (str) {
				str.match(exp);
				str = RegExp.leftContext;
				
				var isExist = false;
				for (var j = 0; j < extImageList.length; j++) {
					if (extImageList[j] == str) { isExist = true; break; }
				}
				if (!isExist) { extImageList[n++] = str; }
			}
		}
	}
	return extImageList;
}

/**
 * ��ʸ��HTML��������곰��FlashURL("http://��")��PickUp
 */
function getExternalFlashList(orgHtml) {

	var extFlashList = new Array();
	var list = new Array();
	var n = 0;	
	var exp;

	var flashTag = "\\{\\\$CMSInclude [^\\\$\\}]*name=\"flash\"\\s*param=\"[^\"]*Movie=http://[^\"]*\"\\s*\\\$\\}";	
	exp = new RegExp(flashTag, 'ig');	
	list = orgHtml.match(exp);
	if (list) {
		for (var i = 0; i < list.length; i++) {
			var str = "";
			exp = new RegExp("Movie=");
			list[i].match(exp);
			str = RegExp.rightContext;
			exp = new RegExp(";");
			if (str) {
				str.match(exp);
				str = RegExp.leftContext;

				var isExist = false;				
				for (var j = 0; j < extFlashList.length; j++) {
					if (extFlashList[j] == str) { isExist = true; break; }
				}
				if (!isExist) { extFlashList[n++] = str; }
			}
		}
	}
	return extFlashList;
}
 
/**
 * ��󥯤��������̵���ˤ��롣
 * ��<a>������href°����"javascript:void(0)"���ִ���
 */
function replaceLinkInvalid(html) {
	html = html.replace(/<a [^>]*>/ig, '<a href="javascript:void(0);">');
	html = html.replace(/openPermLink\([^)]*\)/ig, 'void(0)');
	return html;
}

/**
 * URL�ѤΥ��������פ�Ԥ��ޤ���
 * '&' -> '&amp;'
 * '>' -> '&gt;'
 * '<' -> '&lt;'
 * '"' -> '&quot;'
 * ξü�ζ���� Trim
 * ' ' -> '%20'
 * @param src �о�ʸ����
 */
function /*: String :*/ escapeUrl(/*: String :*/ src) {
	var s = replaceAll(src, '&', '&amp;');
	s = replaceAll(s, '>', '&gt;');
	s = replaceAll(s, '<', '&lt;');
	s = replaceAll(s, '"', '&quot;');
	s = trim(s);
	s = replaceAll(s, ' ', '%20');
	return s;
}
function /*: String :*/ escapeScript(/*: String :*/ src) {
	var s = replaceAll(src, "\\", "\\\\");
	s = replaceAll(s, "'", "\\'");
	s = replaceAll(s, "\"", "\\\"");
	return s;
}
/**
 * ʸ��������ִ���Ԥ��ޤ���
 * @param src �о�ʸ����
 * @param oldc ��ʸ����
 * @param newc ��ʸ����
 */
function /*: String :*/ replaceAll(/*: String :*/src, /*: String :*/oldc, /*: String :*/newc) {
 var h = "";
 var b = src;
 var index = 0;
 while (true) {
  index = b.indexOf(oldc, 0);
  if (index == -1) {
   break;
  }
  src = b.replace(oldc, newc);
  h += src.substring(0, index + newc.length);
  b = src.substring(index + newc.length, src.length);
 }
 return h + b;
}
/**
 * ξü��Ⱦ��/���ѥ��ڡ�����������ޤ���
 * @param src �о�ʸ����
 */
function /*: String :*/ trim(/*: String :*/src ) {
	var s = trimR(src);
	return trimL(s);
}
/**
 * ��ü��Ⱦ��/���ѥ��ڡ�����������ޤ���
 * @param src �о�ʸ����
 */
function /*: String :*/ trimR(/*: String :*/src) {
	var nLoop = 0;
	var strFinal = src;
	var strTemp = src;
	while (nLoop < strTemp.length) {
		if ((strFinal.substring(strFinal.length - 1, strFinal.length) == " ") || (strFinal.substring(strFinal.length - 1, strFinal.length) == "��")) {
			strFinal = strTemp.substring(0, strTemp.length - (nLoop + 1));
		}
		else {
			break;
		}
		nLoop++;
	}
	return strFinal;
}
/**
 * ��ü��Ⱦ��/���ѥ��ڡ�����������ޤ���
 * @param src �о�ʸ����
 */
function /*: String :*/ trimL(/*: String :*/src) {
	var nLoop = 0;
	var strFinal = src;
	var strTemp = src;
	while (nLoop < strTemp.length) {
		if ((strFinal.substring(0, 1) == " ") || (strFinal.substring(0, 1) == "��")) {
			strFinal = strTemp.substring(nLoop + 1, strTemp.length );
		}
		else {
			break;
		}
		nLoop++;
	}
	return strFinal;
}

/**
 * �Ѿ�
 */
function extend(subClass, superClass) {
    for (var prop in superClass.prototype) {
        subClass.prototype[prop] = superClass.prototype[prop];
    }
}


/**
 * �ݥåץ��åץ֥�å����к�
 * window.onPopupBlocked,window.onPopupNonBlocked�򥪡��С��饤�ɤ��Ƥ���������
 */
window.onPopupBlocked = function(){
	alert('������ɥ��򳫤��ޤ���Ǥ������ݥåץ��åץ֥�å�����ͭ���ˤ��Ƥ�����ϡ�̵���ˤ��Ƥ���������');
};

window.onPopupNonBlocked = function(){}

if (typeof window.org_open == 'undefined'){
	window.org_open = window.open;
	window.open = function() {
		var win = (2 == window.open.arguments.length)
					? window.org_open(window.open.arguments[0], window.open.arguments[1])
					: window.org_open(window.open.arguments[0], window.open.arguments[1], window.open.arguments[2]);
		if (null == win) {
			window.onPopupBlocked(window.open.arguments);
		} else {
			window.onPopupNonBlocked(window.open.arguments);
		}
		return win;
	}
}


