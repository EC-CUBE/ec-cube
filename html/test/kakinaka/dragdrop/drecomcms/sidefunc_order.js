/**
 * 
 * �T�C�h�o�[�ҏW JavaScript
 *
 * Copyright (c) 2003-2004 DRECOM CO.,LTD. All rights reserved.
 * 
 * info@drecom.co.jp
 * http://www.drecom.co.jp/
 */


//---------------------------------------------------------------------------------
// �ݒ�
//---------------------------------------------------------------------------------

// *** �����ݒ�ϐ� *** //

var gSidefuncOrderForm = null;
var SIDEFUNC_ORDER_FORM_NAME = "SidefuncOrderForm";


// 2004-04-13  Takanori Ishikawa  
// ------------------------------------------------------------------------
//
// Mac �� IE5 �͍��W�֘A�̋@�\�i��������Ȃ����ǁj�̑����������Ă���
// �̂œK���ɑΉ�
//
// �Q�l�F
// Mac �̐H���Ȃ���Y�ǂ�
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
// ���ʎq
var PANEL_NAME_DISABLE	= 'DISABLE';	// �O�@�\BOX��ID
var PANEL_NAME_LEFT		= 'LEFT';		// ���E���@�\BOX��ID
var PANEL_NAME_RIGHT	= 'RIGHT';		// ���E�E�@�\BOX��ID

//���ׂĂ� Panel ���v���p�e�B�ɂ��I�u�W�F�N�g
// key �� name
var gAllPanels         = new Object();

// ������̏���
var gPanelNames         = [PANEL_NAME_DISABLE, PANEL_NAME_LEFT, PANEL_NAME_RIGHT];


// --- Box ---
var BOX_ID_PREFIX 		= 'f';			// Box �� ID �v���t�B�b�N�X
var BoxConfig = {
	width: 		120,	// Box ��
	height: 	20,		// Box ����
	padding: 	10,		// Box �Ԃ̃X�y�[�X
	marginTop: 	5,		// ��ԏ��Box�Ɛe�p�l���Ƃ̃X�y�[�X
	marginLeft: 5		// Box �Ɛe�p�l���̍����̃X�y�[�X
};

// Box �z�u�̈�̍ŏ�����
var MIN_BOXIES_HEIGHT	= 200;

// ���ׂĂ� Box ���v���p�e�B�ɂ��I�u�W�F�N�g
var gAllBoxes           = new Object();

/**
 * �p�l���Ɋi�[�ł���ő� Box �����L�^�����I�u�W�F�N�g
 * 
 * key: �p�l���� value: �p�l���Ɋi�[�ł���ő� Box ��
 * 
 */

BoxConfig.maxNBoxes = new Object();
BoxConfig.maxNBoxes[PANEL_NAME_DISABLE]	= OEMBlogGlobal.PANEL_DISABLE_MAX_NBOXES;	// �u�g�p���Ȃ��@�\�v
BoxConfig.maxNBoxes[PANEL_NAME_LEFT]	= OEMBlogGlobal.PANEL_LEFT_MAX_NBOXES;		// �u�g�p����@�\�v��
BoxConfig.maxNBoxes[PANEL_NAME_RIGHT]	= OEMBlogGlobal.PANEL_RIGHT_MAX_NBOXES;		// �u�g�p����@�\�v�E

/**
 * �y�[�W�ǂݍ��ݎ��̔z�u�f�[�^
 */
gInitialBoxPositionsData = null;

//---------------------------------------------------------
// �z�u�Ȃ�
//---------------------------------------------------------
/**
 * HTML �� hidden input �v�f���獀�ڂ̔z�u�Ȃǂ̏���ǂݎ��I�u�W�F�N�g
 * 
 * @author Takanori Ishikawa
 * @version 2004/04/23
 */
SFSettings = new Object();

/**
 * HTML �� hidden input �v�f����ݒ�l��ǂݎ��
 */
