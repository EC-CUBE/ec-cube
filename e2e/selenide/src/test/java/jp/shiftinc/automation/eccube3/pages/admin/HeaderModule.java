package jp.shiftinc.automation.eccube3.pages.admin;

import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Selectors.byXpath;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * 管理画面の共通ヘッダを表すモジュール。
 *
 * Created by tamagawa on 2016/12/16.
 */
public class HeaderModule {

    @Step("ログアウトする")
    public LoginPage logout() {
        $(".navbar-menu .cb-angle-down").click();
        $(byXpath("//a[text()='ログアウト']")).click();

        return page(LoginPage.class);
    }
}
