// 画面の HTML にあるフォームを記録する (httpsender スクリプト)。
//
// シナリオが送っていない項目や、通っていない分岐 (mode やボタンの値) を見つけるため、
// フォームの送信先と項目を /tmp/forms.tsv に書き出す (zap/bin/form_coverage.sh で params.tsv と突き合わせる)。
// 攻撃の応答も含めてすべてを解析すると重いため、同じメソッドとパスの画面は最初の LIMIT 回だけ解析し、
// 同じ内容のフォームは 1 回だけ書き出す。
//
// クエリ文字列の付いたリンク (並べ替え・表示件数・ページ送りなど) も、フォームのメソッドを LINK として記録する。
// リンクの値は、分岐に関わる名前 (LINK_VALUES) だけを比べ、ID やページ番号は名前だけを比べる。
//
// 出力はタブ区切り: <画面のメソッド> <画面のパス> <フォームのメソッド> <送信先のパス> <項目>
// 項目は名前 (URL エンコード) をスペースで区切ったもの。値で分岐する項目 (mode・ボタン・hidden の _method) は
// 名前=値のハッシュ (record_params.js と同じ)~値 (URL エンコード) を付ける。

var OUT = Java.type('java.nio.file.Paths').get('/tmp/forms.tsv');
var Files = Java.type('java.nio.file.Files');
var StandardOpenOption = Java.type('java.nio.file.StandardOpenOption');
var JString = Java.type('java.lang.String');
var StandardCharsets = Java.type('java.nio.charset.StandardCharsets');
var ConcurrentHashMap = Java.type('java.util.concurrent.ConcurrentHashMap');
var AtomicInteger = Java.type('java.util.concurrent.atomic.AtomicInteger');

var LIMIT = 5;
var LINK_VALUES = /^(mode|orderby|disp_number|sort|order|type|resume)$/;
var parsed = new ConcurrentHashMap();
var written = new ConcurrentHashMap();

function hash(v) {
    var s = String(v);
    return s.length + ':' + (new JString(s).hashCode() >>> 0).toString(36);
}

function attr(tag, name) {
    var m = new RegExp('\\s' + name + '\\s*=\\s*(?:"([^"]*)"|\'([^\']*)\'|([^\\s>]+))', 'i').exec(tag);
    if (m === null) {
        return null;
    }
    var v = m[1] !== undefined ? m[1] : (m[2] !== undefined ? m[2] : m[3]);
    return v.replace(/&amp;/g, '&').replace(/&quot;/g, '"').replace(/&#0?39;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>');
}

function normalize(path) {
    return path.split('/').map(function (s) {
        return /^[0-9]+$/.test(s) ? '{id}' : (s.length >= 20 && /^[A-Za-z0-9_-]+$/.test(s) ? '{token}' : s);
    }).join('/');
}

function sendingRequest(msg, initiator, helper) {}

function responseReceived(msg, initiator, helper) {
    var header = msg.getRequestHeader();
    var uri = header.getURI();
    if (uri.getHost() != 'ec-cube' || msg.getResponseHeader().getStatusCode() != 200
        || String(msg.getResponseHeader().getHeader('Content-Type') || '').indexOf('text/html') < 0) {
        return;
    }
    var page = String(uri.getPath() || '/');
    var key = header.getMethod() + ' ' + normalize(page);
    parsed.putIfAbsent(key, new AtomicInteger());
    if (parsed.get(key).incrementAndGet() > LIMIT) {
        return;
    }
    var html = String(msg.getResponseBody().toString());
    var formRe = /<form\b([^>]*)>([\s\S]*?)<\/form>/gi;
    var f;
    var lines = '';
    while ((f = formRe.exec(html)) !== null) {
        var method = (attr(f[1], 'method') || 'GET').toUpperCase();
        var action = attr(f[1], 'action');
        var target = page;
        if (action !== null && action !== '') {
            var m = /^(?:https?:\/\/([^\/]+))?([^?#]*)/.exec(action);
            if (m[1] && m[1] != 'ec-cube') {
                continue;
            }
            if (m[2] !== '') {
                target = m[2].charAt(0) == '/' ? m[2] : page.replace(/[^\/]*$/, '') + m[2];
            }
        }
        var fields = {};
        var fieldRe = /<(input|select|textarea|button)\b([^>]*)>/gi;
        var t;
        while ((t = fieldRe.exec(f[2])) !== null) {
            var name = attr(t[2], 'name');
            if (name === null || name === '') {
                continue;
            }
            var type = (attr(t[2], 'type') || '').toLowerCase();
            var value = attr(t[2], 'value');
            var item = encodeURIComponent(name);
            if (value !== null && (name == 'mode' || name == '_method' || t[1].toLowerCase() == 'button' || type == 'submit')) {
                item += '=' + hash(value) + '~' + encodeURIComponent(value);
            }
            fields[item] = true;
        }
        var line = [header.getMethod(), normalize(page), method, normalize(target), Object.keys(fields).sort().join(' ')].join('\t');
        if (written.putIfAbsent(line, true) === null) {
            lines += line + '\n';
        }
    }
    var linkRe = /<a\b[^>]*\shref\s*=\s*"([^"]*\?[^"]*)"/gi;
    var a;
    while ((a = linkRe.exec(html)) !== null) {
        var href = a[1].replace(/&amp;/g, '&');
        var lm = /^(?:https?:\/\/([^\/]+))?([^?#]*)\?([^#]*)/.exec(href);
        if (lm === null || (lm[1] && lm[1] != 'ec-cube')) {
            continue;
        }
        var lpath = lm[2] === '' ? page : (lm[2].charAt(0) == '/' ? lm[2] : page.replace(/[^\/]*$/, '') + lm[2]);
        var items = {};
        lm[3].split('&').forEach(function (pair) {
            if (pair === '') {
                return;
            }
            var i = pair.indexOf('=');
            var name;
            var value;
            try {
                name = decodeURIComponent((i < 0 ? pair : pair.substring(0, i)).replace(/\+/g, ' '));
                value = i < 0 ? '' : decodeURIComponent(pair.substring(i + 1).replace(/\+/g, ' '));
            } catch (e) {
                return;
            }
            items[encodeURIComponent(name) + (LINK_VALUES.test(name) ? '=' + hash(value) + '~' + encodeURIComponent(value) : '')] = true;
        });
        var link = [header.getMethod(), normalize(page), 'LINK', normalize(lpath), Object.keys(items).sort().join(' ')].join('\t');
        if (written.putIfAbsent(link, true) === null) {
            lines += link + '\n';
        }
    }
    if (lines !== '') {
        Files.write(OUT, new JString(lines).getBytes(StandardCharsets.UTF_8),
            StandardOpenOption.CREATE, StandardOpenOption.WRITE, StandardOpenOption.APPEND);
    }
}
