/-
  税率ごとの税額（`Order::getTaxByTaxRate()`）のモデルと証明。

  インボイス制度では、消費税額の端数処理は 1 枚の適格請求書につき税率ごとに 1 回とされている。
  EC-CUBE は税率ごとの税込合計（`getTotalByTaxRate()`。整数の円）から税額を割り戻し、
  税率ごとに 1 回だけ丸める。この値は `PurchaseFlow::calculateTax()` で受注の消費税合計になり、
  納品書（`OrderPdfService`）にも表示される。

  現行の PHP 実装（`src/Eccube/Entity/Order.php`）:

  ```php
  // totalPrice * (rate / (100 + rate))
  $taxRate = bcdiv($rateStr, bcadd('100', $rateStr, 6), 6);
  $value = bcmul($totalPrice, $taxRate, 6);
  $tax[$rate] = TaxRuleService::roundByRoundingType($value, ...);
  ```

  比率 `rate / (100 + rate)` を先に小数 6 桁で切り捨てるため、`value` は真値より小さくなる。
  真値がちょうど整数のとき（税込合計が 110 円・税率 10% など）に切り捨てで 1 円少なくなる。
-/
import Eccube.Tax.Rounding

namespace Eccube.Tax

/-- 現行実装のモデル。`t` は税込合計（円、整数）。比率を単位 10^-6 で切り捨ててから掛ける。 -/
def taxByRateCurrent (rt : RoundingType) (t r : Int) : Int :=
  let ratio6 := truncDiv (r * 10 ^ 6) (100 + r)
  roundScaled rt (t * ratio6) 6

/-- 修正案のモデル。先に掛けてから割る:
    `bcdiv(bcmul($totalPrice, $rateStr, 6), bcadd('100', $rateStr, 6), 6)`。 -/
def taxByRateFixed (rt : RoundingType) (t r : Int) : Int :=
  roundScaled rt (truncDiv (t * r * 10 ^ 6) (100 + r)) 6

/-- 仕様: 税込合計から割り戻した税額の真値 `t * r / (100 + r)` を 1 回だけ丸める。 -/
def taxByRateSpec (rt : RoundingType) (t r : Int) : Int :=
  roundDiv rt (t * r) (100 + r)

/-- **反例（不具合）**: 切り捨て設定・税率 10%・税込合計 110 円で、税額が 10 円ではなく 9 円になる。 -/
theorem taxByRateCurrent_floor_110 :
    taxByRateCurrent .floor 110 10 = 9 ∧ taxByRateSpec .floor 110 10 = 10 := by
  decide

/-- **反例（不具合）**: 軽減税率 8% でも、税込合計 108 円で税額が 8 円ではなく 7 円になる。 -/
theorem taxByRateCurrent_floor_108 :
    taxByRateCurrent .floor 108 8 = 7 ∧ taxByRateSpec .floor 108 8 = 8 := by
  decide

/-- **反例（不具合）**: 四捨五入でも、合計が大きいと誤差が 0.5 円の境界を越える。
    税率 10%・税込合計 500,011 円: 真値 45,455.54… の四捨五入は 45,456 だが 45,455 になる
    （税率 10% で起きる最小の合計。`#eval` で 0〜2,000,000 円を探索した）。 -/
theorem taxByRateCurrent_round_500011 :
    taxByRateCurrent .round 500011 10 = 45455 ∧ taxByRateSpec .round 500011 10 = 45456 := by
  decide

/-- **反例（不具合）**: 切り上げでは、税率 10%・税込合計 1,000,000 円で 90,910 円ではなく 90,909 円になる。 -/
theorem taxByRateCurrent_ceil_1000000 :
    taxByRateCurrent .ceil 1000000 10 = 90909 ∧ taxByRateSpec .ceil 1000000 10 = 90910 := by
  decide

/-- 証明で使う簡約。 -/
macro "tax_by_rate_unfold" : tactic =>
  `(tactic| simp only [taxByRateFixed, taxByRateSpec, roundScaled, roundDiv,
      roundHalfAwayDiv, floorDiv, ceilDiv, truncDiv, Int.reduceAdd, Int.reduceMul, Int.reducePow,
      Int.reduceNeg])

/-- **定理 4**: 修正案は、税率 0 / 3 / 5 / 8 / 10% のすべての税込合計（負の値を含む）と端数処理で、
    真値を 1 回だけ丸めた値と一致する。 -/
theorem taxByRateFixed_eq_spec (rt : RoundingType) (t r : Int)
    (hr : r = 0 ∨ r = 3 ∨ r = 5 ∨ r = 8 ∨ r = 10) :
    taxByRateFixed rt t r = taxByRateSpec rt t r := by
  rcases hr with rfl | rfl | rfl | rfl | rfl <;> cases rt <;> tax_by_rate_unfold <;>
    (repeat' split) <;> omega

end Eccube.Tax
