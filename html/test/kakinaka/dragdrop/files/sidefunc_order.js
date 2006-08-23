/**
 * 
 * �����ɥС��Խ� JavaScript
 *
 * Copyright (c) 2003-2004 DRECOM CO.,LTD. All rights reserved.
 * 
 * info@drecom.co.jp
 * http://www.drecom.co.jp/
 */


//---------------------------------------------------------------------------------
// ����
//---------------------------------------------------------------------------------

// *** ��������ѿ� *** //

var gSidefuncOrderForm = null;
var SIDEFUNC_ORDER_FORM_NAME = "SidefuncOrderForm";


// 2004-04-13  Takanori Ishikawa  
// ------------------------------------------------------------------------
//
// Mac �� IE5 �Ϻ�ɸ��Ϣ�ε�ǽ�ʤ�������ʤ����ɡˤ�¿�������äƤ���
// �Τ�Ŭ�����б�
//
// ���͡�
// Mac �ο����ʤ���Ϻ�ɤ�
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
// ���̻�
var PANEL_NAME_DISABLE	= 'DISABLE';	// ����ǽBOX��ID
var PANEL_NAME_LEFT		= 'LEFT';		// �⡦����ǽBOX��ID
var PANEL_NAME_RIGHT	= 'RIGHT';		// �⡦����ǽBOX��ID

//���٤Ƥ� Panel ��ץ�ѥƥ��ˤ�ĥ��֥�������
// key �� name
var gAllPanels         = new Object();

// ������ν���
var gPanelNames         = [PANEL_NAME_DISABLE, PANEL_NAME_LEFT, PANEL_NAME_RIGHT];


// --- Box ---
var BOX_ID_PREFIX 		= 'f';			// Box �� ID �ץ�ե��å���
var BoxConfig = {
	width: 		120,	// Box ��
	height: 	20,		// Box �⤵
	padding: 	10,		// Box �֤Υ��ڡ���
	marginTop: 	5,		// ���־��Box�ȿƥѥͥ�ȤΥ��ڡ���
	marginLeft: 5		// Box �ȿƥѥͥ�κ�¦�Υ��ڡ���
};

// Box �����ΰ�κǾ��⤵
var MIN_BOXIES_HEIGHT	= 200;

// ���٤Ƥ� Box ��ץ�ѥƥ��ˤ�ĥ��֥�������
var gAllBoxes           = new Object();

/**
 * �ѥͥ�˳�Ǽ�Ǥ������ Box ����Ͽ�������֥�������
 * 
 * key: �ѥͥ�̾ value: �ѥͥ�˳�Ǽ�Ǥ������ Box ��
 * 
 */

BoxConfig.maxNBoxes = new Object();
BoxConfig.maxNBoxes[PANEL_NAME_DISABLE]	= OEMBlogGlobal.PANEL_DISABLE_MAX_NBOXES;	// �ֻ��Ѥ��ʤ���ǽ��
BoxConfig.maxNBoxes[PANEL_NAME_LEFT]	= OEMBlogGlobal.PANEL_LEFT_MAX_NBOXES;		// �ֻ��Ѥ��뵡ǽ�׺�
BoxConfig.maxNBoxes[PANEL_NAME_RIGHT]	= OEMBlogGlobal.PANEL_RIGHT_MAX_NBOXES;		// �ֻ��Ѥ��뵡ǽ�ױ�

/**
 * �ڡ����ɤ߹��߻������֥ǡ���
 */
gInitialBoxPositionsData = null;

//---------------------------------------------------------
// ���֤ʤ�
//---------------------------------------------------------
/**
 * HTML �� hidden input ���Ǥ�����ܤ����֤ʤɤξ�����ɤ߼�륪�֥�������
 * 
 * @author Takanori Ishikawa
 * @version 2004/04/23
 */
SFSettings = new Object();

/**
 * HTML �� hidden input ���Ǥ��������ͤ��ɤ߼��
 */
