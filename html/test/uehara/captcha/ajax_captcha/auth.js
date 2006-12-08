RESULT_PAGE_PHP = 'result.php';			// ��̥ڡ���ɽ��PHP
CREATE_IMAGE_PHP = 'create_image.php';	// ��������PHP
RESULT_TEXT_ID = 'result';				// ���ʸ��ɽ�����ID(HTML�����)
CODE_IMG = 'code';						// ������ɽ��IMG������ID

// �֥饦���ˤ�ä�XmlHttpRequest��Object�򿶤�ʬ���� 
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
 		// Mozilla, Safari�ʤ�
		return new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		// IE
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		// ���б�
		alert("�֥饦����XmlHttpRequest���б����Ƥ��ޤ��󡪡�");
	}
}

// ���֥�����������
var receiveReq = getXmlHttpRequestObject();

// �ꥯ�����Ƚ���
function makeRequest(url, param) {
	// ������λ���ޤ�open�᥽�åɤ��ƤӽФ���Ƥ��ʤ�
	if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
		// �����ФȤ��̿��򳫻�
		receiveReq.open("POST", url, true);
		// �����С�����α������ν���������ʷ�̤Υڡ����ؤ�ȿ�ǡ�
		receiveReq.onreadystatechange = updatePage; 

		// �إå������å�
		receiveReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		receiveReq.setRequestHeader("Content-length", param.length);
		receiveReq.setRequestHeader("Connection", "close");

		// ����
		receiveReq.send(param);
	}   
}

// �����С�����α������ν���
function updatePage() {
	// ��������λ���Ƥ�����¹�
	if (receiveReq.readyState == 4) {
		// ���ꤷ��ID����������ʸ����򥻥å�
		document.getElementById(RESULT_TEXT_ID).innerHTML = receiveReq.responseText;
		// �����ɲ������Ѳ�������
		img = document.getElementById(CODE_IMG); 
		// ����å������򤹤뤿��˥�������ͤ�Ĥ���
		img.src = CREATE_IMAGE_PHP + '?' + Math.random();
	}
}

// ǧ�ڽ����¹�
function getParam(forms) {
	var postData = forms.input_data.name + "=" + encodeURIComponent( forms.input_data.value );
	// �ꥯ�����ȼ¹�
	makeRequest(RESULT_PAGE_PHP, postData);
}