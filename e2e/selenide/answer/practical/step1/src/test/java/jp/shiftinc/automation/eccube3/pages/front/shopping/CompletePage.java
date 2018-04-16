package jp.shiftinc.automation.eccube3.pages.front.shopping;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;

/**
 * 購入管理画面を表すクラスです。
 *
 * Created by tamagawa on 2017/06/06.
 */
public class CompletePage {

    public CompletePage() {
        $("h2").shouldHave(text("ご購入完了"));
    }
}
