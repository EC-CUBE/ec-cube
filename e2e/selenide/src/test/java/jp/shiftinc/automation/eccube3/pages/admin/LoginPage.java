package jp.shiftinc.automation.eccube3.pages.admin;

import com.codeborne.selenide.Selenide;
import com.codeborne.selenide.SelenideElement;
import org.openqa.selenium.support.FindBy;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.exist;
import static com.codeborne.selenide.Selenide.$;

/**
 * 管理者用のログイン画面を表すクラスです。
 *
 */
public class LoginPage {

    @FindBy(id = "login_id")
    private SelenideElement loginIdText;

    public LoginPage() {
        $("#login-form").should(exist);
    }

    @Step("管理画面へログインID:{0}, パスワード{1}でログインする")
    public AdminPage login(String loginId, String password) {
        loginIdText.setValue(loginId);
        $("#password").setValue(password);
        $(".btn-tool-format").click();

        return Selenide.page(AdminPage.class);
    }
}

