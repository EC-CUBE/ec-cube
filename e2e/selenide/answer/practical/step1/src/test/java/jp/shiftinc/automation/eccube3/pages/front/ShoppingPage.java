package jp.shiftinc.automation.eccube3.pages.front;

import jp.shiftinc.automation.eccube3.pages.front.shopping.CompletePage;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * 注文内容確認画面を表すクラスです。
 *
 * Created by tamagawa on 2017/06/06.
 */
public class ShoppingPage {

    public ShoppingPage() {
        $("h2").shouldHave(text("ご注文内容の確認"));
    }

    @Step("注文を確定する")
    public CompletePage confirm() {
        $("#next-top").click();

        return page(CompletePage.class);
    }
}
