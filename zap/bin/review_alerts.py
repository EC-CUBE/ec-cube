#!/usr/bin/env python3
"""ZAP のスキャン結果 (alerts.json と HTML レポート) を、ルールごとにまとめたレビュー用の HTML にする。

usage: review_alerts.py -o review.html [-f zap/options.properties] [-k zap/known_alerts.tsv] [-t タイトル] <ラベル>=<結果のディレクトリ> ...

  結果のディレクトリの下を再帰的に探し、alerts.json のあるディレクトリを 1 ターゲットとして扱う。
  ターゲット名はディレクトリ名 (CI の artifact の zap-<target>-report なら <target>)。
  ラベルは DB など結果の系統の名前で、ラベルごとに件数を並べる。

  # CI の run の artifact から作る
  gh run download <run-id> -D out/pgsql -p 'zap-*-report'
  zap/bin/review_alerts.py -o out/review.html pgsql=out/pgsql

  - 全レベル (Informational まで) のアラートを、ルールごとに URL (ID を {id} に正規化) とパラメータでまとめる
  - 各箇所を「要確認」「除外」「既知」に分け、要確認が残るルールを先に並べる
  - 除外は、今の options.properties のフィルターに当てはまるもの (ZAP が False Positive にしたものを含む) と、
    check_results.sh と同じく根拠の無い Path Traversal。理由はフィルターの直前のコメント。
    スキャンの時点で ZAP がまだ外していなかったものには「スキャンの時点では ZAP で未適用」と添える
  - 既知は、zap/known_alerts.tsv に当てはまるもの (仕様・誤検知と判断済み)
  - ルール名・説明・対処法は HTML レポートから取る
  - 判定とメモはブラウザの localStorage に保存し、CSV で書き出せる
  - 攻撃の値はエスケープして出力する。外部のファイルは読み込まない
"""
import argparse
import collections
import html
import json
import os
import re
import sys

RISK = {3: 'High', 2: 'Medium', 1: 'Low', 0: 'Informational', -1: 'False Positive'}
CONF = {0: 'False Positive', 1: 'Low', 2: 'Medium', 3: 'High', 4: 'Confirmed'}


def target_name(d):
    b = os.path.basename(d.rstrip('/'))
    m = re.fullmatch(r'zap-(.+)-report', b)
    return m.group(1) if m else b


def load_filters(path):
    filters = collections.defaultdict(dict)
    if not path:
        return []
    comment, in_comment = [], False
    for line in open(path, encoding='utf-8'):
        line = line.rstrip('\n')
        if line.startswith('#'):
            if not in_comment:
                comment, in_comment = [], True
            comment.append(line.lstrip('# '))
            continue
        in_comment = False
        m = re.match(r'globalalertfilter\.filters\.filter\\\((\d+)\\\)\.(\w+)=(.*)$', line)
        if m:
            # フィルターの直前のコメントを理由として表示する
            f = filters[int(m.group(1))]
            f.setdefault('comment', ' '.join(comment))
            f[m.group(2)] = m.group(3)
    out = []
    for i, f in sorted(filters.items()):
        if f.get('enabled', 'true') != 'true' or f.get('newrisk') != '-1':
            continue
        out.append((i, f))
    return out


def field_match(f, key, value):
    pat = f.get(key, '')
    if pat == '':
        return True
    value = value or ''
    if f.get(key + 'regex') == 'true':
        try:
            return re.fullmatch(pat, value) is not None
        except re.error:
            return False
    return pat == value


def filtered_by(filters, a):
    for i, f in filters:
        if str(a.get('ruleid')) != f.get('ruleid'):
            continue
        if all(field_match(f, k, a.get(k)) for k in ('url', 'param', 'attack', 'evidence')):
            return i
    return None


def load_known(path):
    """既知のアラートの一覧 (zap/known_alerts.tsv) を読む。列はルール ID・URL・パラメータ・根拠・区分・理由。
    URL・パラメータは正規表現の完全一致、根拠は正規表現の部分一致で、空は何にでも当てはまる。"""
    known = []
    if not path or not os.path.exists(path):
        return known
    for n, line in enumerate(open(path, encoding='utf-8'), 1):
        line = line.rstrip('\n')
        if not line.strip() or line.startswith('#'):
            continue
        cols = line.split('\t')
        if len(cols) != 6:
            sys.exit('%s:%d: 列が 6 つではありません' % (path, n))
        rid, url, param, evidence, kind, reason = cols
        known.append({'ruleid': int(rid), 'url': re.compile(url) if url else None,
                      'param': re.compile(param) if param else None,
                      'evidence': re.compile(evidence) if evidence else None, 'kind': kind, 'reason': reason})
    return known


