// 店舗設定で ON にしたときだけ現れる項目を、シナリオの送信に足す (httpsender スクリプト)。
//
// 商品ごとの税率・送料を ON にすると、商品の登録・編集と規格の一覧に tax_rate・delivery_fee の項目が増える。
// OFF のときに送ると余分な項目として入力エラーになるため、シナリオには書かず、options_on.js が
// グローバル変数を立てたターゲット (zap/targets.json の *_options) でだけ足す。

var ScriptVars = Java.type('org.zaproxy.zap.extension.script.ScriptVars');
var JString = Java.type('java.lang.String');
var LATIN1 = Java.type('java.nio.charset.StandardCharsets').ISO_8859_1;

var VALUES = {tax_rate: '10', delivery_fee: '100'};

function sendingRequest(msg, initiator, helper) {
    if (ScriptVars.getGlobalVar('eccube.options') != '1') {
        return;
    }
    var header = msg.getRequestHeader();
    var path = String(header.getURI().getPath());
    var type = String(header.getHeader('Content-Type') || '');
    var body = String(new JString(msg.getRequestBody().getBytes(), LATIN1));
    var changed = body;
    var m;
    if (/^\/admin\/product\/product\/(new|\d+\/edit)$/.test(path) && (m = /boundary=([^;\s]+)/.exec(type))) {
        // 規格の無い商品 (admin_product[class] がある) にだけ足す
        if (body.indexOf('name="admin_product[class][sale_type]"') >= 0 && body.indexOf('name="admin_product[class][tax_rate]"') < 0) {
            var parts = '';
            Object.keys(VALUES).forEach(function (k) {
                parts += '--' + m[1] + '\r\nContent-Disposition: form-data; name="admin_product[class][' + k + ']"\r\n\r\n' + VALUES[k] + '\r\n';
            });
            var end = changed.lastIndexOf('--' + m[1] + '--');
            if (end >= 0) {
                changed = changed.substring(0, end) + parts + changed.substring(end);
            }
        }
    } else if (/^\/admin\/product\/product\/class\/\d+$/.test(path) && type.indexOf('x-www-form-urlencoded') >= 0) {
        var seen = {};
        var re = /product_class_matrix%5Bproduct_classes%5D%5B(\d+)%5D%5B/g;
        var add = '';
        while ((m = re.exec(body)) !== null) {
            if (seen[m[1]] || body.indexOf('product_class_matrix%5Bproduct_classes%5D%5B' + m[1] + '%5D%5Btax_rate%5D') >= 0) {
                continue;
            }
            seen[m[1]] = true;
            Object.keys(VALUES).forEach(function (k) {
                add += '&product_class_matrix%5Bproduct_classes%5D%5B' + m[1] + '%5D%5B' + k + '%5D=' + VALUES[k];
            });
        }
        changed += add;
    }
    if (changed !== body) {
        msg.setRequestBody(new JString(changed).getBytes(LATIN1));
        header.setContentLength(msg.getRequestBody().length());
    }
}

function responseReceived(msg, initiator, helper) {}
