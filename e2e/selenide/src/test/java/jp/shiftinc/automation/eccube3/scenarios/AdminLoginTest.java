package jp.shiftinc.automation.eccube3.scenarios;

import jp.shiftinc.automation.eccube3.constant.ConfigurationKey;
import jp.shiftinc.automation.eccube3.core.BaseTest;
import jp.shiftinc.automation.eccube3.pages.admin.LoginPage;
import org.testng.annotations.Test;
import ru.yandex.qatools.allure.annotations.Features;
import ru.yandex.qatools.allure.annotations.Stories;

import static com.codeborne.selenide.Selenide.open;

/**
 * Created by tamagawa on 2016/12/16.
 */
@Features("管理画面ログイン")
public class AdminLoginTest extends BaseTest {

    @Stories("管理画面にログイン・ログアウトできる")
    @Test
    public void adminCanLoginLogout() {
        LoginPage loginPage = open("/admin", LoginPage.class);
        loginPage.login(configuration.get(ConfigurationKey.ADMIN_ID), configuration.get(ConfigurationKey.ADMIN_PASSWORD))
                .header()
                .logout();
    }
}
