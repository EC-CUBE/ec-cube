// シナリオの再生で、入力エラーの画面が返ったリクエストをログに出す (httpsender スクリプト)。
//
// ZAP は再生の成否を HTTP の状態コードで判定するため、入力エラーで同じ画面を 200 で返すと成功として扱う。
// その場合、後続の画面 (確認画面など) に能動スキャンの攻撃が届かない。
// 再生も攻撃と同じく能動スキャン (initiator が ACTIVE_SCANNER) として送られるため区別できない。
// 攻撃を送らない Smoke でだけ組み込む (generate_automation_config.sh)。

// フロントは form_div_layout.twig、管理画面は bootstrap_4_layout.html.twig の form_errors。
// 管理画面の一部 (商品・支払方法の登録) には JavaScript で使う空の form-error-message があり、
// 住所の登録件数の上限は <span class="ec-errorMessage"> で出るため、中身のあるものに限る
var ERROR_PATTERN = /<p class="ec-errorMessage">\s*([^<\s][^<]*)|<span class="form-error-message">\s*([^<\s][^<]*)/;

function sendingRequest(msg, initiator, helper) {}

function responseReceived(msg, initiator, helper) {
    // 入力エラーを意図したリクエスト (シナリオで X-ZAP-Expect-Form-Error を付ける) は対象にしない
    if (msg.getRequestHeader().getMethod() != "POST" || msg.getRequestHeader().getHeader("X-ZAP-Expect-Form-Error") != null) {
        return;
    }
    var m = ERROR_PATTERN.exec(msg.getResponseBody().toString());
    if (m) {
        print("Form error: " + msg.getRequestHeader().getURI() + " " + (m[1] || m[2]).trim());
    }
}
