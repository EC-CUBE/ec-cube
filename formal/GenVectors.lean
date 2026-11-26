/-
  差分テスト用のテストベクタを生成する。

  `lake env lean --run GenVectors.lean > ../tests/Eccube/Tests/Formal/vectors/tax.json`

  期待値は **実装モデル**（`*Impl` / `taxByRateFixed` / `roundScaled`）から計算する。
  PHPUnit はこれと PHP 実装を突き合わせ、モデルが bcmath の挙動を正しく写していることを確かめる。
  モデルと仕様（真値を 1 回だけ丸めた値）の一致は、各定理がその前提を満たすすべての入力について保証する。
-/
import Eccube.Tax.Rounding
import Eccube.Tax.CalcTax
import Eccube.Tax.TaxByRate

open Eccube.Tax

/-- 単位 `10^-s` の整数を 10 進数の文字列にする（例: `fmtScaled 6867 2 = "68.67"`）。 -/
def fmtScaled (v : Int) (s : Nat) : String :=
  let sign := if v < 0 then "-" else ""
  let n := v.natAbs
  if s == 0 then sign ++ toString n
  else
    let base := 10 ^ s
    let frac := toString (n % base)
    let pad := String.ofList (List.replicate (s - frac.length) '0')
    sign ++ toString (n / base) ++ "." ++ pad ++ frac

def jsonStr (s : String) : String := "\"" ++ s ++ "\""

def jsonRow (cols : List String) : String := "[" ++ ",".intercalate cols ++ "]"

def jsonRows (rows : List (List String)) : String :=
  "[\n" ++ ",\n".intercalate (rows.map fun r => "  " ++ jsonRow r) ++ "\n  ]"

def range (lo hi : Int) : List Int :=
  (List.range (hi - lo + 1).toNat).map fun (i : Nat) => lo + (i : Int)

/-! ### 入力の領域

  税額は価格に対して周期的に変わる（税率 10% の割り戻しは 11 円周期、8% は 27 円周期）。
  連続区間は各周期を数周分含む長さにし、端数の境界付近と大きな値を足す。
-/

/-- 整数の円の連続区間（単位 10^-2）。負の値（値引き明細）を含む。 -/
def integerPrices : List Int := (range (-60) 240).map (· * 100)

/-- 小数のある価格（単位 10^-2）: 0.5 の境界の前後と、税率 3% の反例。 -/
def centPrices : List Int :=
  ((range 1 20).flatMap fun yen => [1, 49, 50, 51, 99].map (yen * 100 + ·))
  ++ [6867, -6867, 10101]

/-- 大きな値（桁あふれや精度の確認）。 -/
def largePrices : List Int := [99999999999, 100000000000, 12345678912, -12345678912]

def prices : List Int := integerPrices ++ centPrices ++ largePrices

/-- 現行の税率。全組み合わせを生成する。 -/
def mainRates : List Int := [8, 10]

/-- 過去の税率と 0%。小さな区間だけ生成する。 -/
def otherRates : List Int := [0, 3, 5]

def smallPrices : List Int := (range (-20) 40).map (· * 100) ++ centPrices

/-- 税率別税額の税込合計（円）。連続区間と、現行実装の反例の付近。 -/
def totals : List Int :=
  range (-30) 330 ++ [250026, 250027, 250028, 500010, 500011, 500012, 999999, 1000000, 1000001]

/-- 端数処理の入力（単位 10^-4）: 各整数の前後の境界。 -/
def roundValues : List Int :=
  (range (-3) 3).flatMap fun k => [0, 1, 4999, 5000, 5001, 9999].map (k * 10000 + ·)

/-! ### 各関数のベクタ -/

/-- `mtb_rounding_type.id`。99 は未知の id（PHP の `default` で切り上げ）。 -/
def roundIds : List Int := [1, 2, 3, 99]

/-- (価格, 税率, 調整額) の組。調整額 1 は現行の税率の小さな区間だけ。 -/
def priceRateAdjust (ps : List Int) (others : List Int) : List (Int × Int × Int) :=
  (ps.flatMap fun p => mainRates.map fun r => (p, r, 0))
  ++ (smallPrices.flatMap fun p => others.map fun r => (p, r, 0))
  ++ (smallPrices.flatMap fun p => mainRates.map fun r => (p, r, 1))

def taxRows (f : RoundingType → Int → Int → Int → Int) (xs : List (Int × Int × Int)) :
    List (List String) :=
  xs.flatMap fun (p, r, a) => RoundingType.all.map fun (rt : RoundingType) =>
    [jsonStr (fmtScaled p 2), jsonStr (toString r), jsonStr (toString a),
      toString rt.toId, jsonStr (toString (f rt p r a))]

def calcTaxRows : List (List String) :=
  taxRows calcTaxImpl (priceRateAdjust prices otherRates)

/-- 税率 3% は整数の円の価格と反例だけ（定理 3 の領域と、その外の境界）。 -/
def calcTaxIncludedRows : List (List String) :=
  let rate3 := ((range (-20) 40).map (· * 100) ++ [6867, -6867]).map fun p => (p, (3 : Int), (0 : Int))
  taxRows calcTaxIncludedImpl (priceRateAdjust prices [0, 5] ++ rate3)

def roundRows : List (List String) :=
  roundValues.flatMap fun v => roundIds.map fun id =>
    [jsonStr (fmtScaled v 4), toString id,
      jsonStr (toString (roundScaled (RoundingType.ofId id) v 4))]

def taxByRateRows : List (List String) :=
  let xs := (totals.flatMap fun t => mainRates.map fun r => (t, r))
    ++ ((range (-10) 60).flatMap fun t => otherRates.map fun r => (t, r))
  xs.flatMap fun (t, r) => RoundingType.all.map fun (rt : RoundingType) =>
    [jsonStr (toString t), jsonStr (toString r), toString rt.toId,
      jsonStr (toString (taxByRateFixed rt t r))]

def main : IO Unit := do
  IO.println "{"
  IO.println "  \"_comment\": \"formal/GenVectors.lean が生成。手で編集しない。再生成: cd formal && lake env lean --run GenVectors.lean > ../tests/Eccube/Tests/Formal/vectors/tax.json\","
  IO.println s!"  \"roundByRoundingType\": {jsonRows roundRows},"
  IO.println s!"  \"calcTax\": {jsonRows calcTaxRows},"
  IO.println s!"  \"calcTaxIncluded\": {jsonRows calcTaxIncludedRows},"
  IO.println s!"  \"taxByRate\": {jsonRows taxByRateRows}"
  IO.println "}"
