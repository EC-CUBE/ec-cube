<style type="text/css">    
    div.dragged_elm {
        position:   absolute;
        z-index:    100;
        border:     1px solid black;
        background: rgb(195,217,255);
        color:      #333;
        cursor:		hand;
        PADDING-RIGHT: 	2px;
        PADDING-LEFT: 	2px;
        PADDING-BOTTOM: 2px; 
        PADDING-TOP: 	5px;
        FONT-SIZE: 		12pt;
    }

    div.drop_target {
        border:      0px solid black;
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

<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="page_id" value="<!--{$page_id}-->">
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt}-->">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg" >
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--�ᥤ�󥨥ꥢ-->
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
						
						<!--��Ͽ�ơ��֥뤳������-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�쥤�������Խ�</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--���쥤�������Խ�����������-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=""><strong>�쥤�������Խ�</strong></td>
								<td bgcolor="#f2f1ec" align="center" colspan=""><strong>̤���ѥ֥�å�</strong></td>
							</tr>
							<tr class="fs12n">
								<!--���쥤�����ȡ���������-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="495" border=0 cellspacing="1" cellpadding="" summary=" " bgcolor="ffffff">
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height = 50>
											<td bgcolor="#cccccc" align="center" colspan=3> �إå����� </td>
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n">
											<!-- ������ ���ʥӥơ��֥� ������ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" width="165" height="400" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center" name='LeftNavi' width="165" height="400" id="layout">
															<div tid="LeftNavi" class="drop_target" id="t1" style="width: 165px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ������ ���ʥӥơ��֥� ������ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" width="165" height="400" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<!-- ������ �ᥤ������ơ��֥� ������ -->
													<thead>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainHead' height="100" id="layout">
															<div tid="MainHead" class="drop_target" id="t2" style="width: 165px; height: 100px;"></div>
														</td>
													</tr>
													</thead>
													<!-- ������ �ᥤ������ơ��֥� ������ -->
													<!-- ������ �ᥤ�� ������ -->
													<tr class="fs12n">
														<td height=198 align="center" name='Main'>�ᥤ��</td>
													</tr>
													<!-- ������ �ᥤ�� ������ -->
													<!-- ������ �ᥤ�����ơ��֥� ������ -->
													<tfoot>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainFoot' height="100" id="layout">
															<div tid="MainFoot" class="drop_target" id="t4" style="width: 165px; height: 100px;"></div>
														</td>
													</tr>
													</tfoot>
													<!-- ������ �ᥤ�����ơ��֥� ������ -->
												</table>
											</td>
											<!-- ������ ���ʥӥơ��֥� ������ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" width="165" height="400" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center" name='RightNavi' width="165" height="400" id="layout">
															<div tid="RightNavi" class="drop_target" id="t3" style="width: 165px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ������ ���ʥӥơ��֥� ������ -->
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height=50><td bgcolor="#cccccc" align="center" colspan=3>�եå�����</td></tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
									</table>
								</td>
								<!--���쥤�����ȡ������ޤ�-->
				
								<!--��̤���ѥ֥�å�����������-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="140" border="0" cellspacing="1" cellpadding="" summary=" " bgcolor="#ffffff">
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" height="400" name="Unused" id="layout">
												<div tid="Unused" class="drop_target" id="t5" style="width: 160px; height: 500px;"></div>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" height="30">
												<input type='button' value='�����֥�å�����' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','','');"  />
											</td>
										</tr>
									</table>
								</td>
								<!--��̤���ѥ֥�å��������ޤ�-->
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<input type='button' value='��¸' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','','');"  />
									<input type='button' value='�ץ�ӥ塼' name='preview' onclick="doPreview();" <!--{if $page_id == "0" or $exists_page == "0" }-->DISABLED<!--{/if}--> />
								</td>
							</tr>
						</table>
						<!--���쥤�������Խ��������ޤ�-->
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>

						<!--���ڡ�����������������-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>�Խ���ǽ�ڡ���</strong></td>
							</tr>

							<!--{foreach key=key item=item from=$arrEditPage}-->
							<tr class="fs12n" height=20>
								<td align="center" width=600 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<a href="<!--{$smarty.server.PHP_SELF}-->?page_id=<!--{$item.page_id}-->" ><!--{$item.page_name}--></a>
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.tpl_dir != ""}-->
										<input type='button' value='�ڡ����Խ�' name='page_edit' onclick="location.href='./main_edit.php?page_id=<!--{$item.page_id}-->'"  />
									<!--{else}-->
										�ڡ����Խ��Ǥ��ޤ���
									<!--{/if}-->
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.edit_flg == 1}-->
									<input type='button' value='���' name='del' onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','','');"  />
									<!--{/if}-->
								</td>
							</tr>
							<!--{/foreach}-->

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3>
									<input type='button' value='�����ڡ�������' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"  />
								</td>
							</tr>
						</table>
						<!--���ڡ��������������ޤ�-->

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
			<!--�ᥤ�󥨥ꥢ-->
		</table>
		</td>
	</tr>

