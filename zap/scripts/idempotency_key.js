// Idempotency-Key を、送信のたびに新しい値にする (httpsender スクリプト)。
//
// エージェントコマースの API (ACP / UCP のチェックアウト) は、同じキーで内容の異なるリクエストを 422 で拒否する。
// シナリオで固定のキーを送ると、本文を書き換えた能動スキャンの攻撃がすべて 422 で止まり処理に届かないため、
// シナリオでは Idempotency-Key に ZAP_RANDOM を入れて送り、ここでリクエストごとの値に置き換える。

var PLACEHOLDER = 'ZAP_RANDOM';
var UUID = Java.type('java.util.UUID');

function sendingRequest(msg, initiator, helper) {
    var header = msg.getRequestHeader();
    if (header.getHeader('Idempotency-Key') == PLACEHOLDER) {
        header.setHeader('Idempotency-Key', 'zap-' + UUID.randomUUID().toString());
    }
}

function responseReceived(msg, initiator, helper) {}