SFSettings.loadBoxPositions = function()
{
	var positions = new Object();
	var orders       = new Array();
	var panelNames   = new Array();	
	
	// hidden����box_position�����
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
 * ���ߤξ��֤�ñ��Υ��֥������Ȥ˥��󥳡��ɤ����֤���
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
 * �����ɥС����ѹ���ƻ뤹�륪�֥�������
 * ContentsChangedListener �Υ��֥��饹
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
	// ������ prototype �Υ᥽�åɤ�ƤӽФ��Ƥ⤦�ޤ������ʤ���

	this.changed = (gInitialBoxPositionsData != persistent);
}

//---------------------------------------------------------
// Drag & Drop: SFDragManager (Sidebar Function DragManager)
//---------------------------------------------------------
/**
 * �ɥ�å�������ͭ����������ξ��֤��ݻ����뤿��Υ��֥�������
 * �ɥ�å������ϰ��٤ˤҤȤĤ���������ʤ��Τǡ����ޤΤȤ���
 * Singleton �Ȥ��Ƽ���
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
SFDragManager.ondrop = null;  // ���֤��ѹ����줿


// �ɥ�å����������Ѥ��� document �Υ��٥�ȥϥ�ɥ������
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

// �ɥ�å����������Ѥ��� document �Υ��٥�ȥϥ�ɥ����
SFDragManager.disbleEventHandler= function()
{
	document.onmousemove	= '';
	document.onmouseup		= '';
}


//�ɥ�å���Υ��֥�������

SFDragManager.destination = null;	/* ��ư�� Panel */
SFDragManager.source      = null;	/* ��ư�� Panel */
SFDragManager.box         = null;	/* �ɥ�å���� Panel */

/**
 * �����ɥС����ܤ��ѹ���ƻ뤹�륪�֥������Ȥ��֤���
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
	// �����ɥС������֤��ѹ����줿���ɤ�����Ͽ
	
	var listener = SFDragManager.getChangedListener();
	
	listener.listenEvent(SFDragManager, 'ondrop');
	OEMBlogGlobal.watchOtherLinks(document, listener);
}

/**
  * setOnLoad()
  *
  * �ڡ����ɤ߹��߻��ν������
  */
SFDragManager.loaded = false;
SFDragManager.onload = function() 
{
	// �ե���������
	gSidefuncOrderForm = UtilKit.getPropertyNotNull(document, SIDEFUNC_ORDER_FORM_NAME);
	
	// �� Panel, Box ���֥�����������
	var boxPositions = SFSettings.loadBoxPositions();
	gInitialBoxPositionsData = SFSettings.getObjectForPersistent();
	for (var i = 0; i < gPanelNames.length; i++) {
		var nm = gPanelNames[i];
		var panel;
		
		panel = new Panel(nm, boxPositions[nm]);
		
		for(var key in panel.box_position) {
			gAllBoxes[key] = new Box(key);
		}
		
		// Box �ν������
		panel.setBoxPosition();
		Panel[nm] = panel;
		
		gAllPanels[nm] = panel;
	}
	
	
	// �ɥ�å������ν���
	SFDragManager.setUpContentsChangedListener();
	SFDragManager.enableEventHandler();
	
	// onload �⥤�٥��
	resetPositionsInEventHandler();
	window.onresize = resetPositionsInEventHandler;
	
	SFDragManager.loaded = true;
	// 2004-04-20  Takanori Ishikawa  
	// ------------------------------------------------------------------------
	// Opera �Ǥ� onload �����ǥ쥤�䡼�κ����褬�֤˹��ʤ���
	// ���δؿ���Ȥäơ�̵���������褹�롣
	if (window.opera) {
		SFDragManager.loaded = false;
		setTimeout("refreshDisplay()", 0.8*1000);
	}
	
}


//---------------------------------------------------------
// ���٥�Ƚ���
//---------------------------------------------------------
// CheckLine
/**
 *  �ɥ�å���� Box �����������ս��ؤ���������Υ饤�� 
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
// Mac IE �Ǥϥ��٥�ȥϥ�ɥ����Ǻ�ɸ�ط�������Ԥ��Ȥ��ޤ������ʤ��Τ�
// �������٤餻�Ƽ¹�
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
  * ���٤Ƥ� Box �������
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
 * ���٤Ƥ� Box ����پä��������ˤ��٤�ɽ��
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
// �ܥå����Υɥ�å����ɥ�å�
// -----------------------------------------------------------------

/**
  * pickUpBox()
  * theEvent: ���٥��
  *
  * Box������夲��(����å����줿)���ν����򤹤�ؿ�
  */
