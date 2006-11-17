var IMG_FOLDER_CLOSE   = "./img/folder_close.gif";				// フォルダクローズ時画像パス
var IMG_FOLDER_CLOSE_P = "./img/folder_close_p.gif";			// フォルダクローズ時画像パス(子有り)
var IMG_FOLDER_OPEN    = "./img/folder_open.gif";				// フォルダオープン時画像パス
var IMG_FOLDER_OPEN_M  = "./img/folder_open_m.gif";				// フォルダオープン時画像パス(子有り)

var tree = "";
var parent = "";
var arrTest = new Array();
arrTest[0] = new Array("0", "_parent", "", 0);
arrTest[1] = new Array("1", "_child", "0", 1);
arrTest[2] = new Array("2", "_parent", "0", 1);
arrTest[3] = new Array("3", "_parent", "2", 2);
arrTest[4] = new Array("4", "_child", "3", 3);
arrTest[5] = new Array("5", "_parent", "", 0);
arrTest[6] = new Array("6", "_child", "5", 1);
arrTest[7] = new Array("7", "_child", "6", 1);

for(i = 0; i < arrTest.length; i++) {
	
	id = arrTest[i][0];
	level = arrTest[i][3];
	
	if(i == 0) {
		old_id = "0";
		old_level = 0;
	} else {
		old_id = arrTest[i-1][0];
		old_level = arrTest[i-1][3];
	}

	// 階層上へ戻る
	if(level <= (old_level - 1)) {
		tmp_level = old_level - level;
		for(up_roop = 0; up_roop <= tmp_level; up_roop++) {
			tree += '</div>';
		}
	}
	
	// 同一階層で次のフォルダへ
	if(id != old_id && level == old_level) tree += '</div>';

	// 階層の分だけスペースを入れる
	for(space_cnt = 0; space_cnt < arrTest[i][3]; space_cnt++) {
		tree += "　";
	}
	
	// 初期表示の画像を選択
	if(arrTest[i][1] == '_parent') {
		defalt_img = IMG_FOLDER_CLOSE_P;
	} else {
		defalt_img = IMG_FOLDER_CLOSE;
	}
	
	tree += '<a href="javascript:fnTreeMenu(\'tree'+ i +'\',\''+ arrTest[i][1] +'\',\'tree_img'+   i+'\')"><img src="'+ defalt_img +'" border="0" name="tree_img'+ i +'" ></a><br/>';
	tree += '<div id="tree'+ i +'" style="display:none">';

}

function test() {
	document.form1['test1'].value = tree;
}


// ツリー描画
function fnTreeDrow(id) {
	// ブラウザ取得
	MyBR = fnGetMyBrowser();
	// ブラウザ事に処理を切り分け
	switch(myBR) {
		// IE4の時の表示
		case 'I4':
			document.all(id).innerHTML = this.tree;
			break;
		// NN4の時の表示
		case 'N4':
			document.layers[id].document.open();
			document.layers[id].document.write("<div>");
			document.layers[id].document.write(this.tree);
			document.layers[id].document.write("</div>");
			document.layers[id].document.close();
			break;
		default:
			document.getElementById(id).innerHTML=this.tree;
			break;
	}
}

// 階層ツリーメニュー表示・非表示処理
flag = false;
/*
function treeMenu(tName) {
  tMenu = document.all[tName].style;
  if(tMenu.display == 'none') tMenu.display = "block";
  else tMenu.display = "none";
}
*/
function fnTreeMenu(tName, type, imgName) {

	tMenu = document.all[tName].style;
	if(tMenu.display == 'none') {
		if(type == '_parent') {
			fnChgImg(IMG_FOLDER_OPEN_M, imgName);
		} else {
			fnChgImg(IMG_FOLDER_OPEN, imgName);
		}
		tMenu.display = "block";
	} else {
		if(type == '_parent') {
			fnChgImg(IMG_FOLDER_CLOSE_P, imgName);
		} else {
			fnChgImg(IMG_FOLDER_CLOSE, imgName);
		}
		
		tMenu.display = "none";
	}
}

// 閲覧ブラウザ取得
function fnGetMyBrowser() {
	myOP = window.opera;            // OP
	myN6 = document.getElementById; // N6
	myIE = document.all;            // IE
	myN4 = document.layers;         // N4
	if      (myOP) myBR="O6";       // OP6以上
	else if (myIE) myBR="I4";       // IE4以上
	else if (myN6) myBR="N6";       // NS6以上
	else if (myN4) myBR="N4";       // NN4
	else           myBR="";         // その他
		
	return myBR;
}

// imgタグの画像変更
function fnChgImg(fileName,imgName){
	document.images[imgName].src = fileName;
}