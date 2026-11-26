# formal/ — Lean 4 による形式検証

EC-CUBE の計算ロジックの一部を [Lean 4](https://lean-lang.org/) でモデル化し、性質を証明する。
PHP のコードを Lean へ自動で変換する手段は無いので、次の二段構えにしている。

1. **Lean で実装モデルと仕様を書き、両者が一致することを証明する。**
   実装モデルは bcmath の途中の切り捨てまで写したもの、仕様は真値を 1 回だけ丸めたもの。
2. **実装モデルからテストベクタ（JSON）を生成してコミットし、PHPUnit で PHP 実装と突き合わせる（差分テスト）。**
   モデルが PHP 実装を正しく写していることを、ベクタの範囲で確かめる。

Lean は本体の依存ではない。PHPUnit が読むのはコミット済みの JSON だけなので、Lean の環境が無くても
既存のテストと開発手順は変わらない。`formal/` は配布パッケージに含めない（`package.sh`）。

方針と今後の対象は [#7311](https://github.com/EC-CUBE/ec-cube/issues/7311) を参照。

## 対象と定理

### 税計算（`Eccube/Tax/`）

| ファイル | PHP 実装 | 内容 |
|---|---|---|
| `Rounding.lean` | `TaxRuleService::roundByRoundingType()` | 四捨五入（0.5 は 0 から遠い側へ）・切り捨て・切り上げ。未知の id は切り上げ |
| `CalcTax.lean` | `TaxRuleService::calcTax()` / `calcTaxIncluded()` | 明細の税額 |
| `TaxByRate.lean` | `Order::getTaxByTaxRate()` | 税率ごとの税額（インボイスの税率別消費税額） |

| 定理 | 主張 |
|---|---|
| `calcTaxImpl_eq_spec` | `calcTax` は、すべての価格（小数 2 桁）・税率・調整額・端数処理で真値の丸めと一致する |
| `calcTaxIncludedImpl_eq_spec` | `calcTaxIncluded` は、税率 0 / 5 / 8 / 10% のすべての価格（小数 2 桁、負の値を含む）で真値の丸めと一致する |
| `calcTaxIncludedImpl_eq_spec_rate3_integer` | 税率 3% でも、価格が整数（円）なら一致する |
| `calcTaxIncluded_counterexample_rate3` | 税率 3% で価格に小数があると一致しない（68.67 円・切り上げで 3 ではなく 2）。現行の税率では起きない |
| `taxByRateFixed_eq_spec` | 税率ごとの税額（#7279 の修正後の式）は、税率 0 / 3 / 5 / 8 / 10% のすべての整数の税込合計で真値の丸めと一致する |
| `taxByRateCurrent_*` | #7279 の修正前の式の反例（切り捨て・税込 110 円・10% で 9 円になる等） |

#7279 で報告されていた不具合は、このモデルを作る途中でも再発見された。報告では切り捨て設定に限るとされていたが、
四捨五入・切り上げでも税込合計が大きいと起きることを反例で示している（`taxByRateCurrent_*`）。

## 環境

`lean-toolchain` の版の Lean 4 を使う。Mathlib などの外部の依存は無い。

```bash
# elan（Lean の公式のバージョン管理ツール）を使う場合。lean-toolchain の版が自動で入る
curl https://elan.lean-lang.org/elan-init.sh -sSf | sh

# Nix を使う場合（nixpkgs の lean4 が lean-toolchain と同じ版であること）
nix shell nixpkgs#lean4
```

## 使い方

```bash
cd formal

# 証明を検査する（sorry などの警告もエラーにする）
lake build --wfail

# テストベクタを再生成する（モデルを変えたら必ず実行してコミットする）
lake env lean --run GenVectors.lean > ../tests/Eccube/Tests/Formal/vectors/tax.json

# PHP 実装と突き合わせる（リポジトリのルートで）
cd .. && vendor/bin/phpunit tests/Eccube/Tests/Formal
```

CI では `.github/workflows/formal-verification.yml` が証明の検査とベクタの再生成チェックを行い、
差分テストは `unit-test.yml` が PHP 8.2〜8.5 で実行する（8.2 / 8.3 では bcmath の polyfill が使われる）。

## 書くときの決まり

- `sorry` / `admit` / `native_decide` / `axiom` を使わない（CI で検出する）。`native_decide` はコンパイラを、
  `axiom` は証明の無い仮定を信頼の基盤に加えてしまう。
- 金額は「10^-s 単位の整数」で表す。bcmath は 10 進数の文字列を扱うため、整数への写像で誤差なくモデル化できる。
- bcmath の `bcmul` / `bcdiv` が指定の桁数で行う **0 方向への切り捨て** は、`truncDiv` で明示的にモデルへ含める。
- 各モジュールの先頭に、対応する PHP 実装の場所とコードの抜粋を書く。

## 限界

- 証明できるのは Lean のモデルについてであり、PHP 実装の正しさは差分テストのベクタの範囲に限られる。
- 証明に失敗しても実装の不具合とは限らない。仕様やモデルの誤りであることもある。
- 法令の解釈そのもの（何を真値とするか）は検証の対象外。仕様の各定義は、レビューを受けるための読める仕様書として扱う。
