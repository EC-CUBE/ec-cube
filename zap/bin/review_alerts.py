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
  - 除外は ZAP が付けた判定 (options.properties のフィルターで confidence が False Positive になったもの) と、
    check_results.sh と同じく根拠の無い Path Traversal。フィルターに当てはまるのに ZAP が外していないものは
    「フィルター #N が効いていない」と出す。除外の理由は、フィルターの直前のコメント
  - zap/known_alerts.tsv に当てはまるもの (仕様・誤検知と判断済み) は「既知」として理由を出す
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
        'count': 0, 'targets': set(), 'labels': set(), 'risk': set(), 'conf': set(),
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
                # 除外は ZAP が実際に付けた判定で決める (フィルターが効くと confidence が False Positive になる)。
                # check_results.sh と同じく、根拠の無い Path Traversal も High の判定から外している
                if a.get('confidence') == 0:
                    comment = dict(filters).get(fid, {}).get('comment') if fid is not None else ''
                    excluded = 'フィルター #%s%s' % (fid if fid is not None else '?', ': ' + comment if comment else '')
                elif rid == 6 and not a.get('evidence'):
                    excluded = '根拠なしの Path Traversal'
                else:
                    excluded = None
                if fid is not None and a.get('confidence') != 0:
                    ins_note = 'フィルター #%d が効いていない' % fid
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
                if len(ins['samples']) < 2 and (a.get('attack') or a.get('evidence')):
                    sample = (a.get('attack') or '', a.get('evidence') or '')
                    if sample not in ins['samples']:
                        ins['samples'].append(sample)
                risk_counts[label]['ex' if excluded else a.get('risk')] += 1
                tcount['ex' if excluded else a.get('risk')] += 1

    labels = [s.partition('=')[0] for s in args.sources]
    rows = []
    for rid, ins in instances.items():
        variants = rules.get(rid, {}).get('variants', {})
        risk = max(r for i in ins.values() for r in i['risk'])
        total = sum(i['count'] for i in ins.values())
        unfiltered = sum(i['count'] - sum(i['filtered'].values()) for i in ins.values())
        nknown = sum(sum(i['known'].values()) for i in ins.values())
        rows.append((risk, total, rid, variants, ins, unfiltered, nknown))
    rows.sort(key=lambda r: (-r[0], -r[5], r[2]))

    e = html.escape
    out = []
    w = out.append
    w('<!doctype html><html lang="ja"><head><meta charset="utf-8">')
    w('<meta name="viewport" content="width=device-width, initial-scale=1">')
    w('<title>%s</title><style>%s</style></head><body>' % (e(args.title), CSS))
    w('<header><h1>%s</h1>' % e(args.title))
    w('<p class="meta">入力: %s</p>' % ' / '.join(
        '%s (%d ターゲット)' % (e(l), len(targets[l])) for l in labels))
    w('<table class="sum"><tr><th>リスク</th>%s<th>ルール数</th></tr>' % ''.join('<th>%s</th>' % e(l) for l in labels))
    for rk in (3, 2, 1, 0, -1):
        nrules = sum(1 for r in rows if r[0] == rk)
        if not nrules and not any(risk_counts[l][rk] for l in labels):
            continue
        w('<tr><td><span class="risk r%d">%s</span></td>%s<td>%d</td></tr>' % (
            rk if rk >= 0 else 9, RISK[rk], ''.join('<td>%d</td>' % risk_counts[l][rk] for l in labels), nrules))
    w('<tr><td>除外</td>%s<td></td></tr>' % ''.join('<td>%d</td>' % risk_counts[l]['ex'] for l in labels))
    w('</table>')
    w('<p class="meta">件数は ZAP が出したアラートの件数 (同じ画面・項目で重複を含む)。'
      '「除外」は zap/options.properties の誤検知フィルター、または check_results.sh が High の判定から外すもの。'
      '「既知」は zap/known_alerts.tsv で仕様・誤検知と判断済みのもの (理由は zap/README.md の「既知のアラート」)。'
      '判定とメモはこのブラウザの localStorage に保存され、「CSV で書き出す」で取り出せます。</p>')
    w('<div class="controls"><label>リスク <select id="frisk"><option value="0">Informational 以上</option>'
      '<option value="1">Low 以上</option><option value="2">Medium 以上</option><option value="3">High</option></select></label>'
      '<label><input type="checkbox" id="fhide"> 除外・既知の行を隠す</label>'
      '<label><input type="checkbox" id="fundecided"> 未判定のルールだけ (すべて既知のルールを除く)</label>'
      '<input type="search" id="fq" placeholder="ルール名・URL・ターゲットで絞り込み">'
      '<button id="expand">すべて開く</button><button id="collapse">すべて閉じる</button>'
      '<button id="csv">CSV で書き出す</button></div></header><main>')

    for risk, total, rid, variants, ins, unfiltered, nknown in rows:
        names = sorted({v['name'] for v in variants.values() if v['name']}) or ['(名前不明: ルール %d)' % rid]
        v0 = next(iter(variants.values()), {})
        allknown = nknown == total
        w('<details class="rule" data-risk="%d" data-rule="%d" data-known="%d">' % (risk, rid, 1 if allknown else 0))
        w('<summary><span class="risk r%d">%s</span> <b>%s</b> <span class="rid">%d</span>'
          ' <span class="cnt">%d 件 / %d 箇所%s</span>'
          ' <select class="verdict" data-rule="%d" onclick="event.stopPropagation()">'
          '<option value="">未判定</option><option>対応要</option><option>要調査</option>'
          '<option>誤検知</option><option>許容</option><option>情報のみ</option></select></summary>' % (
              risk if risk >= 0 else 9, RISK.get(risk, risk), e(' / '.join(names)), rid, total, len(ins),
              ''.join(x for x in (
                  '' if unfiltered == total else ' (うち除外 %d 件)' % (total - unfiltered),
                  '' if not nknown else (' すべて既知' if allknown else ' (うち既知 %d 件)' % nknown))), rid))
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
            meta += ['<a href="https://www.zaproxy.org/docs/alerts/%s/" rel="noreferrer">%s</a>' % (e(r), e(r)) for r in refs]
            if v0.get('cwe'):
                meta.append('CWE-%s' % e(v0['cwe']))
            if v0.get('wasc'):
                meta.append('WASC-%s' % e(v0['wasc']))
            w('<p class="meta">%s</p>' % ' · '.join(meta))
        w('<textarea class="memo" data-rule="%d" placeholder="メモ"></textarea>' % rid)
        w('<table class="ins"><thead><tr><th>メソッド</th><th>URL (正規化)</th><th>パラメータ</th><th>件数</th>'
          '<th>%s</th><th>ターゲット</th><th>除外</th><th>既知 (zap/known_alerts.tsv)</th><th>攻撃 / 根拠 (例)</th></tr></thead><tbody>' % '/'.join(e(l) for l in labels))
        for (method, url, param), i in sorted(ins.items(), key=lambda kv: (-kv[1]['count'], kv[0])):
            fcount = sum(i['filtered'].values())
            settled = fcount + sum(i['known'].values()) >= i['count']
            tg = sorted(i['targets'])
            tgs = ', '.join(e(t) for t in tg[:6]) + (' ほか %d' % (len(tg) - 6) if len(tg) > 6 else '')
            samples = ''.join('<div><code>%s</code>%s</div>' % (
                e(a[:300]) or '—', (' → <code>%s</code>' % e(ev[:300])) if ev else '') for a, ev in i['samples'])
            w('<tr class="%s" data-text="%s"><td>%s</td><td class="url">%s</td><td>%s</td><td>%d</td><td>%s</td>'
              '<td>%s</td><td>%s</td><td>%s</td><td class="sample">%s</td></tr>' % (
                  'fonly' if settled else '', e(' '.join([url, param] + tg).lower()), e(method), e(url), e(param),
                  i['count'], ' '.join('●' if l in i['labels'] else '·' for l in labels), tgs,
                  e(', '.join('%s %d' % kv for kv in list(i['filtered'].items()) + list(i['notes'].items()))),
                  e(' / '.join(k for k in i['known'])) + ('' if sum(i['known'].values()) in (0, i['count']) else ' (一部)'), samples))
        w('</tbody></table></div></details>')
    w('<details class="rule"><summary><b>ターゲット別の結果</b> <span class="cnt">%d ターゲット</span></summary><div class="body">' % len(status))
    w('<p class="meta">再生の失敗は check_results.sh と同じ数え方 (passed = false / Assign: failed / Form error)。High〜Info は除外を除いた件数。</p>')
    w('<table class="ins"><thead><tr><th>ターゲット</th>%s</tr></thead><tbody>' % ''.join(
        '<th>%s: プラン</th><th>再生の失敗</th><th>High / Med / Low / Info / 除外</th>' % e(l) for l in labels))
    for tgt in sorted(status):
        cells = ''
        for l in labels:
            if l not in status[tgt]:
                cells += '<td>—</td><td></td><td></td>'
                continue
            plan, fails, total, c = status[tgt][l]
            cells += '<td>%s</td><td>%d / %d</td><td>%d / %d / %d / %d / %d</td>' % (
                e(plan), fails, total, c[3], c[2], c[1], c[0], c['ex'])
        w('<tr data-text="%s"><td>%s</td>%s</tr>' % (e(tgt), e(tgt), cells))
    w('</tbody></table></div></details>')
    w('</main><script>%s</script></body></html>' % JS)
    with open(args.output, 'w', encoding='utf-8') as fh:
        fh.write('\n'.join(out))
    print('%s: %d rules, %d instances' % (args.output, len(rows), sum(len(r[4]) for r in rows)))