def known_as(known, a):
    for k in known:
        if k['ruleid'] != int(a.get('ruleid')):
            continue
        if k['url'] and not k['url'].fullmatch(a.get('url') or ''):
            continue
        if k['param'] and not k['param'].fullmatch(a.get('param') or ''):
            continue
        if k['evidence'] and not k['evidence'].search(a.get('evidence') or ''):
            continue
        return '%s: %s' % (k['kind'], k['reason'])
    return None


def norm_url(url):
    m = re.match(r'(https?://[^/]+)(/[^?#]*)?(\?[^#]*)?', url or '')
    if not m:
        return url or ''
    path = m.group(2) or '/'
    segs = []
    for s in path.split('/'):
        if re.fullmatch(r'\d+', s):
            s = '{id}'
        elif re.fullmatch(r'[0-9a-fA-F]{16,}|[A-Za-z0-9_-]{32,}', s):
            s = '{token}'
        segs.append(s)
    q = ''
    if m.group(3):
        names = sorted({p.split('=', 1)[0] for p in m.group(3)[1:].split('&') if p})
        q = '?' + '&'.join(names)
    return m.group(1) + '/'.join(segs) + q


def strip_tags(s):
    s = re.sub(r'<br\s*/?>|</p>\s*<p>', '\n', s)
    s = re.sub(r'<[^>]+>', '', s)
    return html.unescape(s).strip()


