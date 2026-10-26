// 別の画面にだけ値が出る格納型 XSS を調べる (能動スキャンのスクリプト。ルール 50000 の Script Active Scan Rules として動く)。
//
// ZAP の格納型 XSS のルール (40014 / 40016 / 40017) は、項目ごとに目印を書き込んだ後でまとめて画面を探すため、
// 編集のフォームでは次の項目の送信で前の項目の目印が元の値に戻り、新規登録では増えたレコードに目印が一覧から
// 押し出されて検知できない (zap/README.md の「検知できることの検証」)。
// このスクリプトは、シナリオのリクエストの X-ZAP-Sink ヘッダーに書いた、値が出る画面の URL (カンマ区切りで複数) を、
// 項目ごとに目印の値を送った直後に開き、目印がエスケープされずに出ていれば High のアラートを出す。
// X-ZAP-Sink の無いリクエストでは何もしない。

var HttpMessage = Java.type('org.parosproxy.paros.network.HttpMessage');
var HttpRequestHeader = Java.type('org.parosproxy.paros.network.HttpRequestHeader');
var URI = Java.type('org.apache.commons.httpclient.URI');

var SINK_HEADER = 'X-ZAP-Sink';

function scanNode(as, msg) {}

function scan(as, msg, param, value) {
    var sinks = msg.getRequestHeader().getHeader(SINK_HEADER);
    if (!sinks) {
        return;
    }
    var marker = 'zapsk' + Math.floor(Math.random() * 1000000000);
    var payload = '<' + marker + '>';
    var attack = msg.cloneRequest();
    as.setParam(attack, param, payload);
    // CSRF トークンは送信のたびに取り直す
    as.sendAndReceive(attack, false, true);

    String(sinks).split(',').forEach(function (url) {
        url = url.trim();
        if (url === '' || as.isStop()) {
            return;
        }
        var sink = new HttpMessage(new HttpRequestHeader('GET', new URI(url, true), 'HTTP/1.1'));
        var cookie = msg.getRequestHeader().getHeader('Cookie');
        if (cookie) {
            sink.getRequestHeader().setHeader('Cookie', cookie);
        }
        as.sendAndReceive(sink, false, false);
        var body = String(sink.getResponseBody().toString());
        if (body.indexOf(payload) >= 0) {
            as.newAlert()
                .setRisk(3)
                .setConfidence(2)
                .setName('Cross Site Scripting (Persistent, X-ZAP-Sink)')
                .setDescription('送った値が、別の画面 (' + url + ') にエスケープされずに出力されました。')
                .setParam(param)
                .setAttack(payload)
                .setEvidence(payload)
                .setOtherInfo('値を送ったリクエスト: ' + attack.getRequestHeader().getURI() + '\n値が出た画面: ' + url)
                .setCweId(79)
                .setWascId(8)
                .setMessage(sink)
                .raise();
        }
    });
}
