package jp.shiftinc.automation.eccube3.pages.front.mypage;

import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.exist;
import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.$$;

/**
 * 購入履歴の詳細画面のクラスです。
 *
 * Created by tamagawa on 2017/06/12.
 */
public class HistoryPage {

    public HistoryPage() {
        $("h3").shouldHave(text("購入履歴一覧"));
        $("img[" +
                "alt='この購入内容で再注文する']").should(exist);
    }

    @Step("商品名が{0}であることを確認する")
    public HistoryPage shouldHaveProductName(String name) {
        // 商品名を取得する良いセレクタがなかったため、やむを得ずindexを使用
        $$("td").get(1).shouldHave(text(name));

        return this;
    }

}
