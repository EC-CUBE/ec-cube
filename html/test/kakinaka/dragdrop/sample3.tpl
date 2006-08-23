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

// �������������饹�����
function SC_Size() {
	this.id = '';				// ID
	this.left = 0;				// ���֤���Y����ɸ
	this.top = 0;				// ���֤���X����ɸ
	this.width = blocWidth;		// ���֥������Ȥ���
	this.height = blocHeight;	// ���֥������Ȥι⤵
	this.target_id = '';		// ���־��ʺ��ʥӤȤ���
	this.margin = 10;			// ��Υ��֥������ȤȤ���
	this.obj;
};

// �ѿ����
var blocHeight = 50;		// �֥�å��ι⤵
var blocWidth = 50;			// �֥�å�����

var gDragged = "";

var arrObj = new Object();	// �֥�å����֥������ȳ�Ǽ��
arrObj['B1'] = Array();
arrObj['B2'] = Array();
arrObj['B3'] = Array();

var objCheckLine = "";
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

        try {
           
            node.filters["alpha"].opacity = val*100;

        }
        catch (e) {
        }
       
    } else if (node.style.opacity) {

        node.style.opacity = val;

    }
   
}

// Zindex���ѹ����������ɽ�����ء�
function setZindex(node, val) {
	node.style.zIndex = val;
}

// �ͤ����
function getAttrValue ( elm, attrname ) {
    return elm.attributes[ attrname ].nodeValue;
}

// �ͤ򥻥å�
function setAttrValue ( elm, attrname, val ) {
    elm.attributes[ attrname ].nodeValue = val;
}

// ���֥������Ȥ�X��ɸ�����
function getX ( elm ) {
   return parseInt(elm.style.left);
}

// ���֥������Ȥ�Y��ɸ�����
function getY ( elm ) {
    return parseInt(elm.style.top);
}

// X��ɸ�����
function getEventX ( evt ) {
    return evt.pageX ? evt.pageX : evt.clientX;
}

// Y��ɸ�����
function getEventY ( evt ) {
    return evt.pageY ? evt.pageY : evt.clientY;
}

// ���֥������Ȥ��������
function getWidth ( elm ) {
    return parseInt( elm.style.width );
}

// ���֥������Ȥι⤵�����
function getHeight ( elm ) {
    return parseInt( elm.style.height );
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
	    setZindex ( gDragged , 0);
    
	    addEvent ( document, 'mousemove', onMouseMove, false );
	    addEvent ( document, 'mouseup', onMouseUp, false );
	    mouseFlg = true;
	}
}


