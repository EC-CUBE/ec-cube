function setTopButton(topURL) {
    if(!topURL){
        topURL = "/";
    }
    var buttonText = fnT("j_breadcrumbs_001");
    var buttonId = "btn-top";

    //ボタンの生成・設定
    var btn = document.createElement('div');
    var a = document.createElement('a');
    btn.id = buttonId;
    btn.onclick = function(){location=topURL;};
    a.href = topURL;
    a.innerText = buttonText;

    /* 背景色の設定 ---------------------*/
    //最初の見出しの背景色を取得、設定
    var obj = document.getElementsByTagName('h2')[0];
    var col = document.defaultView.getComputedStyle(obj,null).getPropertyValue('background-color');
    btn.style.backgroundColor = col;

    //省略表示用テキストの生成
    var spn = document.createElement('span');
    spn.innerText = obj.innerText;
    obj.innerText = "";
    spn.style.display = "inline-block";
    spn.style.maxWidth = "50%";
    spn.style.overflow = "hidden";
    spn.style.textOverflow = "ellipsis";
    obj.appendChild(spn);

    //ボタンを追加
    btn.appendChild(a);
    document.getElementsByTagName('body')[0].appendChild(btn);;
}
