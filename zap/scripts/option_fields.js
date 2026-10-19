// 店舗設定で ON にしたときだけ現れる項目を、OFF のターゲットでは送信の直前に取り除く (httpsender スクリプト)。
//
// 商品ごとの税率・送料を ON にすると、商品の登録・編集と規格の一覧に tax_rate・delivery_fee の項目が増える。
// ZAP はシナリオにある項目だけを攻撃するため、項目はシナリオに書く。OFF のときに送ると余分な項目として
// 入力エラーになるため、options_on.js がグローバル変数を立てたターゲット (zap/targets.json の *_options) 以外では
// 取り除く。OFF のターゲットでは、これらの項目への攻撃は元のリクエストと同じ内容で送られる。

var ScriptVars = Java.type('org.zaproxy.zap.extension.script.ScriptVars');
var JString = Java.type('java.lang.String');
var LATIN1 = Java.type('java.nio.charset.StandardCharsets').ISO_8859_1;

var NAMES = '(?:tax_rate|delivery_fee)';

function sendingRequest(msg, initiator, helper) {
    if (ScriptVars.getGlobalVar('eccube.options') == '1') {
        return;
    }
    var header = msg.getRequestHeader();
    var path = String(header.getURI().getPath());
    if (!/^\/admin\/product\/product\/(new|\d+\/edit|class\/\d+)$/.test(path)) {
        return;
    }
    var type = String(header.getHeader('Content-Type') || '');
    var body = String(new JString(msg.getRequestBody().getBytes(), LATIN1));
    var changed = body;
    var m;
    if ((m = /boundary=([^;\s]+)/.exec(type))) {
        var b = m[1].replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        changed = changed.replace(new RegExp('--' + b + '\\r\\nContent-Disposition: form-data; name="admin_product\\[class\\]\\[' + NAMES + '\\]"\\r\\n\\r\\n[^\\r]*\\r\\n', 'g'), '');
    } else {
        changed = changed.replace(new RegExp('&?product_class_matrix%5Bproduct_classes%5D%5B\\d+%5D%5B' + NAMES + '%5D=[^&]*', 'gi'), '');
    }
    if (changed !== body) {
        msg.setRequestBody(new JString(changed).getBytes(LATIN1));
        header.setContentLength(msg.getRequestBody().length());
    }
}

function responseReceived(msg, initiator, helper) {}