// �ޥ����ࡼ�֥��٥��
function onMouseMove(evt) {

    // �ɥ�å���ϥ饤���ɽ��
    objCheckLine.style.visibility = "visible";  
    
	// ���ߤκ�ɸ�����
	var x = getEventX ( evt );					// �ޥ�����ɸ X
	var y = getEventY ( evt );					// �ޥ�����ɸ Y
    var nowleft = getEventX ( evt ) - gDeltaX;	// ���֥������Ⱥ�ɸ LEFT
    var nowtop = getEventY ( evt ) - gDeltaY;	// ���֥������Ⱥ�ɸ TOP

    // ���֥������Ȥ��ư
    moveElm ( gDragged, nowleft, nowtop );
    
    // ������ɥ������������
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");
    
    // �ޥ����������뤬������ɥ��γ��˽Ф����ˤϸ����᤹
	if (x > scrX-5 || x < 5 || y > scrY-5 || y < 5) {
		// ���ΰ��֤��᤹
        moveElm ( gDragged, gOrgX, gOrgY );
        setAttrValue ( gDragged, 'target_id', gtarget_id );
	}
	
    for ( var i = 0; i < all_elms.length; i++ ) {
    	// drop_target��ˤ������ˤΤ߽�����Ԥ�
	    if ( isEventOnElm ( evt, all_elms[i].id ) ) {
            if ( all_elms[i].attributes['tid'] ) {
	            var tid = getAttrValue ( all_elms[i], 'tid' );

				// target_id �ν񤭴���
		        setAttrValue ( gDragged, 'target_id', tid );

/*
			    // �饤�����
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

				// ����κƺ���
				fnCreateArr(1, y, x);
				// ������¤��ؤ�
				fnChangeObj(tid);				
		    }
		}
    }
}


// �ޥ������åץ��٥��       
function onMouseUp(evt) {

	// ȾƩ�����ޥ����ݥ��󥿡������̽������᤹
    setOpacity( gDragged, 1);
    setCursor ( gDragged, 'hand' );
    setZindex ( gDragged , 2);

	// ���٥�Ȥδ�Ϣ�դ����
	if (mouseFlg == true) {
	    removeEvent ( document, 'mousemove', onMouseMove, false );
	    removeEvent ( document, 'mouseup', onMouseUp, false );
	    mouseFlg = false;
	}

    if ( isOnDropTarget (evt) ) {
    
	    // �¤��ؤ�
		fnSortObj();

    }
    else {
		// ���ΰ��֤��᤹
        moveElm ( gDragged, gOrgX, gOrgY );
        setAttrValue ( gDragged, 'target_id', gtarget_id );
    }
    
        
    // �ɥ�å���ϥ饤���ɽ��
    objCheckLine.style.visibility = "hidden";    
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


// �������
function init () {

    document.body.ondrag = function () { return false; };
    document.body.onselectstart = function () { return false; };
    
    // ������ɥ������������
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");    
    
	// ������ɥ��������ѹ����٥�Ȥ˴�Ϣ�դ�
    window.onresize = fnMoveObject;
    
    //
    // Assign Event Handlers
    //
   
    // div���������
    all_elms = document.getElementsByTagName ( 'div' );
    
	// �������
	fnCreateArr(0);

    // �¤��ؤ�
	fnSortObj();
	
}


// ���֥������Ȥ��¤��ؤ���Ԥ�
function fnSortObj(){
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

// ����κ���
function fnCreateArr( addEvt , top , left ){

	var arrObjtmp = new Object();
	arrObjtmp['B1'] = Array();
	arrObjtmp['B2'] = Array();
	arrObjtmp['B3'] = Array();

    for ( var i = 0; i < all_elms.length; i++ ) {
	    if ( objCheckLine == "" && getAttrValue ( all_elms[i], 'id' ) == 'checkline' ) {
	    	objCheckLine = all_elms[i];
	    }

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
			//arrObjtmp[target_id][len].width = getWidth ( all_elms[i] );
			//arrObjtmp[target_id][len].height = getHeight ( all_elms[i] );

			// �ɥ�å���Υ��֥������Ȥ�¸�ߤ���С��ɤΥ��֥������Ȥ����ޥ����ݥ��󥿤κ�ɸ����ꤹ�롣
			if (gDragged != "") {
				if (did != getAttrValue ( gDragged, 'did' )) {
					// top �Ͼ�˥��֥������Ȥ��濴���������褦�ˤ���
					arrObjtmp[target_id][len].top = (parseInt(all_elms[i].style.top) + arrObjtmp[target_id][len].height / 2 );
					arrObjtmp[target_id][len].left = all_elms[i].style.left;
				}else {
					arrObjtmp[target_id][len].top = top;
					arrObjtmp[target_id][len].left = left;
				}
			} else {
				// top �Ͼ�˥��֥������Ȥ��濴���������褦�ˤ���
				arrObjtmp[target_id][len].top = (parseInt(all_elms[i].style.top) + arrObjtmp[target_id][len].height / 2 );
				arrObjtmp[target_id][len].left = all_elms[i].style.left;
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
	
	for ( var j = 0; j < arrObj[tid].length; j++ ) {
		// ���֤����ɸ�μ���
	    var left = parseInt(all_elms[cnt].style.left) + parseInt(all_elms[cnt].style.width) / 2 - parseInt(arrObj[tid][j].width) / 2;
	    if (j == 0){
	    	var top = getY ( all_elms[cnt] ) + arrObj[tid][j].margin;
	    }else{
	    	var top = arrObj[tid][j-1].top + blocHeight + arrObj[tid][j].margin ;
	    }

		// ��ɸ���ݻ�
		arrObj[tid][j].top = top;
		arrObj[tid][j].left = left;
		
		// ���֤�Ԥ�
		moveElm ( arrObj[tid][j].obj, left ,top );
		
		// �⤵�׻�
		target_height = target_height + arrObj[tid][j].margin + arrObj[tid][j].height;
	}
	
	// �ɥ�åץ������åȤ���Ĵ��
	if ( parseInt(all_elms[cnt].style.height) <= target_height || target_height > 100 ) {
		all_elms[cnt].style.height = target_height+20;
	} else {
		all_elms[cnt].style.height = 100;
	}
	
}

//������ɥ�����������
function GetWindowSize(type){
    var ua = navigator.userAgent;       	// �桼���������������
    var nWidth, nHeight;                   // ������
    var nHit = ua.indexOf("MSIE");     	// ���פ�����ʬ����Ƭʸ����ź����
    var bIE = (nHit >=  0);                // IE ���ɤ���
    var bVer6 = (bIE && ua.substr(nHit+5, 1) == "6");  // �С������ 6 ���ɤ���
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
    // ������ɥ������������
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

// ���̤Υ��ɥ��٥�Ȥ˴�Ϣ�դ�
addEvent ( window, 'load', init, false );
</script>