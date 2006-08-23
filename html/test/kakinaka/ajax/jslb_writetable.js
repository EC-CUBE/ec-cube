//====================================================================
// テーブル処理用ライブラリ jslb_writetable.js
//
// 最新情報 http://jsgt.org/mt/archives/01/000414.html 
// 上記コメント削除不可。商用利用、改造、自由。連絡不要です。
// 

////
// テーブルを書き出します
// @param  tableId       対象テーブルを書き出すDIVのID名
// @param  dataAry       データ 二次元配列で渡します
// @sample               writeTable('tdiv',[['名前','data'],['山田','12']])
//
function writeTable(tableId,dataAry)
{
	//引数があれば下記を実行
	if(!!writeTable.arguments[0]){
		removeTable(tableId)       ; //テーブル削除
		mkTable(tableId,dataAry)   ; //テーブル生成
		mkGraph(tableId)           ; //グラフ生成
	}
}

////
// テーブルをソートして書き出します
// @param  tableId       対象テーブルを書き出すDIVのID名
// @param  dataAry       データ
// @param  sortFunc      数値ソート関数名 昇順sortA|降順sortD
// @sample               reWriteTable('tdiv',[['名前','data'],['a',8],['b',3]],sortD)
//
function reWriteTable(tableId,dataAry,sortFunc)
{
	sortwk(dataAry,sortFunc)
	writeTable(tableId,dataAry)
}


////
// 対象テーブル削除
// @param  tableId       対象テーブルを書き出すDIVのID名
//
function removeTable(tableId){
	document.getElementById(tableId).innerHTML=''
}

////
// グラフ追加
// @param  tableId       対象テーブルを書き出すDIVのID名
//
function mkGraph(tableId)
{
	var i,td,img                                  ; // ローカル変数
	var mydoc	= document                        ; // documentオブジェクト
	var table	= mydoc.getElementById(tableId)   ; // 対象テーブル
	var trs		= table.getElementsByTagName('TR'); // 対象テーブル下のTR配列

	// TRを1行ずつ処理
	for( i = 1 ; i < trs.length ; i++)
	{
		//グラフ用データを前のセルから取得
		forGraphData = trs.item(i).childNodes.item(1).firstChild.nodeValue
		//TDと画像を生成
		td	 = mydoc.createElement("TD")
		img	= mydoc.createElement("IMG")
		img.setAttribute('src','./bar1.gif')
		img.setAttribute('height', 20 )
		img.setAttribute('width', forGraphData )
		//グラフ用TDと画像を挿入
		trs.item(i).insertBefore(td, null).insertBefore(img, null)
	}

}

////
// テーブル生成
// @param  tableId       対象テーブルを書き出すDIVのID名
// @param  dataAry     データ
//
function mkTable(tableId,dataAry) 
{
	if(!dataAry)return 
	var table, tbody, tr, td, text, i ,j          ; // ローカル変数
	var row = dataAry.length                    ; // テーブルデータ行数
	var col = dataAry[0].length                 ; // テーブルデータ列数
	var mydoc = document                          ; // documentオブジェクト

	//tableとtbody要素を生成
	table = mydoc.createElement("TABLE")
	tbody = mydoc.createElement("TBODY")

	//tableへtbody要素を挿入しさらに出力用DIVへ挿入
	table.insertBefore(tbody, null)
	document.getElementById(tableId).insertBefore(table, null)

	//行の処理
	for (i=0; i<row; i++) {
		tr	 = mydoc.createElement("TR")
		tbody.insertBefore(tr, null)

		//列の処理
		for (j=0; j<col; j++) {
			td	 = mydoc.createElement("TD")
			text = mydoc.createTextNode(dataAry[i][j])
			tr.insertBefore(td, null)
			td.insertBefore(text, null)

			//見出しセル(1列目と1行目)に関するCSS用class名を設定
			var className=(typeof ScriptEngine=='function')?'className':'class';
			// 1列目
			if(j==0)td.setAttribute(className,'col0')
			// 2列目 (成績)
			if(j==1)td.setAttribute(className,'col1')
			// 1行目
			if(i==0)td.setAttribute(className,'row0')
		}
	}

	return table
}

//====================================================================
// 並べ替え
//

////
// 並べ替え
// @param  dataAry       並べ替え対象配列
// @param  sortFunc      数値ソート関数名 昇順sortA|降順sortD
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

//数値ソート昇順
function sortA(a,b){ return a[1] - b[1] }
//数値ソート降順
function sortD(a,b){ return b[1] - a[1] }
