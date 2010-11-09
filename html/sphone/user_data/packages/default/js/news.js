var newsText = new Array();
var newsCnt = 0;
/*------------------------------------------
　基本関数(配列の次のカウントを得る）
------------------------------------------*/
function getNext(ary, cnt) {
    if(++cnt >= ary.length)
        return 0;
    else
        return cnt;
}
/*------------------------------------------
　基本関数(配列の次のカウントを得る）
------------------------------------------*/
function nextNews(){
    newsText[newsCnt].className = "anews";
    newsCnt = getNext(newsText, newsCnt);
    newsText[getNext(newsText, newsCnt)].className = "anews right";
    newsText[newsCnt].className = "anews view";
}

function initNews(){
    newsText = document.getElementsByClassName('anews');
    newsid = setInterval("nextNews()", 10000);
    newsText[getNext(newsText, newsCnt)].className = "anews right";
    newsText[newsCnt].className = "anews view";
}