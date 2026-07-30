---
name: eccube-asset
description: EC-CUBE 4.4 のフロントエンドアセット（SCSS / JS バンドル）をビルド・改修するときの規約。「スタイルを変えて」「CSSを直して」「scssを編集して」「デザインを調整して」「JSを追加して」「アセットをビルドして」などと言われたとき、または html/template 配下の scss・js/bundle.js・webpack.config.js・gulpfile.js・gulp/ を作成・編集するときに使用する。生成物（css / min.css / map / html/bundle）は git 管理されているため、ソースだけコミットすると実機に反映されない。
---

# アセットビルド規約（EC-CUBE 4.4）

## 対象

- **ソース**: `html/template/{default,admin,install}/assets/scss/**/*.scss`, `html/template/*/assets/js/bundle.js`
- **ビルド定義**: `gulpfile.js`, `gulp/config.js`, `gulp/build/`, `gulp/task/`, `webpack.config.js`, `package.json`
- **生成物（すべて git 管理下）**: `html/template/*/assets/css/*.css` / `*.min.css` / `*.map`, `html/bundle/`

## 基本ルール

- **生成物は git 管理されている。ソースだけコミットしても実機には反映されない。**
  `.scss` を変更したら必ずビルドし、生成された `css/` 配下も同じコミットに含める。
- **`css/` 配下を直接編集しない。** 次のビルドで上書きされて消える。変更は必ず `scss/` 側へ。
- **`.map` も追跡対象**（`style.css.map` 等）。生成物一式をコミットする。
- ビルドは `npm run build`（= gulp の既定タスク）。中身は `series(scss, scss-min, webpack)` で、
  1 回で `.css` → `.min.css` → JS バンドルまで通る。個別タスクだけを流して片方だけ更新しない。
- **CI は生成物の鮮度を検査しない。** ソースと生成物の不一致は自動検出されないため、
  コミット前に自分で `git status` を確認する（下記「実行・確認方法」）。

### 変換パイプライン（`gulp/task/` の実装）

| タスク | 入力 | 出力 | 処理 |
|---|---|---|---|
| `scss` | `html/template/**/scss/**/*.scss` | 同階層の `css/` へ `*.css` ＋ `*.css.map` | sass → postcss（`postcss-import` / `autoprefixer` / `postcss-sort-media-queries`（mobile-first）） |
| `scss-min` | 同上 | 同階層の `css/` へ `*.min.css` ＋ `*.min.css.map` | 上記 ＋ `gulp-clean-css` |
| `webpack` | `html/template/{default,admin,install}/assets/js/bundle.js` | `html/bundle/{front,admin,install}.bundle.js` ＋ `.map` ＋ `.LICENSE.txt` | webpack（`mode: production`, `devtool: source-map`） |

`postcss-sort-media-queries` が `@media` を **mobile-first 順に並べ替え・統合**する点が重要。
手書きで `css` の末尾に `@media` を足した差分は、この並べ替えを通っていないため一目で判別できる。
レビューで「フルビルドか手書き追記か」を見分けるときはここを見る。

## 実装パターン

### スタイルを変える

```bash
# 1. scss を編集（例: 店頭）
#    html/template/default/assets/scss/project/_15.1.cart.scss
# 2. ビルド（Docker 環境。ホストに node がある場合は npm ci && npm run build）
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml \
  run --rm -T nodejs npm ci
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml \
  run --rm -T nodejs npm run build
# 3. 生成物を含めてコミット
git status   # scss と css/*.css *.min.css *.map が両方出ていることを確認
```

新しい部分ファイルを足すときは、エントリ（`style.scss` / `app.scss`）の `@import` へ追加する。
エントリから辿れない `.scss` はビルド対象にならない。

### JS を足す

エントリは 3 つ（`front` / `admin` / `install`）で、それぞれ `assets/js/bundle.js` が入口。
新しいスクリプトはこの `bundle.js` から `import` / `require` する。`webpack.config.js` の
`entry` を増やすのは、新しい画面区分を作るときだけ。

## よくある間違い

- ❌ `.scss` だけ変更してコミットする → ✅ 生成物（`css/*.css` `*.min.css` `*.map`）も同じコミットに含める。含めないと実機のスタイルが変わらない
- ❌ 反映されないので `css/style.css` を直接編集する → ✅ 次のビルドで消える。`scss/` を直して再ビルドする
- ❌ 「E2E が緑だから生成物は最新」と判断する → ✅ E2E のワークフローは自分で `npm run build` するため、コミット済み生成物が古くても緑になる
- ❌ `scss` タスクだけ流して `.min.css` を古いまま残す → ✅ `npm run build` で `scss` / `scss-min` / `webpack` を通す
- ❌ 生成された `css` に手で `@media` を追記する → ✅ `postcss-sort-media-queries` の並べ替えを通らず、次のビルドで消える
- ❌ 生成物のパーミッション差分（`.css` は 100755 / `.map` は 100644）に気づかずモードだけ変えてコミットする → ✅ `git diff` でモード変更が出たら戻す
- ❌ エントリ（`style.scss` / `app.scss`）へ `@import` せずに部分ファイルだけ追加する → ✅ 辿れない `.scss` はビルドされない
- ❌ ホストとコンテナでビルドを混在させ、sass のバージョン差で無関係な行まで差分が出る → ✅ どちらかに統一する（Docker 推奨）

## 実行・確認方法

```bash
# ビルド（Docker 環境。AGENTS.md「アセットビルド」の手順）
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml \
  run --rm -T nodejs npm ci
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml \
  run --rm -T nodejs npm run build

# 生成物の取りこぼしが無いか（scss を触ったのに css が出ていなければビルド漏れ）
git status --short html/template html/bundle

# モードだけの差分が混ざっていないか
git diff --summary | grep 'mode change' || echo 'モード変更なし'
```

- 生成物に**想定外の広範囲な差分**が出たときは、ホスト / コンテナのビルド環境差か依存更新を疑う。
  意図した変更だけが出ているかを `git diff --stat` で確認してからコミットする。
- 実機での確認は `bin/console cache:clear` 後にブラウザのキャッシュを無効化して表示する。
