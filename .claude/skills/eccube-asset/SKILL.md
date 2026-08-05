---
name: eccube-asset
description: EC-CUBE 4.4 のフロントエンドアセット（SCSS / JS バンドル）をビルド・改修するときの規約。「スタイルを変えて」「CSSを直して」「scssを編集して」「デザインを調整して」「JSを追加して」「アセットをビルドして」などと言われたとき、または html/template 配下の scss・js/bundle.js・esbuild.config.mjs を作成・編集するときに使用する。生成物（css / min.css / map / html/bundle）は git 管理されているため、ソースだけコミットすると実機に反映されない。
---

# アセットビルド規約（EC-CUBE 4.4）

## 対象

- **ソース**: `html/template/{default,admin,install}/assets/scss/**/*.scss`, `html/template/*/assets/js/bundle.js`
- **ビルド定義**: `esbuild.config.mjs`, `package.json`
- **生成物（すべて git 管理下）**: `html/template/*/assets/css/*.css` / `*.min.css` / `*.map`, `html/bundle/*.bundle.js` / `*.map`, `html/bundle/ace/`

## 基本ルール

- **生成物は git 管理されている。ソースだけコミットしても実機には反映されない。**
  `.scss` を変更したら必ずビルドし、生成された `css/` 配下も同じコミットに含める。
- **`css/` 配下を直接編集しない。** 次のビルドで上書きされて消える。変更は必ず `scss/` 側へ。
- **`.map` も追跡対象**（`style.css.map` 等）。生成物一式をコミットする。
- ビルドは `npm run build`（= `node esbuild.config.mjs`）。**1 回で SCSS → `.css` / `.min.css` と
  JS バンドルまで通る**ので、片方だけ更新されることはない。監視ビルドは `npm start`（`--watch`）。
- **CI は生成物の鮮度を検査しない。** ソースと生成物の不一致は自動検出されないため、
  コミット前に自分で `git status` を確認する（下記「実行・確認方法」）。

### 変換パイプライン（`esbuild.config.mjs` の実装）

| 処理 | 入力 | 出力 | 内容 |
|---|---|---|---|
| SCSS | `html/template` 配下を再帰探索して見つけた `_` 始まり**でない** `*.scss` | 同階層の `css/` へ `*.css` ＋ `*.min.css` ＋ 各 `.map` | `sass-embedded` → postcss（`autoprefixer` / `postcss-sort-media-queries`（mobile-first））。1 エントリを expanded と compressed で 2 回ビルドする |
| JS | `html/template/{default,admin,install}/assets/js/bundle.js` | `html/bundle/{front,admin,install}.bundle.js` ＋ `.map` | esbuild（`bundle` / `minify` / `target: es2018` / `sourcemap`）。CommonJS 対応で `global` を `window` に読み替える |
| 画像・フォント | JS / CSS から参照される `.png` `.svg` `.woff2` 等 | バンドルに埋め込み | `dataurl` ローダ（webpack の url-loader 相当） |
| ace エディタ | `node_modules/ace-builds/src-min-noconflict` | `html/bundle/ace/` | **使うものだけの allowlist** をコピー（毎回ディレクトリを作り直す） |

押さえるべき点が 3 つある。

- **SCSS のエントリは自動検出**。`html/template` 配下を歩いて `_` で始まらない `.scss` を全部エントリ扱いする。
  つまり**部分ファイルは必ず `_` 始まりにする**。忘れると単独の CSS として出力され、余計な生成物が増える。
- **CSS は JS から `<style>` として注入される**（`esbuild.config.mjs` の `style-inject` プラグイン）。
  webpack の style-loader と同じ挙動なので、テンプレート側の読み込み方を変える必要はない。
- **`postcss-sort-media-queries` が `@media` を mobile-first 順に並べ替え・統合する**。
  手書きで `css` の末尾に `@media` を足した差分はこの並べ替えを通っていないため一目で判別できる。
  レビューで「フルビルドか手書き追記か」を見分けるときはここを見る。

