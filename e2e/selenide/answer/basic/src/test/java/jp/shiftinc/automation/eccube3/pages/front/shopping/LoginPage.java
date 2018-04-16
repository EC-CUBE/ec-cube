package jp.shiftinc.automation.eccube3.pages.front.shopping;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;

/**
 * フロント側のログイン画面を表すクラスです。
 *
 * Created by tamagawa on 2017/06/06.
 */
public class LoginPage {

    public LoginPage() {
        $("h2").shouldHave(text("ログイン"));
    }
}
