package jp.shiftinc.automation.eccube3.pages.front.mypage;

import jp.shiftinc.automation.eccube3.data.User;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.attribute;
import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selectors.byLinkText;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * Created by tamagawa on 2016/12/12.
 */
public class MyPage {

    public MyPage() {
        $("h2.title").shouldHave(text("マイページ"));
        $(byLinkText("ご注文履歴")).shouldHave(attribute("href", ""));
    }

    @Step("正しいユーザ名が表示されている")
    public MyPage shouldHaveUserName(User user) {
        // FIXME
        $("div.message").shouldHave(text(String.format("ようこそ ／ %s %s 様", user.getLastName(), user.getFirstName())));
        return page(MyPage.class);
    }
}