CSS = r'''
:root{--bg:#fff;--fg:#1d1d1f;--mut:#6b6b70;--line:#e2e2e6;--card:#f7f7f9;--hi:#c62828;--md:#e8710a;--lo:#c9a400;--in:#1a73e8;--fp:#888;--code:#f1f1f4}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){--bg:#17171a;--fg:#e8e8ea;--mut:#9a9aa2;--line:#33333a;--card:#1f1f24;--code:#26262c}}
:root[data-theme="dark"]{--bg:#17171a;--fg:#e8e8ea;--mut:#9a9aa2;--line:#33333a;--card:#1f1f24;--code:#26262c}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--fg);font:14px/1.55 system-ui,-apple-system,"Hiragino Sans","Noto Sans JP",sans-serif}
header{position:sticky;top:0;background:var(--bg);border-bottom:1px solid var(--line);padding:12px 16px;z-index:2}
h1{font-size:18px;margin:0 0 4px}main{padding:12px 16px}.meta{color:var(--mut);font-size:12px;margin:4px 0}
table.sum{border-collapse:collapse;margin:6px 0}table.sum td,table.sum th{border:1px solid var(--line);padding:2px 10px;text-align:right}
.controls{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-top:6px}.controls input[type=search]{flex:1;min-width:200px;padding:4px 8px}
details.rule{border:1px solid var(--line);border-radius:6px;margin:6px 0;background:var(--card)}
details.rule>summary{cursor:pointer;padding:6px 10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.body{padding:0 10px 10px;overflow-x:auto}.rid{color:var(--mut);font-size:12px}.cnt{color:var(--mut);font-size:12px;margin-left:auto}
.risk{display:inline-block;min-width:92px;text-align:center;border-radius:4px;color:#fff;font-size:12px;padding:1px 6px}
.r3{background:var(--hi)}.r2{background:var(--md)}.r1{background:var(--lo);color:#222}.r0{background:var(--in)}.r9{background:var(--fp)}
table.ins{border-collapse:collapse;width:100%;font-size:12px}table.ins th,table.ins td{border-bottom:1px solid var(--line);padding:3px 6px;text-align:left;vertical-align:top}
td.url{word-break:break-all;min-width:240px}td.sample{max-width:420px;word-break:break-all}code{background:var(--code);padding:0 3px;border-radius:3px}
tr.fonly{opacity:.5}textarea.memo{width:100%;min-height:2.2em;margin:4px 0;background:var(--bg);color:var(--fg);border:1px solid var(--line)}
.desc,.sol{margin:6px 0}details.info>summary{cursor:pointer;color:var(--mut);font-size:12px;margin:4px 0}.hidden{display:none}
'''