</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->		

<!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
<div align=center target_id="<!--{$item.target_id}-->" did="<!--{$smarty.foreach.bloc_loop.iteration}-->" class="dragged_elm" id="<!--{$item.target_id}-->"
	 style="left:350px; top:0px; filter: alpha(opacity=100); opacity: 1; z-index: 2; width: 130px; height: 30px;">
	 <!--{$item.name}-->
</div>
<input type="hidden" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->">
<input type="hidden" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->">
<input type="hidden" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->">
<input type="hidden" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->">
<!--{/foreach}-->

<div class="check_line" id=checkline style="VISIBILITY: hidden; WIDTH: 130px; POSITION: absolute; HEIGHT: 1px"><HR color=#ff5555></DIV>
</form>
<script type="text/javascript">

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
//    return parseInt( elm.style.height );
    return parseInt( elm.offsetHeight );
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

				// target_id �ν񤭴���
		        setAttrValue ( gDragged, 'target_id', tid );

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
    setCursor ( gDragged, 'hand' );
    setZindex ( gDragged , 2);
    
    // �¤��ؤ�
	fnSortObj();
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

    var evtX = getEventX(evt) + document.body.scrollLeft;
    var evtY = getEventY(evt) + document.body.scrollTop;
    
    var drop_target = document.getElementById( drop_target_id );
    
	drp_left = drop_target.offsetLeft;
	drp_top = drop_target.offsetTop;
    
    var x = drp_left;
    var y = drp_top;

    var width = getWidth ( drop_target );
    var height = getHeight ( drop_target );
    
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

// ����κ���
function fnCreateArr( addEvt , top , left ){

	var arrObjtmp = new Object();
	arrObjtmp['LeftNavi'] = Array();
	arrObjtmp['RightNavi'] = Array();
	arrObjtmp['MainHead'] = Array();
	arrObjtmp['MainFoot'] = Array();
	arrObjtmp['Unused'] = Array();

	for ( var i = 0; i < all_elms.length; i++ ) {
 

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
			arrObjtmp[target_id][len].width = getWidth ( all_elms[i] );
			arrObjtmp[target_id][len].height = getHeight ( all_elms[i] );

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
	
	drp_left = all_elms[cnt].offsetLeft;
	drp_top = all_elms[cnt].offsetTop;

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
//				alert(all_elms[cnt].offsetTop);
			}else if (tid == 'Unused'){
				target_height = NaviHeight+100;
			}

			all_elms[cnt].style.height = target_height
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



// �������
function init () {
    document.body.ondrag = function () { return false; };
    document.body.onselectstart = function () { return false; };
    
    // ������ɥ������������
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");    
    
	// ������ɥ��������ѹ����٥�Ȥ˴�Ϣ�դ�
    window.onresize = fnMoveObject;

    // div���������
    all_elms = document.getElementsByTagName ( 'div' );
    
	// td���������
	all_td = document.getElementsByTagName ( 'td' );

/**************************************************************************************************************************/
	// �֥�å��ι⤵���������
	for ( var i = 0; i < all_elms.length; i++) {
		var elm_class = getAttrValue ( all_elms[i], 'class' );
		if (elm_class == 'dragged_elm') {
			all_elms[i].height = all_elms[i].offsetHeight
		}
	}
/**************************************************************************************************************************/

	// �������
	fnCreateArr(0);
	
    // �¤��ؤ�
	fnMoveObject();
	
	<!--{$complate_msg}-->
}

// ���̤Υ��ɥ��٥�Ȥ˴�Ϣ�դ�
addEvent ( window, 'load', init, false );
</script>