def parse_report(path):
    """HTML レポートから、アラートの種類 (ref) ごとの名前・説明・対処法などを取り出す。"""
    s = re.sub(r'\s+', ' ', open(path, encoding='utf-8').read())
    types = {}
    for m in re.finditer(r'<li\s+id="alert-type-(\d+)">\s*<h4>(.*?)</h4>(.*?)</table>', s):
        n, name, body = m.group(1), strip_tags(m.group(2)), m.group(3)
        ref = re.search(r'zaproxy\.org/docs/alerts/([^/"]+)/', body)
        cwe = re.search(r'CWE ID</th>\s*<td><a[^>]*>(\d+)', body)
        wasc = re.search(r'WASC ID</th>\s*<td>(\d+)', body)
        refs = re.findall(r'<li><a href="([^"]+)"', body)
        types[n] = {'name': name, 'ref': ref.group(1) if ref else '', 'cwe': cwe.group(1) if cwe else '',
                    'wasc': wasc.group(1) if wasc else '', 'refs': refs, 'desc': '', 'solution': ''}
    for m in re.finditer(r'<a href="#alert-type-(\d+)">.*?</h5>(.*?)</ol>', s, re.S):
        t = types.get(m.group(1))
        if not t or t['desc']:
            continue
        d = re.search(r'Alert description</th>\s*<td>(.*?)</td>', m.group(2), re.S)
        so = re.search(r'Solution</th>\s*<td>(.*?)</td>', m.group(2), re.S)
        t['desc'] = strip_tags(d.group(1)) if d else ''
        t['solution'] = strip_tags(so.group(1)) if so else ''
    return types.values()


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('-o', '--output', required=True)
    ap.add_argument('-f', '--filters', default=os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'options.properties'))
    ap.add_argument('-k', '--known', default=os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'known_alerts.tsv'))
    ap.add_argument('-t', '--title', default='ZAP スキャン結果のレビュー')
    ap.add_argument('sources', nargs='+')
    args = ap.parse_args()

    filters = load_filters(args.filters)
    known = load_known(args.known)
    rules = {}   # ruleid -> {variants: {ref: info}}
    instances = collections.defaultdict(lambda: collections.defaultdict(lambda: {
        'count': 0, 'open': 0, 'targets': set(), 'labels': set(), 'risk': set(), 'conf': set(),
        'filtered': collections.Counter(), 'notes': collections.Counter(), 'known': collections.Counter(), 'samples': [], 'raw_urls': set()}))
    targets = collections.defaultdict(set)
    risk_counts = collections.defaultdict(collections.Counter)
    status = collections.defaultdict(dict)  # target -> label -> (plan, replay failures, total, Counter)

    for src in args.sources:
        label, _, root = src.partition('=')
        for d, _, files in os.walk(root):
            if 'alerts.json' not in files:
                continue
            tgt = target_name(d)
            targets[label].add(tgt)
            plan, fails, total = '—', 0, 0
            log = os.path.join(d, 'zap.log')
            if os.path.exists(log):
                text = open(log, encoding='utf-8', errors='replace').read()
                plan = '成功' if '自動化プランが成功' in text else '失敗'
                fails = len(re.findall(r'passed = false|Assign: failed|^Form error:', text, re.M))
                total = len(re.findall(r'^Response:', text, re.M))
            tcount = collections.Counter()
            status[tgt][label] = (plan, fails, total, tcount)
            for f in files:
                if f.startswith('ZAP-Report-') and f.endswith('.html'):
                    for t in parse_report(os.path.join(d, f)):
                        base = re.match(r'\d+', t['ref'] or '')
                        if not base:
                            continue
                        r = rules.setdefault(int(base.group(0)), {'variants': {}})
                        r['variants'].setdefault(t['ref'], t)
            try:
                alerts = json.load(open(os.path.join(d, 'alerts.json'), encoding='utf-8'))
            except (ValueError, OSError) as e:
                print(f'skip {d}: {e}', file=sys.stderr)
                continue
            for a in alerts:
                rid = int(a.get('ruleid'))
                rules.setdefault(rid, {'variants': {}})
                fid = filtered_by(filters, a)
                # 除外: ZAP が外したもの (フィルターが効くと confidence が False Positive になる) と、今の
                # options.properties のフィルターに当てはまるもの。スキャンの後で足したフィルターも判断に使うため。
                # check_results.sh と同じく、根拠の無い Path Traversal も High の判定から外している
                comment = dict(filters).get(fid, {}).get('comment', '') if fid is not None else ''
                if a.get('confidence') == 0 or fid is not None:
                    excluded = 'フィルター #%s%s' % (fid if fid is not None else '?', ': ' + comment if comment else '')
                elif rid == 6 and not a.get('evidence'):
                    excluded = '根拠なしの Path Traversal'
                else:
                    excluded = None
                if fid is not None and a.get('confidence') != 0:
                    ins_note = 'スキャンの時点では ZAP で未適用 (フィルター #%d)' % fid
                else:
                    ins_note = None
                key = (a.get('method') or '', norm_url(a.get('url')), a.get('param') or '')
                ins = instances[rid][key]
                ins['count'] += 1
                ins['targets'].add(tgt)
                ins['labels'].add(label)
                ins['risk'].add(a.get('risk'))
                ins['conf'].add(a.get('confidence'))
                ins['raw_urls'].add(a.get('url') or '')
                if excluded:
                    ins['filtered'][excluded] += 1
                if ins_note:
                    ins['notes'][ins_note] += 1
                kn = known_as(known, a)
                if kn:
                    ins['known'][kn] += 1
                settled = bool(excluded or kn)
                if not settled:
                    ins['open'] += 1
                if len(ins['samples']) < 2 and (a.get('attack') or a.get('evidence')):
                    sample = (a.get('attack') or '', a.get('evidence') or '')
                    if sample not in ins['samples']:
                        ins['samples'].append(sample)
                risk_counts[label][(a.get('risk'), settled)] += 1
                tcount[a.get('risk')] += 0 if settled else 1
                tcount['settled'] += 1 if settled else 0

    labels = [s.partition('=')[0] for s in args.sources]
    rows = []
    for rid, ins in instances.items():
        variants = rules.get(rid, {}).get('variants', {})
        risk = max(r for i in ins.values() for r in i['risk'])
        total = sum(i['count'] for i in ins.values())
        nopen = sum(i['open'] for i in ins.values())
        open_places = sum(1 for i in ins.values() if i['open'])
        rows.append({'risk': risk, 'total': total, 'rid': rid, 'variants': variants, 'ins': ins,
                     'open': nopen, 'open_places': open_places})
    # 要確認が残るルールを先に、リスクの高い順に並べる
    rows.sort(key=lambda r: (r['open'] == 0, -r['risk'], -r['open'], r['rid']))

    e = html.escape

    def state_of(i):
        """箇所の状態: (CSS のクラス, 表示, 理由の一覧)"""
        reasons = [k for k in i['known']] + [k for k in i['filtered']]
        if i['open']:
            return 'open', '要確認' if i['open'] == i['count'] else '要確認 (一部)', reasons
        if i['known']:
            kinds = sorted({k.split(':', 1)[0] for k in i['known']})
            return 'known', '既知・' + '/'.join(kinds), reasons
        return 'excluded', '除外', reasons

    out = []
    w = out.append
    w('<!doctype html><html lang="ja"><head><meta charset="utf-8">')
    w('<meta name="viewport" content="width=device-width, initial-scale=1">')
    w('<title>%s</title><style>%s</style></head><body>' % (e(args.title), CSS))
    w('<header><h1>%s</h1>' % e(args.title))
    w('<p class="meta">入力: %s</p>' % ' / '.join(
        '%s (%d ターゲット)' % (e(l), len(targets[l])) for l in labels))
    w('<div class="sumwrap"><table class="sum"><tr><th rowspan="2">リスク</th>%s<th colspan="2">ルール数</th></tr><tr>%s<th>要確認あり</th><th>判断済み</th></tr>' % (
        ''.join('<th colspan="2">%s</th>' % e(l) for l in labels),
        ''.join('<th class="st-open">要確認</th><th>判断済み</th>' for _ in labels)))
    for rk in (3, 2, 1, 0, -1):
        rr = [r for r in rows if r['risk'] == rk]
        if not rr and not any(risk_counts[l][(rk, x)] for l in labels for x in (True, False)):
            continue
        w('<tr><td><span class="risk r%d">%s</span></td>%s<td class="st-open">%d</td><td>%d</td></tr>' % (
            rk if rk >= 0 else 9, RISK[rk],
            ''.join('<td class="st-open">%s</td><td>%d</td>' % (risk_counts[l][(rk, False)] or '—', risk_counts[l][(rk, True)]) for l in labels),
            sum(1 for r in rr if r['open']), sum(1 for r in rr if not r['open'])))
    w('</table></div>')
    w('<p class="meta">件数は ZAP が出したアラートの件数 (同じ画面・項目の重複を含む)。'
      '<span class="chip open">要確認</span> まだ判断していないもの。'
      '<span class="chip excluded">除外</span> zap/options.properties の誤検知フィルター、または check_results.sh が High の判定から外すもの。'
      '<span class="chip known">既知</span> zap/known_alerts.tsv で仕様・誤検知と判断済みのもの (zap/README.md の「既知のアラート」)。'
      '判定とメモはこのブラウザの localStorage に保存され、「CSV で書き出す」で取り出せます。</p></header>')
    w('<div class="controls"><label>リスク <select id="frisk"><option value="0">Informational 以上</option>'
      '<option value="1">Low 以上</option><option value="2">Medium 以上</option><option value="3">High</option></select></label>'
      '<label><input type="checkbox" id="fhide"> 要確認だけを表示 (除外・既知を隠す)</label>'
      '<label><input type="checkbox" id="fundecided"> 判定を付けていないルールだけ</label>'
      '<input type="search" id="fq" placeholder="ルール名・URL・ターゲットで絞り込み">'
      '<button id="expand">すべて開く</button><button id="collapse">すべて閉じる</button>'
      '<button id="csv">CSV で書き出す</button></div><main>')

    w('<h2 class="sec">要確認が残るルール (%d)</h2>' % sum(1 for r in rows if r['open']))
    settled_header = False
    for r in rows:
        risk, total, rid, variants, ins = r['risk'], r['total'], r['rid'], r['variants'], r['ins']
        if not r['open'] and not settled_header:
            w('<h2 class="sec settled-sec">判断済みのルール (%d)。すべて除外・既知</h2>' % sum(1 for x in rows if not x['open']))
            settled_header = True
        names = sorted({v['name'] for v in variants.values() if v['name']}) or ['(名前不明: ルール %d)' % rid]
        v0 = next(iter(variants.values()), {})
        n_ex = sum(1 for i in ins.values() if state_of(i)[0] == 'excluded')
        n_kn = sum(1 for i in ins.values() if state_of(i)[0] == 'known')
        if r['open']:
            badge = '<span class="chip open">要確認 %d 箇所</span>' % r['open_places']
        else:
            badge = '<span class="chip done">判断済み</span>'
        extra = ''.join(x for x in (
            ' <span class="chip excluded">除外 %d</span>' % n_ex if n_ex else '',
            ' <span class="chip known">既知 %d</span>' % n_kn if n_kn else ''))
        w('<details class="rule %s" data-risk="%d" data-rule="%d" data-settled="%d">' % (
            'is-open' if r['open'] else 'is-settled', risk, rid, 0 if r['open'] else 1))
        w('<summary><span class="risk r%d">%s</span> <b>%s</b> <span class="rid">%d</span> %s%s'
          ' <span class="cnt">%d 箇所 / %d 件</span>'
          ' <select class="verdict" data-rule="%d" onclick="event.stopPropagation()">'
          '<option value="">判定なし</option><option>対応要</option><option>要調査</option>'
          '<option>誤検知</option><option>許容</option><option>情報のみ</option></select></summary>' % (
              risk if risk >= 0 else 9, RISK.get(risk, risk), e(' / '.join(names)), rid, badge, extra,
              len(ins), total, rid))
        w('<div class="body">')
        if v0:
            w('<details class="info"><summary>ルールの説明と対処法</summary>')
            if v0.get('desc'):
                w('<p class="desc">%s</p>' % e(v0['desc']).replace('\n', '<br>'))
            if v0.get('solution'):
                w('<p class="sol"><b>対処:</b> %s</p>' % e(v0['solution']).replace('\n', '<br>'))
            w('</details>')
            meta = []
            refs = sorted({v['ref'] for v in variants.values() if v['ref']})
            meta += ['<a href="https://www.zaproxy.org/docs/alerts/%s/" rel="noreferrer">%s</a>' % (e(x), e(x)) for x in refs]
            if v0.get('cwe'):
                meta.append('CWE-%s' % e(v0['cwe']))
            if v0.get('wasc'):
                meta.append('WASC-%s' % e(v0['wasc']))
            w('<p class="meta">%s</p>' % ' · '.join(meta))
        w('<textarea class="memo" data-rule="%d" placeholder="メモ"></textarea>' % rid)
        w('<table class="ins"><thead><tr><th>状態</th><th>メソッド</th><th>URL (正規化)</th><th>パラメータ</th><th>件数</th>'
          '<th>%s</th><th>ターゲット</th><th>理由</th><th>攻撃 / 根拠 (例)</th></tr></thead><tbody>' % '/'.join(e(l) for l in labels))
        # 要確認の箇所を先に
        order = {'open': 0, 'known': 1, 'excluded': 2}
        for (method, url, param), i in sorted(ins.items(), key=lambda kv: (order[state_of(kv[1])[0]], -kv[1]['count'], kv[0])):
            cls, text, reasons = state_of(i)
            tg = sorted(i['targets'])
            tgs = ', '.join(e(t) for t in tg[:6]) + (' ほか %d' % (len(tg) - 6) if len(tg) > 6 else '')
            samples = ''.join('<div><code>%s</code>%s</div>' % (
                e(a[:300]) or '—', (' → <code>%s</code>' % e(ev[:300])) if ev else '') for a, ev in i['samples'])
            why = ''.join('<div>%s</div>' % e(x) for x in reasons)
            why += ''.join('<div class="note">%s</div>' % e(x) for x in i['notes'])
            w('<tr class="st-%s" data-text="%s"><td><span class="chip %s">%s</span></td><td>%s</td><td class="url">%s</td><td>%s</td>'
              '<td>%d</td><td>%s</td><td>%s</td><td class="why">%s</td><td class="sample">%s</td></tr>' % (
                  cls, e(' '.join([url, param] + tg).lower()), cls, e(text), e(method), e(url), e(param),
                  i['count'], ' '.join('●' if l in i['labels'] else '·' for l in labels), tgs, why, samples))
        w('</tbody></table></div></details>')
    w('<h2 class="sec">ターゲット別の結果</h2>')
    w('<details class="rule tgt-table"><summary><b>ターゲット別の結果</b> <span class="cnt">%d ターゲット</span></summary><div class="body">' % len(status))
    w('<p class="meta">再生の失敗は check_results.sh と同じ数え方 (passed = false / Assign: failed / Form error)。High〜Info は要確認の件数。</p>')
    w('<table class="ins"><thead><tr><th>ターゲット</th>%s</tr></thead><tbody>' % ''.join(
        '<th>%s: プラン</th><th>再生の失敗</th><th>要確認 High / Med / Low / Info・判断済み</th>' % e(l) for l in labels))
    for tgt in sorted(status):
        cells = ''
        for l in labels:
            if l not in status[tgt]:
                cells += '<td>—</td><td></td><td></td>'
                continue
            plan, fails, total, c = status[tgt][l]
            cells += '<td>%s</td><td>%d / %d</td><td>%d / %d / %d / %d / %d</td>' % (
                e(plan), fails, total, c[3], c[2], c[1], c[0], c['settled'])
        w('<tr class="tgt" data-text="%s"><td>%s</td>%s</tr>' % (e(tgt), e(tgt), cells))
    w('</tbody></table></div></details>')
    w('</main><script>%s</script></body></html>' % JS)
    with open(args.output, 'w', encoding='utf-8') as fh:
        fh.write('\n'.join(out))
    print('%s: %d rules, %d instances' % (args.output, len(rows), sum(len(r['ins']) for r in rows)))


