/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
// �������������饹�����
function SC_Size() {
	this.id = '';				// ID
	this.left = 0;				// ���֤���Y����ɸ
	this.top = 0;				// ���֤���X����ɸ
	this.width = 0;				// ���֥������Ȥ���
	this.height = 0;			// ���֥������Ȥι⤵
	this.target_id = '';		// ���־��ʺ��ʥӤȤ���
	this.margin = 10;			// ��Υ��֥������ȤȤ���
	this.obj;
};

// �ѿ����
var defUnused = 500;	// ̤�����ΰ�Υǥե���Ȥι⤵
var defNavi   = 400;	// �����ʥӤΥǥե���Ȥι⤵
var defMainNavi  = 100;	// �ᥤ��岼�Υǥե���Ȥι⤵
var defMain   = 200;	// �ᥤ��Υǥե���Ȥι⤵

var NowMaxHeight = 0;		// ���ߤκ���ι⤵
var MainHeight = 200;

var marginUnused 	= 688;	// ̤�����ΰ�κ��ޡ�����
var marginLeftNavi  = 180;	// ���ʥӤκ��ޡ�����
var marginRightNavi = 512;	// ���ʥӤκ��ޡ�����
var marginMain		= 348;	// �ᥤ��岼�κ��ޡ�����
var marginMainFootTop= 595;	// �ᥤ�󲼤ξ�ޡ�����

var gDragged = "";			// �ɥ�å��楪�֥�������
var gDropTarget = "";		// �ɥ�å����ϻ���DropTarget

var arrObj = new Object();	// �֥�å����֥������ȳ�Ǽ��

var mouseFlg = false;

var all_elms;				// div�������֥������ȳ�Ǽ��

// ������ɥ�������
var scrX;
var scrY;

// ���٥�Ȥδ�Ϣ�դ���Ԥ�
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


// ���٥�Ȥδ�Ϣ�դ�����
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

// �ޥ�������������ѹ�
function setCursor ( elm, curtype ) {
	elm.style.cursor = curtype;
}

// ���֥������Ȥ�Ʃ���٤��ѹ�   
function setOpacity(node,val) {

    if (node.filters) {
		node.filters["alpha"].opacity = val*100;
    } else if (node.style.opacity) {
        node.style.opacity = val;
    }
}

// Zindex���ѹ����������ɽ�����ء�
function setZindex(node, val) {
	node.style.zIndex = val;
//	alert(val);
}

// �ͤ����
function getAttrValue ( elm, attrname ) {

	if (typeof(elm.attributes[ attrname ]) != 'undefined') {
	    return elm.attributes[ attrname ].nodeValue;
	}

/*
//	if (typeof(elm.attributes.getNamedItem(attrname)) != 'object'){
		val = "";
		if((typeof ScriptEngineMajorVersion)=='function')
		{
			if( Math.floor(ScriptEngineMajorVersion()) == 5 &&
				navigator.userAgent.indexOf("Win")!=-1) //win-e5�б�
				{
				val = elm.attributes.item(attrname)
				}
			else
			{
				val = elm.attributes.getNamedItem(attrname)
			}
		} else {
			val = elm.attributes.getNamedItem(attrname)
		}
		
		alert(val.value);
		
		return val.value;
//	}
*/
}

// �ͤ򥻥å�
function setAttrValue ( elm, attrname, val ) {
    elm.attributes[ attrname ].nodeValue = val;
}

// ���֥������Ȥ�X��ɸ�����
function getX ( elm ) {
//   return parseInt(elm.style.left);
	return parseInt(elm.offsetLeft);
}

// ���֥������Ȥ�Y��ɸ�����
function getY ( elm ) {
	return parseInt(elm.offsetTop);
//    return parseInt(elm.style.top);
}

// X��ɸ�����
function getEventX ( evt ) {
    return evt.clientX ? evt.clientX : evt.pageX;
}

// Y��ɸ�����
function getEventY ( evt ) {
    return evt.clientY ? evt.clientY : evt.pageY;
}

// ���֥������Ȥ��������
function getWidth ( elm ) {
    return parseInt( elm.style.width );
}