SFDragManager.pickUpBox = function(theEvent) {
	if (null == theEvent) {
		theEvent = window.event;
	}
	if (SFDragManager.box != null || SFDragManager.loaded == false)
		return;
	
	// �ޥ������ּ���
	var mouse_x	    = XBSEvent.getMouseX(theEvent);
	var mouse_y	    = XBSEvent.getMouseY(theEvent);
	var pickedPanel = getPanelAtPosition(mouse_x, mouse_y);

	if (null == pickedPanel) 
		return;

	// ���򤵤줿Box���¤ӽ��ֹ�� id �����
	var beforeId = pickedPanel.orderIndexOfBoxAt(mouse_x, mouse_y);
	var funcId	= UtilKit.getKeyForValue(pickedPanel.box_position, beforeId);
	
	if (null == funcId)
		return;
	
	//Box������ȼ���������
	var selected = new XBSLayer(BOX_ID_PREFIX + funcId);
	if (null == selected) 
		return;

	selected.setZIndex(selected.getZIndex() +1);

	//���򤵤줿Box��X,Y��ɸ����¸
	before_box_x	= selected.getX() + MAC_IE5_PITCH_X;
	before_box_y	= selected.getY() + MAC_IE5_PITCH_Y;

	//�ޥ�����Box�����κ�
	box_to_mouse_x	= mouse_x - before_box_x;
	box_to_mouse_y	= mouse_y - before_box_y;
	
	// �ɥ�å�����
	SFDragManager.box = selected;
	SFDragManager.source = pickedPanel;
	before_order_id = beforeId;
	function_id = funcId;
}

/**
 * 
 * Box���ư����ؿ�
 * 
 * @param theEvent mousemove ���٥��
 */
SFDragManager.moveBox = function(theEvent) {
	var selected = SFDragManager.box;
	
	if (null == selected || SFDragManager.loaded == false)
		return;
	if (null == theEvent) {
		theEvent = window.event;
	}
	//
	// �ɥ�å��桢������ɥ�����Ф���ꥻ�åȤ��ƽ����
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
		// �����å��饤��ɽ��
		curPanel.showCheckLineAtPosition(mouse_x, mouse_y);
	}

	//Box��ư
	move_to_x = mouse_x - box_to_mouse_x;
	move_to_y = mouse_y - box_to_mouse_y;
	
	selected.setLeftTopPosition(move_to_x, move_to_y);
}

/**
 * Box �򲼤������ν���
 * 
 * @param theEvent ���٥��
 */
