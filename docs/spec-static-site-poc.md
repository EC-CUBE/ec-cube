# 仕様書 静的サイト PoC — ジェネレータ比較（Issue #6906 §8.2）

コード近接の `README.html`（人間向けの真の仕様書）を集約して常時公開する**静的サイト**（戦略資料 §8.2）の
ジェネレータを選定するための PoC 記録。候補は戦略資料が挙げる **Astro Starlight / VitePress / Antora**。

> 本 PR（Phase B）のスコープは「`eccube:docs:export` コマンド」「PoC による方針決定」「鮮度維持（PR テンプレ・CI）」まで。
> 静的サイトの本番構築・ホスティング（`docs.ec-cube.net/spec/`）は本 PoC の結論に基づく後続作業とする。

## 前提となる制約（PoC で判明した最重要事実）

各 `README.html` は **自己完結した完全な HTML 文書**（`<!DOCTYPE html>` … `<head><style>` … `<body>`）である。
一方、主要な SSG はいずれも **Markdown / MDX / AsciiDoc を一次コンテンツ**とし、`<head>`・レイアウト・テーマを
ジェネレータ側が支配する設計になっている。したがって:

- **どの SSG も「素の完結 HTML 文書」をテーマ適用付きの本文としては取り込まない。** 取り込むなら
  (a) 静的アセット（`public/` 等）としてそのまま配信する、(b) iframe で埋め込む、(c) HTML→MD/MDX へ変換する、のいずれか。
- 本仕様書は「自己完結 HTML であること」自体が価値（§8.1：単一ソースで顧客提出 PDF を派生できる）なので、
  **(c) 変換は本末転倒**。現実的には **(a) 静的アセット配信 ＋ SSG は索引・ナビ・検索の layer** に徹する構成が素直。
- この構成なら、`bin/console eccube:docs:export` の出力（集約済み `README.html` 群）を **SSG の `public/` に流し込む**だけでよく、
  SSG 間の差は「索引/ナビ/検索の作り」「導入の重さ」「EC-CUBE の既存ツールチェーンとの親和性」に集約される。

## PoC 実施結果

### VitePress（実際にビルド実行 ✅）

最小構成で PoC を実行し、**ビルド成功**を確認した。

- 手順: `docs/public/spec/` に全 `README.html`（27 ファイル）を相対構造のままコピー → 索引 `index.md` を自動生成 →
  `vitepress build`。
- 結果: **`dist/` に索引 `index.html` が生成され、27 個の `README.html` が自己完結のまま（`<style>`・`<section>`・
  日本語をそのまま保持して）配信された。** 追加依存は 126 パッケージ / インストール約 12 秒、ビルド約 4 秒。
- 所見: EC-CUBE は既に **Node/npm/webpack** を使う（`package.json` あり）ため導入障壁が最も低い。
  索引 `index.md` は `eccube:docs:export` の出力一覧から機械生成でき、鮮度維持と相性が良い。

### Astro Starlight（評価）

- ドキュメントサイト向けにテーマ・全文検索・サイドバー・バージョニングが充実。MDX 一次で表現力は高い。
- 本用途では自己完結 HTML を `public/` 配信する点は VitePress と同じで、**テーマの恩恵は索引側にしか及ばない**。
- 依存・ビルド構成は VitePress より重め。将来「Markdown で書き直した本文にテーマを当てる」方針に転換するなら有力。

### Antora（評価）

- **複数リポジトリ横断・バージョン別**ドキュメントに最強（AsciiDoc 一次）。EC-CUBE 本体＋プラグイン群を
  横断する将来像では魅力的。
- ただし一次コンテンツが **AsciiDoc** で、本仕様書（HTML）とはコンテンツモデルが最も乖離する。導入・運用も最重量。
- 現段階（単一リポジトリ・HTML 集約）ではオーバースペック。

## 比較表

| 観点 | VitePress | Astro Starlight | Antora |
|---|---|---|---|
| 一次コンテンツ | Markdown | MDX/Markdown | AsciiDoc |
| 自己完結 HTML の扱い | `public/` 配信（PoC 実証） | `public/` 配信 | 静的資産配信 |
| 導入の軽さ | ◎（Node、~126 pkg） | ○ | △（最重量） |
| 索引/ナビ/検索 | ○ | ◎（検索標準） | ◎（横断・版管理） |
| EC-CUBE 既存ツールチェーン親和性 | ◎（npm/webpack 既存） | ○ | △ |
| 複数リポジトリ・版管理 | △ | ○ | ◎ |
| 本用途の総合 | **◎（PoC 実証済み）** | ○ | △ |

## 推奨（PoC の結論）

- **第一候補: VitePress。** 自己完結 HTML を `public/` に置く前提では SSG 間の本質差は小さく、
  **導入の軽さと EC-CUBE 既存 Node ツールチェーンとの親和性**で優位。索引は `eccube:docs:export` 出力から機械生成でき、
  本 PoC で **27 ファイル集約 → ビルド成功**を実証済み。
- 全文検索・テーマを重視するなら Astro Starlight、将来プラグイン群まで横断・版管理するなら Antora を再評価する。
- **最終決定は本 PoC を土台にメンテナと合意**し、後続作業で本番サイト（`docs.ec-cube.net/spec/`）を構築する。

## 再現手順（参考）

```bash
# 1) 集約済み README.html を出力
bin/console eccube:docs:export            # var/docs/all/ に全章
bin/console eccube:docs:export --filter=customer   # var/docs/customer/ に顧客向け章のみ

# 2) SSG の public/ に配置して索引を生成 → ビルド（VitePress の例）
#    出力 index.html ＋ 各 README.html が集約された静的サイトが得られる。
#    顧客提出 PDF は出力 HTML をブラウザの「印刷 → PDF」で派生させる。
```

> PoC の VitePress プロジェクト（`node_modules` 含む）はリポジトリにコミットしない使い捨て。
> 本番採用時に構成を確定してから追加する。
