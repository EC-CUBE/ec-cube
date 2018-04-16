package jp.shiftinc.automation.eccube3.pages.front.entry;

import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selectors.byXpath;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * 会員登録の確認画面用クラスです。
 *
 * Created by tamagawa on 2016/12/12.
 */
public class EntryConfirmPage {

    public EntryConfirmPage() {
        $("h1").shouldHave(text("新規会員登録確認"));
    }

    @Step("登録を確定する")
    public EntryActivatePage submit() {
        $(byXpath("//button[@type='submit']")).click();
        return page(EntryActivatePage.class);
    }
}
