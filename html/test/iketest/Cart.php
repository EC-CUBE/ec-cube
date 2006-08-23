<?php
#########################################################
# Veritrans CVS Merchant Development Kit.
# BSCVS::Cart.php Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: ウェブサンプルのカートクラス。
#       お客様の環境に合わせてカスタマイズしてください。
#########################################################

class Cart
{

#------------------------------------------#
# メソッド: getOrderId
# 説明: ダミーの取引ＩＤを生成
# 入力値  : void
# 戻り値  : ダミー取引ＩＤ(YYYYMMDD-HHMISS)
# 注意点  :
#------------------------------------------#
function getOrderId() {
    $id = sprintf("%4d%02d%02d-%02d%02d%02d",
                  date("Y"), date("m"), date("d"),
                  date("H"), date("i"), date("s"));
    return $id;
}

#------------------------------------------#
# メソッド: getPrice
# 説明: ダミーの支払金額を生成
# 入力値  : void
# 戻り値  : ダミー支払金額(2円)
# 注意点  :
#------------------------------------------#
function getPrice() {
  return '2';
}

#------------------------------------------#
# メソッド: getPayLimit
# 説明: ダミーの支払期限を生成
# 入力値  : void
# 戻り値  : ダミー支払期限(YYYY/MM/DD)
# 注意点  : 現在の日付より20日後の日付を返す
#------------------------------------------#
function getPayLimit() {
    $date = sprintf("%10s",
                    date("Y/m/d",mktime(0,0,0,date("m"),
                    date("d")+20,date("Y"))));
    return $date;
}

}
?>