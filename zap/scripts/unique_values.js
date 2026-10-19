// 一意でなければならない項目を、2 回目以降の送信で一意な値に置き換える (httpsender スクリプト)。
//
// シナリオの値 (メールアドレス、コード、ファイル名など) はシナリオの実行ごとに 1 回だけ作られ、再生のたびに同じ値が送られる。
// 新規登録は最初の送信で保存されるため、2 回目以降の送信は元の値のままでも一意性の検証で弾かれ、
// 攻撃がどの項目も処理に届かない (zap/bin/param_counts.sh の「(元の値)」に入力エラーが出る)。
// 最初の送信は元の値のまま送る。後続のステップは、その値で登録したデータを参照するため。
// 攻撃で値が書き換わった項目は、元の値と一致しないため置き換えない。
//
// 置き換えた項目の元の値は X-ZAP-Unique-Original ヘッダーに付けて送り、record_params.js が元の値として記録する
// (置き換えた値のまま記録すると、どの送信もその項目への攻撃に見えるため)。

var ConcurrentHashMap = Java.type('java.util.concurrent.ConcurrentHashMap');
var AtomicInteger = Java.type('java.util.concurrent.atomic.AtomicInteger');
var LocalDate = Java.type('java.time.LocalDate');
var JString = Java.type('java.lang.String');
// 本文にはバイナリ (テンプレートの tar.gz など) も入るため、ISO-8859-1 で 1 バイトを 1 文字として扱い、そのまま戻す
var LATIN1 = Java.type('java.nio.charset.StandardCharsets').ISO_8859_1;

// kind: suffix (末尾に連番を付ける。max を超える場合は末尾を連番に置き換える) / email (@ の前に連番を付ける) /
//       date (yyyy-MM-dd の日付を連番の日数だけ進める)
// step: 同じパスに複数のステップがある場合に、ステップを見分ける項目。値ごとに最初の送信を元の値のまま送る
var RULES = [
    {path: /^\/admin\/customer\/new$/, kind: 'email', names: ['admin_customer[email]']},
    {path: /^\/entry$/, kind: 'email', step: 'mode', names: ['entry[email][first]', 'entry[email][second]']},
    {path: /^\/admin\/setting\/system\/member\/new$/, kind: 'suffix', max: 50, names: ['admin_member[login_id]']},
    {path: /^\/admin\/plugin\/coupon\/new$/, kind: 'suffix', max: 20, names: ['coupon[coupon_cd]']},
    {path: /^\/admin\/content\/page\/new$/, kind: 'suffix', max: 255, names: ['main_edit[url]', 'main_edit[file_name]']},
    {path: /^\/admin\/content\/block\/new$/, kind: 'suffix', max: 255, names: ['block[file_name]']},
    {path: /^\/admin\/setting\/shop\/mail$/, kind: 'suffix', max: 255, names: ['mail[file_name]']},
    {path: /^\/admin\/store\/template\/install$/, kind: 'suffix', max: 255, names: ['admin_template[code]']},
    {path: /^\/admin\/setting\/shop\/calendar\/new$/, kind: 'date', names: ['calendar[holiday]']}
];

var seen = new ConcurrentHashMap();
var counter = new AtomicInteger();

function decode(s) {
    try {
        return decodeURIComponent(s.replace(/\+/g, ' '));
    } catch (e) {
        return s;
    }
}

function quote(s) {
    return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function unique(rule, value, n) {
    if (rule.kind == 'email') {
        var at = value.lastIndexOf('@');
        return at < 0 ? value : value.substring(0, at) + n + value.substring(at);
    }
    if (rule.kind == 'date') {
        try {
            return String(LocalDate.parse(value).plusDays(n).toString());
        } catch (e) {
            return value;
        }
    }
    var s = String(n);
    return value.length + s.length <= rule.max ? value + s : value.substring(0, rule.max - s.length) + s;
}

function sendingRequest(msg, initiator, helper) {
    var header = msg.getRequestHeader();
    var uri = header.getURI();
    if (uri.getHost() != 'ec-cube' || header.getMethod() != 'POST') {
        return;
    }
    var path = String(uri.getPath());
    var rule = null;
    RULES.forEach(function (r) { if (r.path.test(path)) { rule = r; } });
    if (rule === null) {
        return;
    }
    var multipart = String(header.getHeader('Content-Type') || '').toLowerCase().indexOf('multipart/form-data') >= 0;
    var body = String(new JString(msg.getRequestBody().getBytes(), LATIN1));
    var step = '';
    if (rule.step) {
        var sm = new RegExp('(?:^|&)' + quote(rule.step) + '=([^&]*)').exec(body);
        step = sm === null ? '' : sm[1];
    }
    var n = 0;
    var originals = [];
    rule.names.forEach(function (name) {
        // application/x-www-form-urlencoded は値を URL エンコードしたまま、multipart/form-data はそのまま扱う
        var re = multipart
            ? new RegExp('(name="' + quote(name) + '"\\r\\n\\r\\n)([^\\r]*)(\\r\\n)')
            : new RegExp('((?:^|&)' + quote(encodeURIComponent(name)) + '=)([^&]*)()', 'i');
        var m = re.exec(body);
        if (m === null) {
            return;
        }
        // 置き換える値は ASCII (メールアドレス・コード・日付) のため、ISO-8859-1 のままで比べられる
        var value = multipart ? m[2] : decode(m[2]);
        var key = path + '\n' + step + '\n' + name + '\n' + value;
        if (seen.putIfAbsent(key, true) === null) {
            return;
        }
        if (n === 0) {
            n = counter.incrementAndGet();
        }
        var replaced = unique(rule, value, n);
        body = body.substring(0, m.index) + m[1] + (multipart ? replaced : encodeURIComponent(replaced)) + m[3]
            + body.substring(m.index + m[0].length);
        originals.push(encodeURIComponent(name) + '=' + encodeURIComponent(value));
    });
    if (originals.length > 0) {
        msg.setRequestBody(new JString(body).getBytes(LATIN1));
        header.setContentLength(msg.getRequestBody().length());
        header.setHeader('X-ZAP-Unique-Original', originals.join('&'));
    }
}

function responseReceived(msg, initiator, helper) {}