SFSettings.loadBoxPositions = function()
{
	var positions = new Object();
	var orders       = new Array();
	var panelNames   = new Array();	
	
	// hidden����box_position���擾
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
 * ���݂̏�Ԃ�P��̃I�u�W�F�N�g�ɃG���R�[�h���ĕԂ��B
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
 * �T�C�h�o�[�̕ύX���Ď�����I�u�W�F�N�g
 * ContentsChangedListener �̃T�u�N���X
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
	// ������ prototype �̃��\�b�h���Ăяo���Ă����܂������Ȃ��B

	this.changed = (gInitialBoxPositionsData != persistent);
}

//---------------------------------------------------------
// Drag & Drop: SFDragManager (Sidebar Function DragManager)
//---------------------------------------------------------
/**
 * �h���b�O�����̗L�����A�r���̏�Ԃ�ێ����邽�߂̃I�u�W�F�N�g
 * �h���b�O�����͈�x�ɂЂƂ����N����Ȃ��̂ŁA���܂̂Ƃ���
 * Singleton �Ƃ��Ď���
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
SFDragManager.ondrop = null;  // �z�u���ύX���ꂽ


// �h���b�O�����ɗ��p���� document �̃C�x���g�n���h����ݒ�
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

// �h���b�O�����ɗ��p���� document �̃C�x���g�n���h��������
SFDragManager.disbleEventHandler= function()
{
	document.onmousemove	= '';
	document.onmouseup		= '';
}


//�h���b�O���̃I�u�W�F�N�g

SFDragManager.destination = null;	/* �ړ��� Panel */
SFDragManager.source      = null;	/* �ړ��� Panel */
SFDragManager.box         = null;	/* �h���b�O���� Panel */

/**
 * �T�C�h�o�[���ڂ̕ύX���Ď�����I�u�W�F�N�g��Ԃ��B
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
	// �T�C�h�o�[�̔z�u���ύX���ꂽ���ǂ������L�^
	
	var listener = SFDragManager.getChangedListener();
	
	listener.listenEvent(SFDragManager, 'ondrop');
	OEMBlogGlobal.watchOtherLinks(document, listener);
}

/**
  * setOnLoad()
  *
  * �y�[�W�ǂݍ��ݎ��̏�������
  */
SFDragManager.loaded = false;
SFDragManager.onload = function() 
{
	// �t�H�[��������
	gSidefuncOrderForm = UtilKit.getPropertyNotNull(document, SIDEFUNC_ORDER_FORM_NAME);
	
	// �e Panel, Box �I�u�W�F�N�g����
	var boxPositions = SFSettings.loadBoxPositions();
	gInitialBoxPositionsData = SFSettings.getObjectForPersistent();
	for (var i = 0; i < gPanelNames.length; i++) {
		var nm = gPanelNames[i];
		var panel;
		
		panel = new Panel(nm, boxPositions[nm]);
		
		for(var key in panel.box_position) {
			gAllBoxes[key] = new Box(key);
		}
		
		// Box �̏����z�u
		panel.setBoxPosition();
		Panel[nm] = panel;
		
		gAllPanels[nm] = panel;
	}
	
	
	// �h���b�O�����̏���
	SFDragManager.setUpContentsChangedListener();
	SFDragManager.enableEventHandler();
	
	// onload ���C�x���g
	resetPositionsInEventHandler();
	window.onresize = resetPositionsInEventHandler;
	
	SFDragManager.loaded = true;
	// 2004-04-20  Takanori Ishikawa  
	// ------------------------------------------------------------------------
	// Opera �ł� onload ���_�Ń��C���[�̍ĕ`�悪�Ԃɍ���Ȃ��B
	// ���̊֐����g���āA�������ĕ`�悷��B
	if (window.opera) {
		SFDragManager.loaded = false;
		setTimeout("refreshDisplay()", 0.8*1000);
	}
	
}


//---------------------------------------------------------
// �C�x���g����
//---------------------------------------------------------
// CheckLine
/**
 *  �h���b�O���� Box ���}�������ӏ����w���������߂̃��C�� 
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
// Mac IE �ł̓C�x���g�n���h���̒��ō��W�֌W�̑�����s���Ƃ��܂������Ȃ��̂�
// �������x�点�Ď��s
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
  * ���ׂĂ� Box ���Ĕz�u
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
 * ���ׂĂ� Box ����x�����A�����ɂ��ׂĕ\��
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
// �{�b�N�X�̃h���b�O���h���b�v
// -----------------------------------------------------------------

/**
  * pickUpBox()
  * theEvent: �C�x���g
  *
  * Box�������グ��(�N���b�N���ꂽ)���̏���������֐�
  */
SFDragManager.pickUpBox = function(theEvent) {
	if (null == theEvent) {
		theEvent = window.event;
	}
	if (SFDragManager.box != null || SFDragManager.loaded == false)
		return;
	
	// �}�E�X�ʒu�擾
	var mouse_x	    = XBSEvent.getMouseX(theEvent);
	var mouse_y	    = XBSEvent.getMouseY(theEvent);
	var pickedPanel = getPanelAtPosition(mouse_x, mouse_y);

	if (null == pickedPanel) 
		return;

	// �I�����ꂽBox�̕��я��ԍ��� id ���擾
	var beforeId = pickedPanel.orderIndexOfBoxAt(mouse_x, mouse_y);
	var funcId	= UtilKit.getKeyForValue(pickedPanel.box_position, beforeId);
	
	if (null == funcId)
		return;
	
	//Box�G�������g�擾�E����
	var selected = new XBSLayer(BOX_ID_PREFIX + funcId);
	if (null == selected) 
		return;

	selected.setZIndex(selected.getZIndex() +1);

	//�I�����ꂽBox��X,Y���W��ۑ�
	before_box_x	= selected.getX() + MAC_IE5_PITCH_X;
	before_box_y	= selected.getY() + MAC_IE5_PITCH_Y;

	//�}�E�X��Box���_�̍�
	box_to_mouse_x	= mouse_x - before_box_x;
	box_to_mouse_y	= mouse_y - before_box_y;
	
	// �h���b�O�J�n
	SFDragManager.box = selected;
	SFDragManager.source = pickedPanel;
	before_order_id = beforeId;
	function_id = funcId;
}


/**
 * 
 * Box���ړ�����֐�
 * 
 * @param theEvent mousemove �C�x���g
 */
SFDragManager.moveBox = function(theEvent) {
	var selected = SFDragManager.box;
	
	if (null == selected || SFDragManager.loaded == false)
		return;
	if (null == theEvent) {
		theEvent = window.event;
	}
	//
	// �h���b�O���A�E�B���h�E����o�鎞���Z�b�g���ďI���
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
		// �`�F�b�N���C���\��
		curPanel.showCheckLineAtPosition(mouse_x, mouse_y);
	}

	//Box�ړ�
	move_to_x = mouse_x - box_to_mouse_x;
	move_to_y = mouse_y - box_to_mouse_y;
	
	selected.setLeftTopPosition(move_to_x, move_to_y);
}



/**
 * Box �����낵�����̏���
 * 
 * @param theEvent �C�x���g
 */
SFDragManager.downBox = function(theEvent)
{
	if (SFDragManager.loaded == false) {
		return;
	}
	if (null == theEvent) {
		theEvent = window.event;
	}
	// �`�F�b�N���C��������
	getCheckLineLayer().setVisible(false);

	// �}�E�X�ʒu�擾
	var mouse_x = XBSEvent.getMouseX(theEvent);
	var mouse_y = XBSEvent.getMouseY(theEvent);
	
	// -------------------------------------
	// �ړ��̈�����O���`�F�b�N
	// -------------------------------------
	// �ړ���̃p�l�����擾���A�h���b�O�����X�V
	// �h���b�O�O��̃p�l��
	var beforePanel = SFDragManager.source;
	var afterPanel  = getPanelAtPosition(mouse_x, mouse_y);
	
	SFDragManager.destination = afterPanel;
	if (null == beforePanel) {
		throw ASSERT_EXCEPTION + 'SFDragManager.source must be not null';
	}
	
	var droppedBox = gAllBoxes[function_id];
	var after_order_id = afterPanel.orderIndexOfMovingAt(mouse_x, mouse_y);
	
	// �h���b�v�ł��Ȃ��ꍇ�ABox ��߂��āA�h���b�O�������I������
	if (false == afterPanel.canDropAtPosition(beforePanel, droppedBox, mouse_x, mouse_y)) {
		droppedBox.setPosition(before_box_x, before_box_y);
		clearMove();
		return;
	}

	// �ړ�����Box���p�l������폜���A���Ԃ𐮗�����
	delete beforePanel.box_position[function_id];	
	for (key in beforePanel.box_position) {
		if (before_order_id < beforePanel.box_position[key]) {
			--beforePanel.box_position[key];
		}
	}

	//after_order_id�̏���
	if (!(beforePanel.equals(afterPanel) && before_order_id <= after_order_id)) {
		++after_order_id;
	}


	// �ő�̃C���f�b�N�X���X�V
	if (after_order_id > afterPanel.getMaxOrderIndex() + 1) {
		after_order_id = afterPanel.getMaxOrderIndex() + 1;
	}

	//�}������Ԃ�����A�}������
	for (key in afterPanel.box_position) {
		if (after_order_id <= afterPanel.box_position[key]) {
			++afterPanel.box_position[key];
		}
	}
	afterPanel.box_position[function_id] = after_order_id;

	//Box�Ĕz�u
	if (false == beforePanel.equals(afterPanel)) {
		beforePanel.setBoxPosition();
	}
	afterPanel.setBoxPosition();

	// HTML��hidden�l�ύX
	for (var nm in gAllPanels) {
		var p = gAllPanels[nm];

		for (key in p.box_position) {
			gSidefuncOrderForm.elements['order'][key].value = p.box_position[key];
			gSidefuncOrderForm.elements['position'][key].value = p.getPosition();
		}
	}

	// �ړ��̉���
	clearMove();
	if (SFDragManager.ondrop != null && typeof SFDragManager.ondrop == 'function') {
		SFDragManager.ondrop();
	}	
}


/**
  * clearMove()
  *
  * Box�ړ����I������֐�
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
  * �X�̃h���b�O�\�ȍ��ڂ�ێ�����̈�
  * 
  * ���܂̂Ƃ���A������u�g�p���Ȃ��@�\�v�u�g�p����@�\�E���v�u�g�p����@�\�E�E�v
  *
  * @param aPanelName    �p�l���̖��O
  * @param aPositionArray Box�̔z�u���z��
  *
  * @author Yusuke Saito
  * @version 2003/10/10
  * 
  * @author Takanori Ishikawa
  * @version 2004/04/20
  */
function Panel(aPanelName, aPositionArray)
{
	this.name			   = aPanelName;						// �p�l���̖��O(div��id)
	this.box_position	   = aPositionArray;					// Box�̔z�u���z��
	this.box_limit_number  = BoxConfig.maxNBoxes[aPanelName];	// �p�l�����̍ő�Box��
	
	this.position = -1;
	for (var i = 0; i < gPanelNames.length; i++) {
		if (this.name == gPanelNames[i]) {
			this.position = i;
			break;
		}
	}
	
	// �p�l���E�{�b�N�X�E�ړ��̊e�̈��ݒ�
	this.setSections();
}

/**
 * Box �� Y offset
 * 
 * @param anIndex order index
 */
Panel.getBoxSectionOffsetY = function(anIndex)
{
	if (Panel.box_section_y == null) {  // Box �ʒu�ݒ�
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
 * ��r
 * 
 * @param aPanel �p�l��
 */
Panel.prototype.equals = function(aPanel)
{
	// 2004-04-23  Takanori Ishikawa 
	// ------------------------------------------------------------------------
	// instanceof �� IE 6 �ł����T�|�[�g	
	if (aPanel == null || /*false == (aPanel instanceof Panel)*/ typeof aPanel.name == 'undefined') {
		return false;
	}	
	return this.name == aPanel.name;
}


/**
  * setSections()
  * 
  * �E�B���h�E�����_�Ƃ����p�l���̂S�ӂ�X,Y���W��ݒ肷��֐�
  * left:����X right:�E��X top:���Y under:����Y
  */
Panel.prototype.setSections = function() {

	// �e���C���[�̈ʒu�擾
	this.setUpPanelSections();

	//Box�̈�̐ݒ�
	this.setBoxSection();

	//Box�ړ���̈�̐ݒ�
	this.setMoveSection();
}


/**
  * setUpPanelSections()
  * 
  * �E�B���h�E�����_�Ƃ����p�l���̂S�ӂ�X,Y���W��ݒ肷��֐�
  * left:����X right:�E��X top:���Y under:����Y
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
  * Box�̎��܂�̈��ݒ肷��֐�
  * box_section[���я��ԍ�] => (left => ����X, right => �E��X, top => ���Y, under => ����Y)
  */
Panel.prototype.setBoxSection = function() {

	//Box�̈�̐ݒ�
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
 * �p�l���̒��ň�ԉ��� Box �̃C���f�b�N�X��Ԃ�
 * 
 * @return ��ԉ��� Box �̃C���f�b�N�X�B
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
 * �w�肳�ꂽ�ʒu�ɍł��߂��ABox ��}���\�ȉӏ��Ƀ`�F�b�N���C����\������B
 * 
 * @param x x ���W
 * @param y y ���W
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
		// �`�F�b�N���C���ړ�
		var check_line_left = this.panel_section['left'];
		var check_line_top = this.move_section[moveOrderId]['top'];

		// ��U�`�F�b�N���C��������
		getCheckLineLayer().setVisible(false);
		getCheckLineLayer().setLeftTopPosition(check_line_left, check_line_top);
		getCheckLineLayer().setVisible(true);
	}
}


/**
  * setMoveSection()
  * 
  * Box���ړ��ł���̈��ݒ肷��֐�
  * move_section[���я��ԍ�] => (left => ����X, right => �E��X, top => ���Y, under => ����Y)
  */
Panel.prototype.setMoveSection = function() {

	//�ړ��̈�̐ݒ�
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

	// �p�l������O�ꂽ�A
	// �p�l���̍ő� Box ���𒴂��� = false
	return (orderIndex != null && (this.equals(aSourcePanel) || this.box_limit_number > maxIndex));
}

/**
 * �ړ����A�}�������ӏ��̃C���f�b�N�X��Ԃ��B
 * 
 * @param x x ���W
 * @param y y ���W
 * @return �}�������C���f�b�N�X�A�Ȃ���� null
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
  * Box���Ĕz�u����֐�
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
 * �X�̃h���b�O�ňړ��\�ȍ���
 * 
 * @param anId HTML �v�f�� id ����
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
 * HTML �� hidden input �v�f�œn�����v���p�e�B�̖��O
 */
Box.POSITION_PROPERTY_NAME = "position";
Box.ORDER_PROPERTY_NAME    = "order";
Box.NAME_PROPERTY_NAME     = "sidefuncName";
Box.AUTHOR_PROPERTY_NAME   = "authorCd";
Box.ID_PROPERTY_NAME       = "sidefuncId";


/**
 * �h���b�O�����̍Œ��ɌĂ΂��B
 * 
 * @param theEvent mousemove
 * @return �h���b�O�������L�����Z������ꍇ�́Afalse ��Ԃ��B
 */
Box.MOVABLE_INSET = 5;	/* �h�L�������g�̒[ - Box.MOVABLE_INSET = �h���b�O�\�ȗ̈� */
Box.canMoveWithEvent = function(theEvent) 
{
	// ���ΓI�Ȉʒu���ق����̂ŁAXBSDocument.getPageOffsetX/Y() ������
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
  * Box�̑傫����ݒ肷��֐�
  */
Box.prototype.setUpSize = function() 
{
	var imp = this.getHTMLElement();
	
	// style.height �łȂ��� opera �ł��܂������Ȃ�
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
  * Box�̈ʒu��ݒ�
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
 * Box �̉�������ݒ�
 */
Box.prototype.setVisible = function(flag)
{
	var imp = this.getHTMLElement();
	XBSLayer.setVisibilityWithLayerImp(imp, flag ? XBSLayer.VISIBLE : XBSLayer.HIDDEN);
}
