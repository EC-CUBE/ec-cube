/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
var obj;
var offsetX;
var offsetY;
var arrObj;
var objParam;

// �ѥ�᡼���������饹�����
function SC_Param() {
	this.ITEM_MAX = 3;		
}

// �������������饹�����
function SC_Size() {
	this.id = '';
	this.left = 0;
	this.top = 0;
	this.width = 0;
	this.height = 0;
	this.obj;
};

// ������ɽ���
onload=function () {
	// �ѥ�᡼���ν����
	objParam = new SC_Param();
	
	// WIN-IE
	if (document.all) {
		objlist = document.all.tags("div");
	// WIN-NN,WIN-FF   
	} else if (document.getElementsByTagName) {
		objlist = document.getElementsByTagName("div");
	} else {
		return;
	}
	
	arrObj = new Array();
	for (i = 0; i < objlist.length; i++) {
		id = objlist[i].id;
		arrObj[id] = new SC_Size();
		arrObj[id].id = id;
		arrObj[id].obj = objlist[id];
		arrObj[id].left = objlist[id].style.left;
		arrObj[id].top = objlist[id].style.top;
		arrObj[id].width = objlist[id].style.width;
		arrObj[id].height = objlist[id].style.height;
		arrObj[id].left = Number(arrObj[id].left.replace(/px/, ''));
		arrObj[id].top = Number(arrObj[id].top.replace(/px/, ''));
		arrObj[id].width = Number(arrObj[id].width.replace(/px/, ''));
		arrObj[id].height = Number(arrObj[id].height.replace(/px/, ''));
		arrObj[id].right = Number(arrObj[id].left) + Number(arrObj[id].width);
		arrObj[id].bottom =Number(arrObj[id].top) + Number(arrObj[id].height);
	}
	
	// MouseDown���٥�Ƚ����������ؤ�
	objlist['item0'].onmousedown = onMouseDown;
	objlist['item1'].onmousedown = onMouseDown;
	objlist['item2'].onmousedown = onMouseDown;
	
	document.onmousemove = onMouseMove;
	document.onmouseup = onMouseUp;
}

// MouseDown���٥��
function onMouseDown(e) {
   obj = this;
   // WIN-IE
   if (document.all) {
      offsetX = event.offsetX + 2;
      offsetY = event.offsetY + 2;
   // WIN-NN,WIN-FF
   } else if (obj.getElementsByTagName) {
      offsetX = e.pageX - parseInt(obj.style.left);
      offsetY = e.pageY - parseInt(obj.style.top);
   }
   return false;
}

// MouseMove���٥��
function onMouseMove(e) {
	if (!obj) {
		return true;
	}	
	// WIN-IE
	if (document.all) {
		x = event.clientX - offsetX;
		// ���̳��˽Фʤ��褦�����椹�롡
		if(x <= 0) {
			x = 0;
		}
		left_max = document.body.clientWidth - arrObj[obj.id].width;
		if(x >= left_max) {
			x =left_max;			
		}
		obj.style.left = x;
		// ���̳��˽Фʤ��褦�����椹�롡
		y = event.clientY - offsetY;
		if(y <= 0) {
			y = 0;
		}
		top_max = document.body.clientHeight - arrObj[obj.id].height;
		if(y >= top_max) {
			y =top_max;			
		}
		obj.style.top = y;		
	// WIN-NN,WIN-FF
	} else if (obj.getElementsByTagName) {
		x = e.pageX - offsetX;
		// ���̳��˽Фʤ��褦�����椹�롡
		if(x <= 0) {
			x = 0;
		}
		left_max = window.innerWidth - arrObj[obj.id].width;
		if(x >= left_max) {
			x =left_max;			
		}
		obj.style.left = x;
		
		y = e.pageY - offsetY;
		// ���̳��˽Фʤ��褦�����椹�롡
		if(y <= 0) {
			y = 0;
			obj.style.top = 0;
		}
		top_max = window.innerHeight - arrObj[obj.id].height;
		if(y >= top_max) {
			y =top_max;			
		}
		obj.style.top = y;
	}
	
	if(isInFlame('flame0', obj)) {
		document.getElementById('td1').style.backgroundColor = '#fffadd';
	} else {
		document.getElementById('td1').style.backgroundColor = '#ffffff';
	}
	return false;
}

// MouseUp���٥��
function onMouseUp(e) {
	if (!obj) {
		return true;
	}
	
	if(!isInFlame('flame0', obj)) {
		// WIN-IE
		if (document.all) {
			// �ǽ�ΰ��֤��᤹
			obj.style.left = arrObj[obj.id].left;
			obj.style.top = arrObj[obj.id].top;
		// WIN-NN,WIN-FF
		} else if (obj.getElementsByTagName) {
			// �ǽ�ΰ��֤��᤹
			obj.style.left = arrObj[obj.id].left;
			obj.style.top = arrObj[obj.id].top;
		}
	}	
	document.getElementById('td1').style.backgroundColor = '#ffffff';	
	obj = null;
}

// �ե졼����˥����ƥब¸�ߤ��뤫Ƚ�ꤹ�롡
function isInFlame(flame_id, item) {
	top_val = item.style.top;
	top_val = Number(top_val.replace(/px/, ''));
	bottom_val = top_val + arrObj[item.id].height;
	left_val = item.style.left;
	left_val = Number(left_val.replace(/px/, ''))
	right_val = left_val + arrObj[item.id].width;		
	if(
		top_val > arrObj[flame_id].top &&
		bottom_val < arrObj[flame_id].bottom &&
		left_val > arrObj[flame_id].left &&
		right_val < arrObj[flame_id].right
		) {
		return true;
	}
	return false;
}

// �������ν���
function preSubmit() {
	for(i = 0; i < 3; i++) {
		id = 'item' + i;
		obj = arrObj[id].obj;
		if(isInFlame ('flame0', obj)) {
			document.form1[obj.id].value = "in";
		} else {
			document.form1[obj.id].value = "out";
		}
	}
}



