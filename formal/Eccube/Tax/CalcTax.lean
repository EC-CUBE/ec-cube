/-
  明細の税額計算のモデルと証明。

  対応する PHP 実装（`src/Eccube/Service/TaxRuleService.php`）:

  ```php
  // calcTax: 税抜価格から税額を求める
  $tax = bcdiv(bcmul($price, $taxRate, 4), '100', 4);
  $roundTax = self::roundByRoundingType($tax, $RoundingType);
  return bcadd($roundTax, $taxAdjust, 2);

  // calcTaxIncluded: 税込価格から税額を割り戻す
  $priceAfterAdjust = bcsub($price, $taxAdjust, 4);
  $divisor = bcadd('100', $taxRate, 4);
  $tax = bcdiv(bcmul($priceAfterAdjust, $taxRate, 4), $divisor, 4);
  return self::roundByRoundingType($tax, $RoundingType);
  ```

  入力の範囲（DB のカラム定義に合わせる）:
  - 価格 `price`: 小数 2 桁（`decimal(12,2)`）。ここでは単位 10^-2 の整数 `p` で表す
  - 税率 `tax_rate`: 0 以上の整数（`decimal(10,0) unsigned`）。ここでは `r`
  - 調整額 `tax_adjust`: 整数（`decimal(10,0)`）。ここでは `a`
  - 結果: 小数 0 桁の税額（円）

  bcmath の `bcmul` / `bcdiv` は指定の桁数で **0 方向へ切り捨てる**（丸めない）。
  実装モデル（`*Impl`）はこの切り捨てを `truncDiv` で明示的に含み、仕様（`*Spec`）は
  真値を 1 回だけ丸める。両者が一致することが「途中の切り捨てが結果を変えない」ことの証明になる。
-/
import Eccube.Tax.Rounding

namespace Eccube.Tax

/-! ## calcTax（税抜価格から税額） -/

/-- `calcTax` の実装モデル。`bcdiv(bcmul(p, r, 4), 100, 4)` は単位 10^-4 で `truncDiv (p*r*100) 100`。 -/
def calcTaxImpl (rt : RoundingType) (p r a : Int) : Int :=
  let tax4 := truncDiv (p * r * 100) 100
  roundScaled rt tax4 4 + a

/-- `calcTax` の仕様: 税額の真値 `(p / 100) * (r / 100)` を 1 回だけ丸め、調整額を足す。 -/
def calcTaxSpec (rt : RoundingType) (p r a : Int) : Int :=
  roundDiv rt (p * r) 10000 + a

theorem truncDiv_mul_100 (x : Int) : truncDiv (x * 100) 100 = x := by
  unfold truncDiv
  split <;> omega

/-- **定理 1**: すべての価格（小数 2 桁）・税率・調整額・端数処理で、`calcTax` の途中の
    切り捨ては結果を変えない。 -/
theorem calcTaxImpl_eq_spec (rt : RoundingType) (p r a : Int) :
    calcTaxImpl rt p r a = calcTaxSpec rt p r a := by
  simp only [calcTaxImpl, calcTaxSpec, roundScaled]
  rw [show p * r * 100 = (p * r) * 100 by rfl, truncDiv_mul_100]
  rfl

/-! ## calcTaxIncluded（税込価格から税額を割り戻す） -/

/-- `calcTaxIncluded` の実装モデル。
    `bcsub(p, a, 4)` → 単位 10^-2 で `p - 100a`、`bcmul(…, r, 4)` → 単位 10^-4 で `(p - 100a) * r * 100`、
    `bcdiv(…, 100 + r, 4)` → `truncDiv` で 0 方向へ切り捨て。 -/
def calcTaxIncludedImpl (rt : RoundingType) (p r a : Int) : Int :=
  let tax4 := truncDiv ((p - 100 * a) * r * 100) (100 + r)
  roundScaled rt tax4 4

/-- `calcTaxIncluded` の仕様: 税額の真値 `((p / 100) - a) * r / (100 + r)` を 1 回だけ丸める。 -/
def calcTaxIncludedSpec (rt : RoundingType) (p r a : Int) : Int :=
  roundDiv rt ((p - 100 * a) * r) ((100 + r) * 100)

/-- 証明で使う簡約: 税率を具体的な値に置いたあと、定義を展開し、定数の演算を畳み込む。 -/
macro "tax_unfold" : tactic =>
  `(tactic| simp only [calcTaxIncludedImpl, calcTaxIncludedSpec, roundScaled, roundDiv,
      roundHalfAwayDiv, floorDiv, ceilDiv, truncDiv, Int.reduceAdd, Int.reduceMul, Int.reducePow,
      Int.reduceNeg])

/-- **定理 2**: 現行の税率（標準 10%・軽減 8%）と過去の 5%、非課税相当の 0% では、すべての価格
    （小数 2 桁、負の値を含む）・調整額・端数処理で、`calcTaxIncluded` の小数 4 桁での切り捨ては
    結果を変えない。 -/
theorem calcTaxIncludedImpl_eq_spec (rt : RoundingType) (p a r : Int)
    (hr : r = 0 ∨ r = 5 ∨ r = 8 ∨ r = 10) :
    calcTaxIncludedImpl rt p r a = calcTaxIncludedSpec rt p r a := by
  rcases hr with rfl | rfl | rfl | rfl <;> cases rt <;> tax_unfold <;>
    generalize p - 100 * a = q <;> (repeat' split) <;> omega

/-- **定理 3**: 税率 3% でも、価格が整数（円単位）なら切り捨ては結果を変えない。 -/
theorem calcTaxIncludedImpl_eq_spec_rate3_integer (rt : RoundingType) (k a : Int) :
    calcTaxIncludedImpl rt (100 * k) 3 a = calcTaxIncludedSpec rt (100 * k) 3 a := by
  cases rt <;> tax_unfold <;> (repeat' split) <;> omega

/-- **反例**: 税率 3% で価格に小数があると、切り上げの結果が真値の切り上げより 1 小さくなる。
    価格 68.67・税率 3%: 真値 2.000097… の切り上げは 3 だが、小数 4 桁で 2.0000 に切り捨てられて 2 になる
    （PHP の `calcTaxIncluded('68.67', '3', RoundingType::CEIL)` でも 2 を返すことを確認済み）。
    現行の税率では起きない（定理 2）が、税率はマスタで自由に設定できるため境界として記録する。 -/
theorem calcTaxIncluded_counterexample_rate3 :
    calcTaxIncludedImpl .ceil 6867 3 0 = 2 ∧ calcTaxIncludedSpec .ceil 6867 3 0 = 3 := by
  decide

end Eccube.Tax
