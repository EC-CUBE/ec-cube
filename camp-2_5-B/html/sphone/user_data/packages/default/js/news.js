var newsText = new Array();
var newsCnt = 0;
/*------------------------------------------
�@��{�֐�(�z��̎��̃J�E���g�𓾂�j
------------------------------------------*/
function getNext(ary, cnt) {
    if(++cnt >= ary.length)
        return 0;
    else
        return cnt;
}
/*------------------------------------------
�@��{�֐�(�z��̎��̃J�E���g�𓾂�j
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