SFDragManager.downBox = function(theEvent)
{
	if (SFDragManager.loaded == false) {
		return;
	}
	if (null == theEvent) {
		theEvent = window.event;
	}
	// �����å��饤���ä�
	getCheckLineLayer().setVisible(false);

	// �ޥ������ּ���
	var mouse_x = XBSEvent.getMouseX(theEvent);
	var mouse_y = XBSEvent.getMouseY(theEvent);
	
	// -------------------------------------
	// ��ư�ΰ��⤫���������å�
	// -------------------------------------
	// ��ư��Υѥͥ����������ɥ�å�����򹹿�
	// �ɥ�å�����Υѥͥ�
	var beforePanel = SFDragManager.source;
	var afterPanel  = getPanelAtPosition(mouse_x, mouse_y);
	
	SFDragManager.destination = afterPanel;
	if (null == beforePanel) {
		throw ASSERT_EXCEPTION + 'SFDragManager.source must be not null';
	}
	
	var droppedBox = gAllBoxes[function_id];
	var after_order_id = afterPanel.orderIndexOfMovingAt(mouse_x, mouse_y);
	
	// �ɥ�åפǤ��ʤ���硢Box ���ᤷ�ơ��ɥ�å�������λ����
	if (false == afterPanel.canDropAtPosition(beforePanel, droppedBox, mouse_x, mouse_y)) {
		droppedBox.setPosition(before_box_x, before_box_y);
		clearMove();
		return;
	}

	// ��ư����Box��ѥͥ뤫�����������֤���������
	delete beforePanel.box_position[function_id];	
	for (key in beforePanel.box_position) {
		if (before_order_id < beforePanel.box_position[key]) {
			--beforePanel.box_position[key];
		}
	}

	//after_order_id�ν���
	if (!(beforePanel.equals(afterPanel) && before_order_id <= after_order_id)) {
		++after_order_id;
	}


	// ����Υ���ǥå����򹹿�
	if (after_order_id > afterPanel.getMaxOrderIndex() + 1) {
		after_order_id = afterPanel.getMaxOrderIndex() + 1;
	}

	//��������֤�Ĥ��ꡢ��������
	for (key in afterPanel.box_position) {
		if (after_order_id <= afterPanel.box_position[key]) {
			++afterPanel.box_position[key];
		}
	}
	afterPanel.box_position[function_id] = after_order_id;

	//Box������
	if (false == beforePanel.equals(afterPanel)) {
		beforePanel.setBoxPosition();
	}
	afterPanel.setBoxPosition();

	// HTML��hidden���ѹ�
	for (var nm in gAllPanels) {
		var p = gAllPanels[nm];

		for (key in p.box_position) {
			gSidefuncOrderForm.elements['order'][key].value = p.box_position[key];
			gSidefuncOrderForm.elements['position'][key].value = p.getPosition();
		}
	}

	// ��ư�β��
	clearMove();
	if (SFDragManager.ondrop != null && typeof SFDragManager.ondrop == 'function') {
		SFDragManager.ondrop();
	}	
}


/**
  * clearMove()
  *
  * Box��ư��λ����ؿ�
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
  * �ġ��Υɥ�å���ǽ�ʹ��ܤ��ݻ������ΰ�
  * 
  * ���ޤΤȤ���������ֻ��Ѥ��ʤ���ǽ�סֻ��Ѥ��뵡ǽ�����סֻ��Ѥ��뵡ǽ������
  *
  * @param aPanelName    �ѥͥ��̾��
  * @param aPositionArray Box�����־�������
  *
  * @author Yusuke Saito
  * @version 2003/10/10
  * 
  * @author Takanori Ishikawa
  * @version 2004/04/20
  */
function Panel(aPanelName, aPositionArray)
{
	this.name			   = aPanelName;						// �ѥͥ��̾��(div��id)
	this.box_position	   = aPositionArray;					// Box�����־�������
	this.box_limit_number  = BoxConfig.maxNBoxes[aPanelName];	// �ѥͥ���κ���Box��
	
	this.position = -1;
	for (var i = 0; i < gPanelNames.length; i++) {
		if (this.name == gPanelNames[i]) {
			this.position = i;
			break;
		}
	}
	
	// �ѥͥ롦�ܥå�������ư�γ��ΰ������
	this.setSections();
}

/**
 * Box �� Y offset
 * 
 * @param anIndex order index
 */
