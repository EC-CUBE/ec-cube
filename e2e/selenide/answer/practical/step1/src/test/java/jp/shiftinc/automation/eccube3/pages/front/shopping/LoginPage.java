package jp.shiftinc.automation.eccube3.pages.front.shopping;

import jp.shiftinc.automation.eccube3.pages.front.FrontPage;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * フロント側のログイン画面を表すクラスです。
 *
 * Created by tamagawa on 2017/06/06.
 */
public class LoginPage {

    public LoginPage() {
        $("h2").shouldHave(text("ログイン"));
    }

    @Step("フロント画面でメールアドレス:{0}, パスワード{1}でログインする")
    public FrontPage login(String email, String password) {
        $("#login_email").setValue(email);
        $("#login_pass").setValue(password);
        $("#log").click();

        return page(FrontPage.class);
    }
}