CSS = r'''
:root{--bg:#fff;--fg:#1d1d1f;--mut:#6b6b70;--line:#e2e2e6;--card:#f7f7f9;--hi:#c62828;--md:#e8710a;--lo:#c9a400;--in:#1a73e8;--fp:#888;--code:#f1f1f4;
--open-bg:#fdecea;--open-fg:#b3261e;--open-bd:#e8a29c;--ex-bg:#ececef;--ex-fg:#55555c;--kn-bg:#e6f4ea;--kn-fg:#1e6b34;--done-bg:#e6f4ea;--done-fg:#1e6b34;--note:#9a5b00}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){--bg:#17171a;--fg:#e8e8ea;--mut:#9a9aa2;--line:#33333a;--card:#1f1f24;--code:#26262c;
--open-bg:#4a1e1b;--open-fg:#ffb4ab;--open-bd:#8c3a33;--ex-bg:#2c2c32;--ex-fg:#b5b5bd;--kn-bg:#173a24;--kn-fg:#9fd8ad;--done-bg:#173a24;--done-fg:#9fd8ad;--note:#f0b45a}}
:root[data-theme="dark"]{--bg:#17171a;--fg:#e8e8ea;--mut:#9a9aa2;--line:#33333a;--card:#1f1f24;--code:#26262c;
--open-bg:#4a1e1b;--open-fg:#ffb4ab;--open-bd:#8c3a33;--ex-bg:#2c2c32;--ex-fg:#b5b5bd;--kn-bg:#173a24;--kn-fg:#9fd8ad;--done-bg:#173a24;--done-fg:#9fd8ad;--note:#f0b45a}
:root{color-scheme:light dark;--link:#0b57d0}@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){--link:#8ab4f8}}:root[data-theme="dark"]{--link:#8ab4f8}a{color:var(--link)}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--fg);font:14px/1.55 system-ui,-apple-system,"Hiragino Sans","Noto Sans JP",sans-serif}
header{padding:12px 16px 0}.sumwrap{overflow-x:auto}
h1{font-size:18px;margin:0 0 4px}main{padding:12px 16px}.meta{color:var(--mut);font-size:12px;margin:4px 0}
h2.sec{font-size:15px;margin:18px 0 6px;padding-bottom:4px;border-bottom:2px solid var(--line)}
table.sum{border-collapse:collapse;margin:6px 0}table.sum td,table.sum th{border:1px solid var(--line);padding:2px 10px;text-align:right}
table.sum .st-open{background:var(--open-bg);color:var(--open-fg);font-weight:600}
.controls{display:flex;flex-wrap:wrap;gap:8px;align-items:center;position:sticky;top:0;z-index:2;background:var(--bg);border-bottom:1px solid var(--line);padding:8px 16px}.controls input[type=search]{flex:1;min-width:200px;padding:4px 8px}
details.rule{border:1px solid var(--line);border-radius:6px;margin:6px 0;background:var(--card)}
details.rule.is-open{border-left:5px solid var(--open-fg)}
details.rule.is-settled{opacity:.72}details.rule.is-settled[open]{opacity:1}
details.rule>summary{cursor:pointer;padding:6px 10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.body{padding:0 10px 10px;overflow-x:auto}.rid{color:var(--mut);font-size:12px}.cnt{color:var(--mut);font-size:12px;margin-left:auto}
.risk{display:inline-block;min-width:92px;text-align:center;border-radius:4px;color:#fff;font-size:12px;padding:1px 6px}
.r3{background:var(--hi)}.r2{background:var(--md)}.r1{background:var(--lo);color:#222}.r0{background:var(--in)}.r9{background:var(--fp)}
.chip{display:inline-block;border-radius:999px;font-size:11.5px;padding:0 8px;white-space:nowrap;border:1px solid transparent}
.chip.open{background:var(--open-bg);color:var(--open-fg);border-color:var(--open-bd);font-weight:700}
.chip.excluded{background:var(--ex-bg);color:var(--ex-fg)}.chip.known{background:var(--kn-bg);color:var(--kn-fg)}
.chip.done{background:var(--done-bg);color:var(--done-fg);font-weight:700}
table.ins{border-collapse:collapse;width:100%;font-size:12px}table.ins th,table.ins td{border-bottom:1px solid var(--line);padding:3px 6px;text-align:left;vertical-align:top}
tr.st-open td{background:var(--open-bg)}tr.st-excluded,tr.st-known{color:var(--mut)}
td.url{word-break:break-all;min-width:220px}td.sample{max-width:380px;word-break:break-all}td.why{min-width:220px;max-width:420px}
td.why .note{color:var(--note)}code{background:var(--code);padding:0 3px;border-radius:3px}
textarea.memo{width:100%;min-height:2.2em;margin:4px 0;background:var(--bg);color:var(--fg);border:1px solid var(--line)}
.desc,.sol{margin:6px 0}details.info>summary{cursor:pointer;color:var(--mut);font-size:12px;margin:4px 0}.hidden{display:none}
'''