Panel.getBoxSectionOffsetY = function(anIndex)
{
	if (Panel.box_section_y == null) {  // Box ��������
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
 * ���
 * 
 * @param aPanel �ѥͥ�
 */
Panel.prototype.equals = function(aPanel)
{
	// 2004-04-23  Takanori Ishikawa 
	// ------------------------------------------------------------------------
	// instanceof �� IE 6 �Ǥ�̤���ݡ���	
	if (aPanel == null || /*false == (aPanel instanceof Panel)*/ typeof aPanel.name == 'undefined') {
		return false;
	}	
	return this.name == aPanel.name;
}


/**
  * setSections()
  * 
  * ������ɥ������Ȥ����ѥͥ�Σ��դ�X,Y��ɸ�����ꤹ��ؿ�
  * left:����X right:����X top:����Y under:����Y
  */
Panel.prototype.setSections = function() {

	// �ƥ쥤�䡼�ΰ��ּ���
	this.setUpPanelSections();

	//Box�ΰ������
	this.setBoxSection();

	//Box��ư���ΰ������
	this.setMoveSection();
}


/**
  * setUpPanelSections()
  * 
  * ������ɥ������Ȥ����ѥͥ�Σ��դ�X,Y��ɸ�����ꤹ��ؿ�
  * left:����X right:����X top:����Y under:����Y
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
  * Box�μ��ޤ��ΰ�����ꤹ��ؿ�
  * box_section[�¤ӽ��ֹ�] => (left => ����X, right => ����X, top => ����Y, under => ����Y)
  */
Panel.prototype.setBoxSection = function() {

	//Box�ΰ������
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
 * �ѥͥ����ǰ��ֲ��� Box �Υ���ǥå������֤�
 * 
 * @return ���ֲ��� Box �Υ���ǥå�����
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
 * ���ꤵ�줿���֤˺Ǥ�ᤤ��Box ��������ǽ�ʲս�˥����å��饤���ɽ�����롣
 * 
 * @param x x ��ɸ
 * @param y y ��ɸ
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
		// �����å��饤���ư
		var check_line_left = this.panel_section['left'];
		var check_line_top = this.move_section[moveOrderId]['top'];

		// ��ö�����å��饤���ä�
		getCheckLineLayer().setVisible(false);
		getCheckLineLayer().setLeftTopPosition(check_line_left, check_line_top);
		getCheckLineLayer().setVisible(true);
	}
}


/**
  * setMoveSection()
  * 
  * Box���ư�Ǥ����ΰ�����ꤹ��ؿ�
  * move_section[�¤ӽ��ֹ�] => (left => ����X, right => ����X, top => ����Y, under => ����Y)
  */
Panel.prototype.setMoveSection = function() {

	//��ư�ΰ������
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

	// �ѥͥ뤫�鳰�줿��
	// �ѥͥ�κ��� Box ����Ķ���� = false
	return (orderIndex != null && (this.equals(aSourcePanel) || this.box_limit_number > maxIndex));
}

/**
 * ��ư�桢���������ս�Υ���ǥå������֤���
 * 
 * @param x x ��ɸ
 * @param y y ��ɸ
 * @return ��������륤��ǥå������ʤ���� null
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
  * Box������֤���ؿ�
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
 * �ġ��Υɥ�å��ǰ�ư��ǽ�ʹ���
 * 
 * @param anId HTML ���Ǥ� id °��
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
 * HTML �� hidden input ���Ǥ��Ϥ����ץ�ѥƥ���̾��
 */
Box.POSITION_PROPERTY_NAME = "position";
Box.ORDER_PROPERTY_NAME    = "order";
Box.NAME_PROPERTY_NAME     = "sidefuncName";
Box.AUTHOR_PROPERTY_NAME   = "authorCd";
Box.ID_PROPERTY_NAME       = "sidefuncId";


/**
 * �ɥ�å������κ���˸ƤФ�롣
 * 
 * @param theEvent mousemove
 * @return �ɥ�å������򥭥�󥻥뤹����ϡ�false ���֤���
 */
Box.MOVABLE_INSET = 5;	/* �ɥ�����Ȥ�ü - Box.MOVABLE_INSET = �ɥ�å���ǽ���ΰ� */
Box.canMoveWithEvent = function(theEvent) 
{
	// ����Ū�ʰ��֤��ۤ����Τǡ�XBSDocument.getPageOffsetX/Y() �����
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
  * Box���礭�������ꤹ��ؿ�
  */
Box.prototype.setUpSize = function() 
{
	var imp = this.getHTMLElement();
	
	// style.height �Ǥʤ��� opera �Ǥ��ޤ������ʤ�
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
  * Box�ΰ��֤�����
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
 * Box �βĻ�°��������
 */
Box.prototype.setVisible = function(flag)
{
	var imp = this.getHTMLElement();
	XBSLayer.setVisibilityWithLayerImp(imp, flag ? XBSLayer.VISIBLE : XBSLayer.HIDDEN);
}
