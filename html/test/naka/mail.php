<?php

require_once("../../admin/require.php");

                    $objMail = new SC_SendMail();
                    $objMail->setItem(
                                      'naka@lockon.co.jp'									//　宛先
                                      , "aa"							//　サブジェクト
                                      , "bb"				//　本文
                                      ,"naka@lockon.co.jp",
                                      ""
                                      );
                    // 宛先の設定
                    $objMail->setTo("naka@lockon.co.jp", "naka@lockon.co.jp");
                    
                    //SC_Utils_Ex::sfPrintR($objMail);
                    $objMail->sendMail();
                    
                    print("ok");
?>