JS = r'''
const KEY='zap-review:'+document.title;let st={};try{st=JSON.parse(localStorage.getItem(KEY)||'{}')}catch(e){}
const save=()=>{try{localStorage.setItem(KEY,JSON.stringify(st))}catch(e){}};
document.querySelectorAll('select.verdict').forEach(s=>{const r=s.dataset.rule;s.value=(st[r]||{}).v||'';s.onchange=()=>{(st[r]=st[r]||{}).v=s.value;save();apply()}});
document.querySelectorAll('textarea.memo').forEach(t=>{const r=t.dataset.rule;t.value=(st[r]||{}).m||'';t.oninput=()=>{(st[r]=st[r]||{}).m=t.value;save()}});
const $=id=>document.getElementById(id);
function apply(){const min=+$('frisk').value,hide=$('fhide').checked,und=$('fundecided').checked,q=$('fq').value.trim().toLowerCase();
document.querySelectorAll('details.rule[data-rule]').forEach(d=>{let ok=+d.dataset.risk>=min||+d.dataset.risk<0&&min==0;
if(hide&&d.dataset.settled=='1')ok=false;
if(und&&(st[d.dataset.rule]||{}).v)ok=false;
const name=d.querySelector('summary b').textContent.toLowerCase();let any=false;
d.querySelectorAll('tbody tr').forEach(tr=>{let v=!(hide&&!tr.classList.contains('st-open'));if(v&&q&&!name.includes(q)&&!tr.dataset.text.includes(q))v=false;tr.classList.toggle('hidden',!v);any=any||v});
d.classList.toggle('hidden',!ok||!any)});
const sec=document.querySelector('h2.settled-sec');if(sec)sec.classList.toggle('hidden',hide)}
['frisk','fhide','fundecided','fq'].forEach(i=>$(i).addEventListener('input',apply));
$('expand').onclick=()=>document.querySelectorAll('details.rule:not(.hidden)').forEach(d=>d.open=true);
$('collapse').onclick=()=>document.querySelectorAll('details.rule').forEach(d=>d.open=false);
$('csv').onclick=()=>{const q=s=>'"'+String(s).replace(/"/g,'""')+'"';const rows=[['rule','name','risk','state','verdict','memo']];
document.querySelectorAll('details.rule[data-rule]').forEach(d=>{const r=d.dataset.rule;rows.push([r,d.querySelector('summary b').textContent,d.querySelector('.risk').textContent,d.dataset.settled=='1'?'判断済み':'要確認',(st[r]||{}).v||'',(st[r]||{}).m||''])});
const a=document.createElement('a');a.href=URL.createObjectURL(new Blob(['﻿'+rows.map(r=>r.map(q).join(',')).join('\n')],{type:'text/csv'}));a.download='zap-review.csv';a.click()};
apply();
'''

if __name__ == '__main__':
    main()
