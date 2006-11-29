var IMG_FOLDER_CLOSE   = "./img/folder_close.gif";				// �ե����������������
var IMG_FOLDER_OPEN    = "./img/folder_open.gif";				// �ե���������ץ������
var IMG_PLUS           = "./img/plus.gif";						// �ץ饹�饤��
var IMG_MINUS          = "./img/minus.gif";						// �ޥ��ʥ��饤��
var IMG_NORMAL         = "./img/normal.gif";					// �Ρ��ޥ�饤��

var tree = "";						// ����HTML��Ǽ
var count = 0;						// �롼�ץ�����
var arrTreeStatus = new Array();	// �ĥ꡼�����ݻ�
var old_select_id = '';				// �������򤷤Ƥ����ե�����

// �ĥ꡼ɽ��
function fnTreeView(view_id, arrTree, openFolder) {

	//tree += '<form name="tree_form">';
	for(i = 0; i < arrTree.length; i++) {
		
		id = arrTree[i][0];
		level = arrTree[i][3];
		
		if(i == 0) {
			old_id = "0";
			old_level = 0;
		} else {
			old_id = arrTree[i-1][0];
			old_level = arrTree[i-1][3];
		}
	
		// ���ؾ�����
		if(level <= (old_level - 1)) {
			tmp_level = old_level - level;
			for(up_roop = 0; up_roop <= tmp_level; up_roop++) {
				tree += '</div>';
			}
		}
		
		// Ʊ�쳬�ؤǼ��Υե������
		if(id != old_id && level == old_level) tree += '</div>';
	
		// ���ؤ�ʬ�������ڡ����������
		for(space_cnt = 0; space_cnt < arrTree[i][3]; space_cnt++) {
			tree += "&nbsp;&nbsp;&nbsp;";
		}

		// ���ز�����ɽ������ɽ������
		if(arrTree[i][4]) {
			if(arrTree[i][1] == '_parent') {
				rank_img = IMG_MINUS;
			} else {
				rank_img = IMG_NORMAL;
			}
			// �������֤��ݻ�
			arrTreeStatus.push(arrTree[i][2]);
			display = 'block';
		} else {
			if(arrTree[i][1] == '_parent') {
				rank_img = IMG_PLUS;
			} else {
				rank_img = IMG_NORMAL;
			}
			display = 'none';
		}
		
		// �ե�����β���������
		if(arrTree[i][2] == openFolder) {
			folder_img = IMG_FOLDER_OPEN;
		} else {
			folder_img = IMG_FOLDER_CLOSE;
		}

		arrFileSplit = arrTree[i][2].split("/");
		file_name = arrFileSplit[arrFileSplit.length-1];

		// ���ز������Ρ��ޥ�λ��Τߥ��󥯥�å�������Ĥ���
		if(rank_img != IMG_NORMAL) {
			tree += '<input type="image" src="'+ rank_img +'" border="0" name="rank_img'+ i +'" id="rank_img'+ i +'" onclick="fnTreeMenu(\'tree'+ i +'\',\'rank_img'+ i +'\',\''+ arrTree[i][2] +'\')">';
		} else {
			tree += '<img src="'+ rank_img +'" border="0" name="rank_img'+ i +'" id="rank_img'+ i +'">';
		}
		tree += '<input type="image" src="'+ folder_img +'" border="0" name="tree_img'+ i +'" id="tree_img'+ i +'" onclick="fnFolderOpen(\''+ arrTree[i][2] +'\')">&nbsp;'+ file_name +'<br/>';
		tree += '<div id="tree'+ i +'" style="display:'+ display +'">';
	
	}
	//tree += '</form>';
	fnDrow(view_id, tree);
	//document.tree_form.tree_test2.focus();	
}

// Tree���֤�hidden�˥��å�
function setTreeStatus(name) {
	var tree_status = "";
	for(i=0; i < arrTreeStatus.length ;i++) {
		if(i != 0) tree_status += '|';
		tree_status += arrTreeStatus[i];
	}
	document.form1[name].value = tree_status;
}

// Tree���֤�������(�Ĥ�����֤�)
function fnDelTreeStatus(path) {
	for(i=0; i < arrTreeStatus.length ;i++) {
		if(arrTreeStatus[i] == path) {
			arrTreeStatus[i] = "";
		}
	}
}
// �ĥ꡼����
function fnDrow(id, tree) {
	// �֥饦������
	MyBR = fnGetMyBrowser();
	// �֥饦�����˽������ڤ�ʬ��
	switch(myBR) {
		// IE4�λ���ɽ��
		case 'I4':
			document.all(id).innerHTML = tree;
			break;
		// NN4�λ���ɽ��
		case 'N4':
			document.layers[id].document.open();
			document.layers[id].document.write("<div>");
			document.layers[id].document.write(tree);
			document.layers[id].document.write("</div>");
			document.layers[id].document.close();
			break;
		default:
			document.getElementById(id).innerHTML=tree;
			break;
	}
}

// ���إĥ꡼��˥塼ɽ������ɽ������
function fnTreeMenu(tName, imgName, path) {

	tMenu = document.all[tName].style;

	if(tMenu.display == 'none') {
		fnChgImg(IMG_MINUS, imgName);
		tMenu.display = "block";
		// ���ؤγ��������֤��ݻ�
		arrTreeStatus.push(path);

	} else {
		fnChgImg(IMG_PLUS, imgName);
		tMenu.display = "none";
		// �Ĥ����֤��ݻ�
		fnDelTreeStatus(path);
	}
}

// �ե���������ץ����
function fnFolderOpen(path) {

	// ����å������ե����������ݻ�
	document.form1['tree_select_file'].value = path;
	// tree�ξ��֤򥻥å�
	setTreeStatus('tree_status');
	// submit
	fnModeSubmit('move','','');
}


// �����֥饦������
function fnGetMyBrowser() {
	myOP = window.opera;            // OP
	myN6 = document.getElementById; // N6
	myIE = document.all;            // IE
	myN4 = document.layers;         // N4
	if      (myOP) myBR="O6";       // OP6�ʾ�
	else if (myIE) myBR="I4";       // IE4�ʾ�
	else if (myN6) myBR="N6";       // NS6�ʾ�
	else if (myN4) myBR="N4";       // NN4
	else           myBR="";         // ����¾
		
	return myBR;
}

// img�����β����ѹ�
function fnChgImg(fileName,imgName){
	document.getElementById(imgName).src = fileName;
}

// �ե���������
function fnSelectFile(id, val) {
	if(old_select_id != '') document.getElementById(old_select_id).style.backgroundColor = '';
	document.getElementById(id).style.backgroundColor = val;
	old_select_id = id;
}

// �طʿ����Ѥ���
function fnChangeBgColor(id, val) {
	if (old_select_id != id) {
		document.getElementById(id).style.backgroundColor = val;
	}
}