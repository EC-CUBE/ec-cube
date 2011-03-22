/*------------------------------------------
指定されたノードを、オープンまたはクローズする
------------------------------------------*/
function openOrClose(tgt){
    //指定ノードが「hidden」のとき、リストを可視化
    if(tgt.style.visibility == "hidden"){
        tgt.style.display = "block";
        tgt.style.visibility = "visible";
        tgt.style.height = "auto";
        tgt.style.paddingTop = "0.25em";
        tgt.style.lineHeight = "1em";
        tgt.style.opacity = 1.0;
    }
    //指定ノードが「hidden」でないとき、リストを隠す
    else{
        tgt.style.display = "none";
        tgt.style.visibility = "hidden";
        tgt.style.height = "0px";
        tgt.style.paddingTop = "0";
        tgt.style.lineHeight = 0;
        tgt.style.opacity = 0;
    }
}

/*------------------------------------------
リストープン処理
------------------------------------------*/
function listopen(lv, num){
    var tgt = document.getElementsByClassName("level" + lv)[num];
    var cnt = 0;
    //次のレベルをサーチ → 次のレベルのリストをオープンする
    for(var i=0; i < document.getElementsByClassName("level" + (lv+1)).length; i++){
        var next_tgt = document.getElementsByClassName("level" + (lv+1))[i];
        //li
        if (next_tgt.parentNode == tgt || next_tgt.parentNode.parentNode == tgt){
            openOrClose(next_tgt);
            cnt++;
        }
    }
    //次のレベルをサーチ → 表示ボタンの状態を変更
    for(var i=0; i < tgt.childNodes.length; i++){
        var next_tgt = tgt.childNodes[i];
        //ul
        if(next_tgt.tagName == "UL"){
        if(next_tgt.style.height == "0px"){
            //for(var i=0; i<next_tgt.parentNode.childNodes.length; i++){
                if(event.srcElement.parentNode.className == "category_header plus"){
                    event.srcElement.innerText = '−';
                    event.srcElement.parentNode.className = "category_header minus";
                }
            //}
            next_tgt.style.height = "auto"; //1.5*cnt + "em";
            next_tgt.style.marginTop = 0;
            next_tgt.style.marginBottom = 0;
        }
        else{
            for(var i=0; i<next_tgt.parentNode.childNodes.length; i++){
                if(event.srcElement.parentNode.className == "category_header minus"){
                    event.srcElement.innerText = '＋';
                    event.srcElement.parentNode.className = "category_header plus";
                }
            }
            next_tgt.style.height = "0px";
            next_tgt.style.marginTop = 0;
            next_tgt.style.marginBottom = 0;
        }
        }
    }
}

/*------------------------------------------
クリックイベントを設定する
------------------------------------------*/
function setclickevent(tgt, lv, num){
    //レベル１以外の時は非表示に
    if(lv!=1){
        tgt.style.visibility = "hidden";
        tgt.style.display = "none";
        tgt.style.height = "0px";
        tgt.style.lineHeight = 0;
        tgt.style.paddingTop = 0;
        tgt.style.paddingBottom = 0;
        if(tgt.parentNode.tagName == "UL"){
            tgt.parentNode.style.height = "0px";
            tgt.parentNode.style.margin = "0";
            tgt.parentNode.style.padding = "0";
            tgt.parentNode.style.border = "none";
        }
    }
    var hasLink_flg;
    if(tgt.childNodes.length){
        for (var j = 0; j < tgt.childNodes.length; j++) {
            //クリック範囲の拡大
            if(tgt.childNodes[j].tagName == 'A'){
                tgt.setAttribute('onclick', 'location.href="' + tgt.childNodes[j].getAttribute('href') + '"');
            }
            //アコーディオンリストの操作イベント関数を追加
            else if(tgt.childNodes[j].tagName == 'UL'){
                //▶を表示し、リストオープン関数を追加
                var linkObj = document.createElement("a");
                linkObj.innerText = '＋';
                tgt.childNodes[0].className="category_header plus";
                tgt.childNodes[0].appendChild(linkObj);
                j++;
                linkObj.parentNode.setAttribute('onclick', 'listopen(' + lv + ',' + num + ')');

                //tgt.setAttribute('onclick', 'listopen(' + lv + ',' + num + ')');
                break;
            }
        }
    }
}

/*------------------------------------------
初期化
------------------------------------------*/
//level?クラスを持つノード全てを走査し初期化
function initCategoryList(){
    var lv = 0;
    //level?クラスを持つノード全てに、クリックイベントを追加
    while(document.getElementsByClassName("level" + (++lv)).length){
        for (var i = 0; i < document.getElementsByClassName("level" + lv).length; i++) {
            setclickevent(document.getElementsByClassName("level" + lv)[i], lv, i);
        }
    }
}