// ���֥������Ȥι⤵�����
function getHeight ( elm ) {
//    return parseInt( elm.style.height );
    return parseInt( elm.offsetHeight );
}

// �ڡ����βĻ��ΰ��X��ɸ���������
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

// �ڡ����βĻ��ΰ��Y��ɸ���������
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


// ���֥������Ȥκ�ɸ�򥻥å�
function moveElm ( elm, x, y ) {
    elm.style.left = x + 'px';
    elm.style.top = y + 'px';
}

// �ޥ��������󥤥٥��
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
	   
	    // �ɥ�å����ȾƩ��
	    setOpacity ( gDragged, 0.6 );
	
	    // �ɥ�å���Ϻ�����ɽ��
	    setZindex ( gDragged , 2);
	    
	    addEvent ( document, 'mousemove', onMouseMove, false );
	    addEvent ( document, 'mouseup', onMouseUp, false );

	    // �ɥ�å��򳫻Ϥ����Ȥ��Ϲ⤵����ٽ�������롣
	    NowMaxHeight = defNavi;
	    	    
	    mouseFlg = true;
	}
}


// �ޥ����ࡼ�֥��٥��
function onMouseMove(evt) {

	// ���ߤκ�ɸ�����
	var x = getEventX ( evt ) + document.body.scrollLeft;					// �ޥ�����ɸ X
	var y = getEventY ( evt ) + document.body.scrollTop;					// �ޥ�����ɸ Y
    var nowleft = getEventX ( evt ) - gDeltaX;	// ���֥������Ⱥ�ɸ LEFT
    var nowtop = getEventY ( evt ) - gDeltaY;	// ���֥������Ⱥ�ɸ TOP

    // ���֥������Ȥ��ư
    moveElm ( gDragged, nowleft, nowtop );
	
    for ( var i = 0; i < all_elms.length; i++ ) {
    	// drop_target��ˤ������ˤΤ߽�����Ԥ�
	    if ( isEventOnElm ( evt, all_elms[i].id ) ) {	    
            if ( all_elms[i].attributes['tid'] ) {
	            var tid = getAttrValue ( all_elms[i], 'tid' );
	            
	            // �طʿ����ѹ� ̤�����ΰ���ѹ����ʤ�
	            all_elms[i].style.background="#ffffdd";
	            
				// target_id �ν񤭴���
		        setAttrValue ( gDragged, 'target_id', tid );

				//objCheckLine.style.top = parseInt(nowtop) + parseInt(gDragged.style.height) / 2 + 'px';
				//objCheckLine.style.top = y;

				// ����κƺ���
				fnCreateArr(1, y, x);
				// ������¤��ؤ�
				fnChangeObj(tid);
		    }
		}else{
			if ( all_elms[i].attributes['tid'] && all_elms[i].style.background!="#ffffff") {
				// �طʿ����ѹ�
				all_elms[i].style.background="#ffffff";
			}
		}
    }
}

// �ޥ������åץ��٥��       
function onMouseUp(evt) {
	// ���٥�Ȥδ�Ϣ�դ����
	if (mouseFlg == true) {
	    removeEvent ( document, 'mousemove', onMouseMove, false );
	    removeEvent ( document, 'mouseup', onMouseUp, false );
	    mouseFlg = false;
	}

    if ( !isOnDropTarget (evt) ) {
		// ���ΰ��֤��᤹
        moveElm ( gDragged, gOrgX, gOrgY );
        setAttrValue ( gDragged, 'target_id', gtarget_id );

		// ����κƺ���
		fnCreateArr(1, gOrgY, gOrgX);
    }
    
    // hidden���Ǥν񤭴���
	var did = getAttrValue( gDragged, 'did' );
	var target_id = "target_id_"+did;
	document.form1[target_id].value = getAttrValue( gDragged, 'target_id' );
	
	// ȾƩ�����ޥ����ݥ��󥿡������̽������᤹
    setOpacity( gDragged, 1);
    setCursor ( gDragged, 'move' );
    setZindex ( gDragged , 1);
    
    // �¤��ؤ�
	fnSortObj();
	
	// �طʿ����᤹
	for ( var i = 0; i < all_elms.length; i++ ) {
    	// drop_target��ˤ������ˤΤ߽�����Ԥ�
	    if ( isEventOnElm ( evt, all_elms[i].id ) && all_elms[i].attributes['tid']) {
			// �طʿ����ѹ�
			all_elms[i].style.background="#ffffff";
		}
    }
}

