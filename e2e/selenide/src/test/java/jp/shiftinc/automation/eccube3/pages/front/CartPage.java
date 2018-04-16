package jp.shiftinc.automation.eccube3.pages.front;

import java.text.DecimalFormat;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;

/**
 * カート画面を表すクラスです。
 *
 * Created by tamagawa on 2017/06/06.
 */
public class CartPage {

    private static final DecimalFormat format = new DecimalFormat();

    public CartPage() {
        $("h2").shouldHave(text("カゴの中"));
    }


    // TODO
    /// 「合計金額を確認する」メソッドと「【購入手続きへ】ボタンをクリックする」メソッドを実装してください。
}
