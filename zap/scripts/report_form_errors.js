// シナリオの再生で、入力エラーの画面が返ったリクエストをログに出す (httpsender スクリプト)。
//
// ZAP は再生の成否を HTTP の状態コードで判定するため、入力エラーで同じ画面を 200 で返すと成功として扱う。
// その場合、後続の画面 (確認画面など) に能動スキャンの攻撃が届かない。
// 再生も攻撃と同じく能動スキャン (initiator が ACTIVE_SCANNER) として送られるため区別できない。
// 攻撃を送らない Smoke でだけ組み込む (generate_automation_config.sh)。

// フロントは form_div_layout.twig、管理画面は bootstrap_4_layout.html.twig の form_errors
var ERROR_PATTERN = /class="(?:ec-errorMessage|form-error-message)">([^<]*)/;

function sendingRequest(msg, initiator, helper) {}

function responseReceived(msg, initiator, helper) {
    if (msg.getRequestHeader().getMethod() != "POST") {
        return;
    }
    var m = ERROR_PATTERN.exec(msg.getResponseBody().toString());
    if (m) {
        print("Form error: " + msg.getRequestHeader().getURI() + " " + m[1].trim());
    }
}
