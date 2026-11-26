/-
  端数処理のモデル。

  金額は「10^-s 単位の整数」で表す（固定小数点）。例えば小数 4 桁の値 `1.2345` は
  単位 10^-4 で `12345` になる。bcmath は文字列の 10 進数を扱うため、整数への写像で
  誤差なくモデル化できる。

  対応する PHP 実装:
  - `Eccube\Entity\Master\RoundingType` の定数（ROUND = 1 / FLOOR = 2 / CEIL = 3）
  - `Eccube\Service\TaxRuleService::roundByRoundingType()`
    （bcround / bcfloor / bcceil を小数 0 桁で使う。未知の値は切り上げ）
-/
namespace Eccube.Tax

/-- 端数処理の種類。`mtb_rounding_type` の id と対応する。 -/
inductive RoundingType where
  /-- 四捨五入（id = 1）。bcround の既定モード（0.5 は 0 から遠い側へ）。 -/
  | round
  /-- 切り捨て（id = 2）。bcfloor（負の無限大方向）。 -/
  | floor
  /-- 切り上げ（id = 3）。bcceil（正の無限大方向）。 -/
  | ceil
  deriving DecidableEq, Repr

namespace RoundingType

/-- `mtb_rounding_type.id` から端数処理を引く。未知の id は切り上げ（PHP の `default`）。 -/
def ofId : Int → RoundingType
  | 1 => .round
  | 2 => .floor
  | _ => .ceil

def toId : RoundingType → Int
  | .round => 1
  | .floor => 2
  | .ceil => 3

def all : List RoundingType := [.round, .floor, .ceil]

end RoundingType

/-! ### 有理数 `n / m`（`m > 0`）を整数へ丸める

  Lean の `Int` の `/` はユークリッド除法で、除数が正なら床関数（負の無限大方向）になる。
-/

/-- `⌊n / m⌋` -/
def floorDiv (n m : Int) : Int := n / m

/-- `⌈n / m⌉` -/
def ceilDiv (n m : Int) : Int := -((-n) / m)

/-- `n / m` を四捨五入する（0.5 は 0 から遠い側へ。bcround の既定と同じ）。 -/
def roundHalfAwayDiv (n m : Int) : Int :=
  if 0 ≤ n then (2 * n + m) / (2 * m) else -((2 * (-n) + m) / (2 * m))

/-- 0 方向への切り捨て除算。bcdiv / bcmul が指定の桁数で行う切り捨てに当たる。 -/
def truncDiv (n m : Int) : Int :=
  if 0 ≤ n then n / m else -((-n) / m)

/-- 有理数 `n / m` を端数処理で整数へ丸める（仕様側。真値の丸め）。 -/
def roundDiv (rt : RoundingType) (n m : Int) : Int :=
  match rt with
  | .round => roundHalfAwayDiv n m
  | .floor => floorDiv n m
  | .ceil => ceilDiv n m

/-- 単位 `10^-s` の値を小数 0 桁へ丸める（`roundByRoundingType` のモデル）。 -/
def roundScaled (rt : RoundingType) (v : Int) (s : Nat) : Int :=
  roundDiv rt v (10 ^ s)

end Eccube.Tax