// DropTarget��˥��֥������Ȥ��褿����Ƚ�Ǥ���
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

// ���֥������Ȥ��¤��ؤ���Ԥ�
function fnSortObj(){
	fnSetTargetHeight();
    for ( var cnt = 0; cnt < all_elms.length; cnt++ ) {

		// class�� drop_target �ξ��Τ߽�����Ԥ�
        if ( getAttrValue ( all_elms[cnt], 'class' ) == 'drop_target' ) {
        	var tid = getAttrValue ( all_elms[cnt], 'tid' );
			
			// ������¤��ؤ�
			fnChangeObj(tid);
			
			// ����
			fnSetObj( tid, cnt );
        }
	}
}

function alerttest(msg, x, y){
 	alert(msg);
}

// ����κ���
function fnCreateArr( addEvt , top , left ){

	var arrObjtmp = new Object();
	arrObjtmp['LeftNavi'] = Array();
	arrObjtmp['RightNavi'] = Array();
	arrObjtmp['MainHead'] = Array();
	arrObjtmp['MainFoot'] = Array();
	arrObjtmp['Unused'] = Array();

	for ( var i = 1; i < all_elms.length; i++ ) {
		// class�� dragged_elm �ξ��Τ߽�����Ԥ�
		if ( getAttrValue ( all_elms[i], 'class' ) == 'dragged_elm' ) {
        
			// �ޥ��������󥤥٥�Ȥȴ�Ϣ�դ���Ԥ�
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

			// �ɥ�å���Υ��֥������Ȥ�¸�ߤ���С����Υ��֥������Ȥ����ޥ����ݥ��󥿤κ�ɸ����ꤹ�롣
			if (gDragged != "") {
				if (did != getAttrValue ( gDragged, 'did' )) {
					// top �Ͼ�˥��֥������Ȥ��濴���������褦�ˤ���
					arrObjtmp[target_id][len].top = (parseInt(getY( all_elms[i] )) + arrObjtmp[target_id][len].height / 2 );
					arrObjtmp[target_id][len].left = getX( all_elms[i] );
				}else {
					arrObjtmp[target_id][len].top = top;
					arrObjtmp[target_id][len].left = left;
				}
			} else {
				// top �Ͼ�˥��֥������Ȥ��濴���������褦�ˤ���
				arrObjtmp[target_id][len].top = i;
				arrObjtmp[target_id][len].left = getX( all_elms[i] );
			}
		}
    }
    
    arrObj = arrObjtmp;
}

// ������¤��ؤ� (�Х֥륽���Ȥ��¤��ؤ���Ԥ�) 
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

// ����
function fnSetObj( tid, cnt ){
	var target_height = 0;
	
	drp_left = getX(all_elms[cnt]); //all_elms[cnt].offsetLeft;
	drp_top = getY(all_elms[cnt]); //all_elms[cnt].offsetTop;

	for ( var j = 0; j < arrObj[tid].length; j++ ) {
		// ���֤����ɸ�μ���
	    var left = parseInt(drp_left) + parseInt(all_elms[cnt].style.width) / 2 - parseInt(arrObj[tid][j].width) / 2;
	    if (j == 0){
	    	var top = drp_top + arrObj[tid][j].margin;
	    }else{
	    	var top = arrObj[tid][j-1].top + arrObj[tid][j].margin + arrObj[tid][j-1].height
	    }

		// ��ɸ���ݻ�
		arrObj[tid][j].top = top;
		arrObj[tid][j].left = left;

		// ���֤�Ԥ�
		moveElm ( arrObj[tid][j].obj, left ,top);

		// �⤵�׻�
		target_height = target_height + arrObj[tid][j].margin + arrObj[tid][j].height;

		// hidden���ͤ�񤭴���
		var top_id = "top_" + arrObj[tid][j].id;
		document.form1[top_id].value = top;

	}
}

