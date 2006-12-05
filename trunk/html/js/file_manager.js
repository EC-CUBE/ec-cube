/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
var IMG_FOLDER_CLOSE   = "../../img/admin/contents/folder_close.gif";		// �ե����������������
var IMG_FOLDER_OPEN    = "../../img/admin/contents/folder_open.gif";		// �ե���������ץ������
var IMG_PLUS           = "../../img/admin/contents/plus.gif";				// �ץ饹�饤��
var IMG_MINUS          = "../../img/admin/contents/minus.gif";				// �ޥ��ʥ��饤��
var IMG_NORMAL         = "../../img/admin/contents/space.gif";				// ���ڡ���

var tree = "";						// ����HTML��Ǽ
var count = 0;						// �롼�ץ�����
var arrTreeStatus = new Array();	// �ĥ꡼�����ݻ�
var old_select_id = '';				// �������򤷤Ƥ����ե�����
var selectFileHidden = "";			// ���򤷤��ե������hidden̾
var treeStatusHidden = "";			// �ĥ꡼������¸�Ѥ�hidden̾
var modeHidden = "";				// mode���å�hidden̾

// �ĥ꡼ɽ��
function fnTreeView(view_id, arrTree, openFolder, selectHidden, treeHidden, mode) {
	selectFileHidden = selectHidden;
	treeStatusHidden = treeHidden;
	modeHidden = mode;
	
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

		arrFileSplit = arrTree[i][2].split("/");
		file_name = arrFileSplit[arrFileSplit.length-1];

		// �ե�����β���������
		if(arrTree[i][2] == openFolder) {
			folder_img = IMG_FOLDER_OPEN;
			file_name = "<b>" + file_name + "</b>";
		} else {
			folder_img = IMG_FOLDER_CLOSE;
		}

		// ���ز����˻Ҷ��������饪�󥯥�å�������Ĥ���
		if(rank_img != IMG_NORMAL) {
			tree += '<a href="javascript:fnTreeMenu(\'tree'+ i +'\',\'rank_img'+ i +'\',\''+ arrTree[i][2] +'\')"><img src="'+ rank_img +'" border="0" name="rank_img'+ i +'" id="rank_img'+ i +'">';
		} else {
			tree += '<img src="'+ rank_img +'" border="0" name="rank_img'+ i +'" id="rank_img'+ i +'">';
		}
		tree += '<a href="javascript:fnFolderOpen(\''+ arrTree[i][2] +'\')"><img src="'+ folder_img +'" border="0" name="tree_img'+ i +'" id="tree_img'+ i +'">&nbsp;'+ file_name +'</a><br/>';
		tree += '<div id="tree'+ i +'" style="display:'+ display +'">';
	
	}
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

// �ե�����ꥹ�ȥ��֥륯��å�����
function fnDbClick(arrTree, path, is_dir, now_dir, is_parent) {

	if(is_dir) {
		if(!is_parent) {
			for(cnt = 0; cnt < arrTree.length; cnt++) {
				if(now_dir == arrTree[cnt][2]) {
					open_flag = false;
					for(status_cnt = 0; status_cnt < arrTreeStatus.length; status_cnt++) {
						if(arrTreeStatus[status_cnt] == arrTree[cnt][2]) open_flag = true;
					}
					if(!open_flag) fnTreeMenu('tree'+cnt, 'rank_img'+cnt, arrTree[cnt][2]);
				}
			}
		}
		fnFolderOpen(path);
	} else {
		// Download
		fnModeSubmit('download','','');
	}
}

// �ե���������ץ����
function fnFolderOpen(path) {

	// ����å������ե����������ݻ�
	document.form1[selectFileHidden].value = path;
	// tree�ξ��֤򥻥å�
	setTreeStatus(treeStatusHidden);
	// submit
	fnModeSubmit(modeHidden,'','');
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

// test
function view_test(id) {
	document.getElementById(id).value=tree
}