JS = r'''
const KEY='zap-review:'+document.title;let st={};try{st=JSON.parse(localStorage.getItem(KEY)||'{}')}catch(e){}
const save=()=>{try{localStorage.setItem(KEY,JSON.stringify(st))}catch(e){}};
document.querySelectorAll('select.verdict').forEach(s=>{const r=s.dataset.rule;s.value=(st[r]||{}).v||'';s.onchange=()=>{(st[r]=st[r]||{}).v=s.value;save();apply()}});
document.querySelectorAll('textarea.memo').forEach(t=>{const r=t.dataset.rule;t.value=(st[r]||{}).m||'';t.oninput=()=>{(st[r]=st[r]||{}).m=t.value;save()}});
const $=id=>document.getElementById(id);
function apply(){const min=+$('frisk').value,hide=$('fhide').checked,und=$('fundecided').checked,q=$('fq').value.trim().toLowerCase();
document.querySelectorAll('details.rule').forEach(d=>{let ok=+d.dataset.risk>=min||+d.dataset.risk<0&&min==0;
if(und&&((st[d.dataset.rule]||{}).v||d.dataset.known=='1'))ok=false;
const name=d.querySelector('summary b').textContent.toLowerCase();let any=false;
d.querySelectorAll('tbody tr').forEach(tr=>{let v=!(hide&&tr.classList.contains('fonly'));if(v&&q&&!name.includes(q)&&!tr.dataset.text.includes(q))v=false;tr.classList.toggle('hidden',!v);any=any||v});
d.classList.toggle('hidden',!ok||!any)})}
['frisk','fhide','fundecided','fq'].forEach(i=>$(i).addEventListener('input',apply));
$('expand').onclick=()=>document.querySelectorAll('details.rule:not(.hidden)').forEach(d=>d.open=true);
$('collapse').onclick=()=>document.querySelectorAll('details.rule').forEach(d=>d.open=false);
$('csv').onclick=()=>{const q=s=>'"'+String(s).replace(/"/g,'""')+'"';const rows=[['rule','name','risk','verdict','memo']];
document.querySelectorAll('details.rule').forEach(d=>{const r=d.dataset.rule;rows.push([r,d.querySelector('summary b').textContent,d.querySelector('.risk').textContent,(st[r]||{}).v||'',(st[r]||{}).m||''])});
const a=document.createElement('a');a.href=URL.createObjectURL(new Blob(['﻿'+rows.map(r=>r.map(q).join(',')).join('\n')],{type:'text/csv'}));a.download='zap-review.csv';a.click()};
apply();
'''

if __name__ == '__main__':
    main()
