package jp.shiftinc.automation.eccube3.pages.front.products;

import com.codeborne.selenide.SelenideElement;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.exist;
import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;

/**
 * 商品の詳細ページを表すクラスです。
 *
 * Created by kenichiro_ota on 2015/12/17.
 */
public class ProductsDetailPage {

    public ProductsDetailPage() {
        $("#item_detail").should(exist);
    }

    @Step("詳細ページで商品名を取得する")
    public SelenideElement getItemName() {
        return $(".item_name");
    }

    @Step("商品名が{0}であることを確認する")
    public ProductsDetailPage shouldHaveProductName(String productName) {
        getItemName().shouldHave(text(productName));
        return this;
    }

    // TODO
    /// 「数量を入力する」メソッドと「カートに入れる」メソッドを実装してください。
}