### source map の sources は相対パスに正規化される

`sass-embedded` は `sources` を `file://` の絶対 URL で返す。**生成物をコミットする運用では
ビルドマシンのパスが焼き込まれ、誰が再ビルドしても map だけが差分化する**ため、
`esbuild.config.mjs` は map ファイルからの相対パス（区切りは POSIX 固定）へ直してから書き出している。
map に絶対パスや `file://` が現れたら、この正規化を通っていない（手で書き換えた等）疑いがある。

## 実装パターン

### スタイルを変える

```bash
# 1. scss を編集（例: 店頭）
#    html/template/default/assets/scss/project/_15.1.cart.scss
# 2. ビルド（Docker 環境。ホストに node があれば npm ci && npm run build でもよい）
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml \
  run --rm -T nodejs npm ci
docker compose -f docker-compose.yml -f docker-compose.dev.yml -f docker-compose.nodejs.yml \
  run --rm -T nodejs npm run build
# 3. 生成物を含めてコミット
git status   # scss と css/*.css *.min.css *.map が両方出ていることを確認
```

新しい部分ファイルを足すときは `_` 始まりの名前にし、エントリ（`style.scss` / `app.scss`）から `@import` する。

### JS を足す

エントリは 3 つ（`front` / `admin` / `install`）で、それぞれ `assets/js/bundle.js` が入口。
新しいスクリプトはこの `bundle.js` から `import` / `require` する。
`esbuild.config.mjs` の `entryPoints` を増やすのは、新しい画面区分を作るときだけ。

### ace エディタのモードを増やす

`esbuild.config.mjs` の `aceFiles` に **allowlist として列挙されているファイルだけ**が
`html/bundle/ace/` へ配置される（既定は `mode-twig` / `mode-css` / `mode-javascript` と worker 3 種ほか）。
新しいモードやテーマを使うテンプレートを追加したら、この配列にも追加する。
**足し忘れはビルドでは検出されず、その画面を開いたときに実行時エラーになる**。

## よくある間違い

- ❌ `.scss` だけ変更してコミットする → ✅ 生成物（`css/*.css` `*.min.css` `*.map`）も同じコミットに含める。含めないと実機のスタイルが変わらない
- ❌ 反映されないので `css/style.css` を直接編集する → ✅ 次のビルドで消える。`scss/` を直して再ビルドする
- ❌ 「E2E が緑だから生成物は最新」と判断する → ✅ E2E のワークフローは自分で `npm run build` するため、コミット済み生成物が古くても緑になる
- ❌ 部分ファイルを `_` 始まりにしない → ✅ SCSS エントリは自動検出なので、`_` を付けないと単独の CSS として出力され余計な生成物が増える
- ❌ 生成された `css` に手で `@media` を追記する → ✅ `postcss-sort-media-queries` の並べ替えを通らず、次のビルドで消える
- ❌ ace のモードを増やすテンプレートを足したのに `aceFiles` を更新しない → ✅ ビルドは通り、その画面を開いたときだけ壊れる
- ❌ 生成物のパーミッション差分（`.css` は 100755 / `.map` は 100644）に気づかずモードだけ変えてコミットする → ✅ `git diff` でモード変更が出たら戻す
- ❌ ホストとコンテナでビルドを混在させ、sass のバージョン差で無関係な行まで差分が出る → ✅ どちらかに統一する（Docker 推奨）
- ❌ Bootstrap の dist CSS（`node_modules` 由来の生成物）に対する lint 指摘をそのまま直そうとする → ✅ 例えば `:not(:-moz-placeholder)` を「廃止予定で機能しない」とする指摘は誤検知で、Bootstrap は `:-moz-placeholder` と `:placeholder-shown` を意図的に別ルールセットへ分割出力しており実挙動は後者で正常。近傍に両方あるかを grep で確認する。そもそもビルド生成物なので手編集してはいけない

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
