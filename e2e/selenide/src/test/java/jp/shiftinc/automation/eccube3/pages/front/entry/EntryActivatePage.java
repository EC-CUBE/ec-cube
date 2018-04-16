package jp.shiftinc.automation.eccube3.pages.front.entry;

import jp.shiftinc.automation.eccube3.pages.front.FrontPage;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * Created by tamagawa on 2016/12/12.
 */
public class EntryActivatePage {

    public EntryActivatePage() {
        $("h1").shouldHave(text("新規会員登録（完了）"));
    }

    @Step("トップページへ戻る")
    public FrontPage backToTop() {
        $("a.btn-info").click();
        return page(FrontPage.class);
    }
}
