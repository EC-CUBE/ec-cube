//Bootstrap ツールチップ
var toolTip = function(){
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        console.log("hoeg")
    })
};

toolTip();

//popover ポップオーバー
// header
var popoverHeader = function(){
    $(function () {
        $('.c-headerBar__userMenu').popover({
            container: 'body'
        })
    })
};
popoverHeader();