// �ɥ�åץ������åȤι⤵Ĵ��
function fnSetTargetHeight(){

	var NaviHeight = defNavi;
	var MainHeadHeight = defMainNavi;
	var MainFootHeight = defMainNavi;
	var UnusedHeight = defUnused;

	// �⤵�׻�
    for ( var cnt = 0; cnt < all_elms.length; cnt++ ) {
		var target_height = 0;
    
		// class�� drop_target �ξ��Τ߽�����Ԥ�
        if ( getAttrValue ( all_elms[cnt], 'class' ) == 'drop_target' ) {
        	var tid = getAttrValue ( all_elms[cnt], 'tid' );

			for ( var j = 0; j < arrObj[tid].length; j++ ) {
				target_height = target_height + arrObj[tid][j].margin + arrObj[tid][j].height;
			}

			// ������
			target_height = target_height + 20;

			// �����ʥӡ�̤�����ΰ�ι⤵���ݻ�
			if (tid == 'LeftNavi' || tid == 'RightNavi' || tid == 'Unused') {
				if (NaviHeight < target_height) {
					NaviHeight = target_height;
				}
			}

			// �ᥤ������ΰ�ι⤵���ݻ�
			if (tid == 'MainHead') {
				if (target_height > defMainNavi) {
					MainHeadHeight = target_height;
				}
			}

			// �ᥤ�����ΰ�ι⤵���ݻ�
			if (tid == 'MainFoot') {
				if (target_height > defMainNavi) {
					MainFootHeight = target_height;
				}
			}	
        }
	}

	// �ᥤ���ΰ�ι⤵���ݻ�
//	alert(NaviHeight+"/"+MainHeadHeight+"/"+MainFootHeight);
	MainHeight = NaviHeight - ( MainHeadHeight + MainFootHeight );
	if (MainHeight < defMain) {
		MainHeight = defMain;
	}

	// �ᥤ����ʬ�Τۤ����礭�����ˤϺ����ʥӤ��礭������
	if (NaviHeight < MainHeadHeight + MainFootHeight + MainHeight) {
		NaviHeight = MainHeadHeight + MainFootHeight + MainHeight;	
	}
	// �ѹ�
    for ( var cnt = 0; cnt < all_elms.length; cnt++ ) {
    	var target_height = 0;

		// class�� drop_target �ξ��Τ߽�����Ԥ�
        if ( getAttrValue ( all_elms[cnt], 'class' ) == 'drop_target' ) {
        	var tid = getAttrValue ( all_elms[cnt], 'tid' );
        	
        	// tid�ˤ�äƽ�����ʬ����
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
	
	// �ᥤ��ơ��֥�ι⤵���ѹ�
    for (var i = 0; i < all_td.length; i++) {
    	name = getAttrValue ( all_td[i], 'name' );
		if (name == 'Main') {
			all_td[i].height = MainHeight-2;
		}
    }
}

//������ɥ�����������
function GetWindowSize(type){
    var ua = navigator.userAgent;       										// �桼���������������
    var nWidth, nHeight;                  										// ������
    var nHit = ua.indexOf("MSIE");     											// ���פ�����ʬ����Ƭʸ����ź����
    var bIE = (nHit >=  0);                										// IE ���ɤ���
    var bVer6 = (bIE && ua.substr(nHit+5, 1) == "6");  							// �С������ 6 ���ɤ���
    var bStd = (document.compatMode && document.compatMode=="CSS1Compat");		// ɸ��⡼�ɤ��ɤ���

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

// ������ɥ����������ѹ��ˤʤä��Ȥ������ƤΥ��֥������Ȥ��ư����
function fnMoveObject() {

    // ������ɥ������ѹ���Ψ�����
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
// ���̤Υ��ɥ��٥�Ȥ˴�Ϣ�դ�
addEvent ( window, 'load', init, false );
