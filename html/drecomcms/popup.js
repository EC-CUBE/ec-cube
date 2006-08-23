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
// div �̑傫�����ς̂��߁AXBSLayer.LEFT_TOP �����w��ł��܂���
var POPUP_POSITION = XBSLayer.LEFT_TOP; // �|�b�v�A�b�v�̍��オ�}�E�X�ʒu

var gPopUpLayer = null;


/**
 * �|�b�v�A�b�v��\������֐�
 * 
 * @param theEvent �C�x���g�I�u�W�F�N�g�i�K�{�j
 * @param text �|�b�v�A�b�v�ɕ\��������e�i�K�{�j
 *
 * @param offsetX X�����ɂ��̋��������C�x���g�̈ʒu���痣���i�I�v�V�����j
 * @param offsetY Y�����ɂ��̋��������C�x���g�̈ʒu���痣���i�I�v�V�����j
 * @param axisType �|�b�v�A�b�v�̈�̒��_�̂����A�ǂ����ɕ\�����邩�B�i�I�v�V�����j
 * <ul>
 * <li>XBSLayer.LEFT_TOP     ���㒸�_���</li>
 * <li>XBSLayer.LEFT_BOTTOM  �������_���</li>
 * <li>XBSLayer.RIGHT_TOP    �E�㒸�_���</li>
 * <li>XBSLayer.RIGHT_BOTTOM �E�����_���</li>
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
  * ���C���[�Ń|�b�v�A�b�v��\������֐�
  */
function hidePopup(e) 
{
	if (gPopUpLayer != null) {
		gPopUpLayer.setVisible(false);
	}
}
// �|�b�v�A�b�v�p html ���o�͂��Ă���
document.write(POPUP_HTML);