// EC-CUBE へ送ったリクエストのパラメータを記録する (httpsender スクリプト)。
//
// アクセスログからは、能動スキャンがどの項目を攻撃したかが分からない。
// リクエストごとに、パラメータの名前と値のハッシュ、応答の状態コードとリダイレクト先を /tmp/params.tsv に書き出す。
// 攻撃は 1 回に 1 項目だけ値を書き換えるため、同じリクエストの中で値が最も多いものを元の値とみなせば、
// 書き換えた項目が分かる (zap/bin/param_counts.sh)。
// クエリ文字列、application/x-www-form-urlencoded、multipart/form-data、JSON の本文を対象にする。
// JSON は葉の値ごとに、キーを . (配列は [番号]) でつないだ名前で記録する。
//
// 出力はタブ区切り: <メソッド> <パス> <状態コード> <リダイレクト先> <名前=ハッシュ をスペースで区切ったもの>
// 名前は URL エンコードする。ハッシュは値の長さと String.hashCode の組。

var OUT = Java.type('java.nio.file.Paths').get('/tmp/params.tsv');
var Files = Java.type('java.nio.file.Files');
var StandardOpenOption = Java.type('java.nio.file.StandardOpenOption');
var JString = Java.type('java.lang.String');
var StandardCharsets = Java.type('java.nio.charset.StandardCharsets');

function hash(v) {
    var s = String(v);
    return s.length + ':' + (new JString(s).hashCode() >>> 0).toString(36);
}

function decode(s) {
    try {
        return decodeURIComponent(s.replace(/\+/g, ' '));
    } catch (e) {
        return s;
    }
}

function addUrlEncoded(params, s) {
    if (!s) {
        return;
    }
    s.split('&').forEach(function (pair) {
        if (pair === '') {
            return;
        }
        var i = pair.indexOf('=');
        params.push([decode(i < 0 ? pair : pair.substring(0, i)), i < 0 ? '' : decode(pair.substring(i + 1))]);
    });
}

function addJson(params, name, v) {
    if (v !== null && typeof v === 'object') {
        if (Array.isArray(v)) {
            v.forEach(function (e, i) { addJson(params, name + '[' + i + ']', e); });
        } else {
            Object.keys(v).forEach(function (k) { addJson(params, name === '' ? k : name + '.' + k, v[k]); });
        }
        return;
    }
    params.push([name, JSON.stringify(v)]);
}

function addMultipart(params, body) {
    var re = /Content-Disposition: *form-data; *name="([^"]*)"[^\r\n]*\r\n(?:[^\r\n]+\r\n)*\r\n([\s\S]*?)\r\n--/gi;
    var m;
    while ((m = re.exec(body)) !== null) {
        params.push([m[1], m[2]]);
    }
}

function sendingRequest(msg, initiator, helper) {}

function responseReceived(msg, initiator, helper) {
    var header = msg.getRequestHeader();
    var uri = header.getURI();
    if (uri.getHost() != 'ec-cube') {
        return;
    }
    var params = [];
    addUrlEncoded(params, uri.getEscapedQuery());
    var type = String(header.getHeader('Content-Type') || '').toLowerCase();
    var body = String(msg.getRequestBody().toString());
    if (type.indexOf('application/x-www-form-urlencoded') >= 0) {
        addUrlEncoded(params, body);
    } else if (type.indexOf('multipart/form-data') >= 0) {
        addMultipart(params, body);
    } else if (type.indexOf('json') >= 0 && body !== '') {
        try {
            addJson(params, '', JSON.parse(body));
        } catch (e) {
            // 攻撃で JSON として読めなくなった本文は、本文全体を 1 つの値として記録する
            params.push(['(本文)', body]);
        }
    }
    var location = String(msg.getResponseHeader().getHeader('Location') || '');
    var line = [
        header.getMethod(),
        uri.getEscapedPath() || '/',
        msg.getResponseHeader().getStatusCode(),
        location.replace(/[\t\r\n]/g, ' '),
        params.map(function (p) { return encodeURIComponent(p[0]) + '=' + hash(p[1]); }).join(' ')
    ].join('\t') + '\n';
    Files.write(OUT, new JString(line).getBytes(StandardCharsets.UTF_8),
        StandardOpenOption.CREATE, StandardOpenOption.WRITE, StandardOpenOption.APPEND);
}
