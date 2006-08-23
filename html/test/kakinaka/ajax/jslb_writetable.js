//====================================================================
// �ơ��֥�����ѥ饤�֥�� jslb_writetable.js
//
// �ǿ����� http://jsgt.org/mt/archives/01/000414.html 
// �嵭�����Ⱥ���Բġ��������ѡ���¤����ͳ��Ϣ�����פǤ���
// 

////
// �ơ��֥��񤭽Ф��ޤ�
// @param  tableId       �оݥơ��֥��񤭽Ф�DIV��ID̾
// @param  dataAry       �ǡ��� �󼡸�������Ϥ��ޤ�
// @sample               writeTable('tdiv',[['̾��','data'],['����','12']])
//
function writeTable(tableId,dataAry)
{
	//����������в�����¹�
	if(!!writeTable.arguments[0]){
		removeTable(tableId)       ; //�ơ��֥���
		mkTable(tableId,dataAry)   ; //�ơ��֥�����
		mkGraph(tableId)           ; //���������
	}
}

////
// �ơ��֥�򥽡��Ȥ��ƽ񤭽Ф��ޤ�
// @param  tableId       �оݥơ��֥��񤭽Ф�DIV��ID̾
// @param  dataAry       �ǡ���
// @param  sortFunc      ���ͥ����ȴؿ�̾ ����sortA|�߽�sortD
// @sample               reWriteTable('tdiv',[['̾��','data'],['a',8],['b',3]],sortD)
//
function reWriteTable(tableId,dataAry,sortFunc)
{
	sortwk(dataAry,sortFunc)
	writeTable(tableId,dataAry)
}


////
// �оݥơ��֥���
// @param  tableId       �оݥơ��֥��񤭽Ф�DIV��ID̾
//
function removeTable(tableId){
	document.getElementById(tableId).innerHTML=''
}

////
// ������ɲ�
// @param  tableId       �оݥơ��֥��񤭽Ф�DIV��ID̾
//
function mkGraph(tableId)
{
	var i,td,img                                  ; // �������ѿ�
	var mydoc	= document                        ; // document���֥�������
	var table	= mydoc.getElementById(tableId)   ; // �оݥơ��֥�
	var trs		= table.getElementsByTagName('TR'); // �оݥơ��֥벼��TR����

	// TR��1�Ԥ��Ľ���
	for( i = 1 ; i < trs.length ; i++)
	{
		//������ѥǡ��������Υ��뤫�����
		forGraphData = trs.item(i).childNodes.item(1).firstChild.nodeValue
		//TD�Ȳ���������
		td	 = mydoc.createElement("TD")
		img	= mydoc.createElement("IMG")
		img.setAttribute('src','./bar1.gif')
		img.setAttribute('height', 20 )
		img.setAttribute('width', forGraphData )
		//�������TD�Ȳ���������
		trs.item(i).insertBefore(td, null).insertBefore(img, null)
	}

}

////
// �ơ��֥�����
// @param  tableId       �оݥơ��֥��񤭽Ф�DIV��ID̾
// @param  dataAry     �ǡ���
//
function mkTable(tableId,dataAry) 
{
	if(!dataAry)return 
	var table, tbody, tr, td, text, i ,j          ; // �������ѿ�
	var row = dataAry.length                    ; // �ơ��֥�ǡ����Կ�
	var col = dataAry[0].length                 ; // �ơ��֥�ǡ������
	var mydoc = document                          ; // document���֥�������

	//table��tbody���Ǥ�����
	table = mydoc.createElement("TABLE")
	tbody = mydoc.createElement("TBODY")

	//table��tbody���Ǥ�����������˽�����DIV������
	table.insertBefore(tbody, null)
	document.getElementById(tableId).insertBefore(table, null)

	//�Ԥν���
	for (i=0; i<row; i++) {
		tr	 = mydoc.createElement("TR")
		tbody.insertBefore(tr, null)

		//��ν���
		for (j=0; j<col; j++) {
			td	 = mydoc.createElement("TD")
			text = mydoc.createTextNode(dataAry[i][j])
			tr.insertBefore(td, null)
			td.insertBefore(text, null)

			//���Ф�����(1���ܤ�1����)�˴ؤ���CSS��class̾������
			var className=(typeof ScriptEngine=='function')?'className':'class';
			// 1����
			if(j==0)td.setAttribute(className,'col0')
			// 2���� (����)
			if(j==1)td.setAttribute(className,'col1')
			// 1����
			if(i==0)td.setAttribute(className,'row0')
		}
	}

	return table
}

//====================================================================
// �¤��ؤ�
//

////
// �¤��ؤ�
// @param  dataAry       �¤��ؤ��о�����
// @param  sortFunc      ���ͥ����ȴؿ�̾ ����sortA|�߽�sortD
//
function sortwk(dataAry,sortFunc)
{
	if(!dataAry)return 
	var head = dataAry[0] ;
	dataAry.shift()
	dataAry.sort(sortFunc)
	dataAry.unshift(head)
	return dataAry
}

//���ͥ����Ⱦ���
function sortA(a,b){ return a[1] - b[1] }
//���ͥ����ȹ߽�
function sortD(a,b){ return b[1] - a[1] }
