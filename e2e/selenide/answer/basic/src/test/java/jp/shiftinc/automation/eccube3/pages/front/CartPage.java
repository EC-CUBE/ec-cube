package jp.shiftinc.automation.eccube3.pages.front;

import jp.shiftinc.automation.eccube3.pages.front.shopping.LoginPage;
import ru.yandex.qatools.allure.annotations.Step;

import java.text.DecimalFormat;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selectors.byXpath;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * カート画面を表すクラスです。
 *
 * Created by tamagawa on 2017/06/06.
 */
public class CartPage {

    private static final DecimalFormat format = new DecimalFormat();

    public CartPage() {
        $("h2").shouldHave(text("カゴの中"));
    }

    @Step("合計金額が{0}円であることを確認する")
    public CartPage shouldHaveTotalPrice(int price) {
        String formattedPrice = format.format(price) + "円";
        $(byXpath("//*[text()='合計']/following-sibling::td")).shouldHave(text(formattedPrice));

        return page(CartPage.class);
    }

    @Step("「購入手続きへ」ボタンをクリックする")
    public LoginPage purchaseByNotAuthenticatedUser() {
        purchase();
        return page(LoginPage.class);
    }

    // ログインユーザとそうでないユーザで結果が異なるので、ボタンを押すだけのメソッドを作成してそれぞれから呼び出す
    private void purchase() {
        $("#form_cart div.btn_area a").click();
    }
}
