// 2 段階認証のコード (TOTP) を、送信の直前に計算して埋める (httpsender スクリプト)。
//
// Zest ではコードを計算できないため、シナリオは device_token に ZAP_TOTP を入れて送り、同じリクエストの
// auth_key (設定・変更の画面) を鍵にして置き換える。能動スキャンの攻撃で device_token が書き換わったリクエストは
// そのまま送る。EC-CUBE (RobThree/TwoFactorAuth) の既定に合わせ、HMAC-SHA1・30 秒・6 桁で計算する。
//
// ログイン後の認証の画面 (/two_factor_auth) は、設定・変更で付く認証済みの cookie が無いときだけ表示される。
// 能動スキャンは cookie を内部で保持して送信のたびに付け直し、このスクリプトからは外せないため通していない
// (zap/coverage_exclude.txt)。

var PLACEHOLDER = 'device_token%5D=ZAP_TOTP';
var AUTH_KEY_PATTERN = /admin_two_factor_auth%5Bauth_key%5D=([A-Z2-7]+)/;

var Mac = Java.type('javax.crypto.Mac');
var SecretKeySpec = Java.type('javax.crypto.spec.SecretKeySpec');

function base32Decode(text) {
    var alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    var bytes = [];
    var buffer = 0;
    var bits = 0;
    for (var i = 0; i < text.length; i++) {
        buffer = ((buffer << 5) | alphabet.indexOf(text.charAt(i))) & 0xffff;
        bits += 5;
        if (bits >= 8) {
            bits -= 8;
            bytes.push((buffer >>> bits) & 0xff);
        }
    }
    return bytes;
}

function toJavaBytes(values) {
    return Java.to(values.map(function (b) { return b > 127 ? b - 256 : b; }), 'byte[]');
}

function totp(key) {
    var counter = Math.floor(Date.now() / 1000 / 30);
    var message = [];
    for (var i = 7; i >= 0; i--) {
        message[i] = counter % 256;
        counter = Math.floor(counter / 256);
    }
    var mac = Mac.getInstance('HmacSHA1');
    mac.init(new SecretKeySpec(toJavaBytes(base32Decode(key)), 'HmacSHA1'));
    var hash = mac.doFinal(toJavaBytes(message));
    var offset = hash[19] & 0x0f;
    var code = ((hash[offset] & 0x7f) << 24) | ((hash[offset + 1] & 0xff) << 16)
        | ((hash[offset + 2] & 0xff) << 8) | (hash[offset + 3] & 0xff);
    return ('000000' + (code % 1000000)).slice(-6);
}

function sendingRequest(msg, initiator, helper) {
    var body = msg.getRequestBody().toString();
    if (body.indexOf(PLACEHOLDER) < 0) {
        return;
    }
    var m = AUTH_KEY_PATTERN.exec(body);
    // 攻撃で auth_key が 1 文字などになると鍵が空になる。ZAP は例外を投げたスクリプトを止め、以降の再生で
    // ZAP_TOTP が置き換わらなくなるため、計算できないときはそのまま送る
    if (!m || base32Decode(m[1]).length === 0) {
        return;
    }
    var code;
    try {
        code = totp(m[1]);
    } catch (e) {
        return;
    }
    msg.setRequestBody(body.replace(PLACEHOLDER, 'device_token%5D=' + code));
    msg.getRequestHeader().setContentLength(msg.getRequestBody().length());
}

function responseReceived(msg, initiator, helper) {}
