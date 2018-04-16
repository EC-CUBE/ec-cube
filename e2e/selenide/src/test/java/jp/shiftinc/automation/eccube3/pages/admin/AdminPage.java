package jp.shiftinc.automation.eccube3.pages.admin;

import jp.shiftinc.automation.eccube3.pages.admin.product.ProductNewPage;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.open;
import static com.codeborne.selenide.Selenide.page;

/**
 * 管理側のトップページを表すクラスです。
 *
 * Created by kenichiro_ota on 2015/12/15.
 */
public class AdminPage extends AdminAuthorizedPage {

    public AdminPage() {
        $("h2").shouldHave(text("システム情報"));
    }

    @Step("商品登録ページを開く")
    public ProductNewPage openNewProductPage() {
        // 遷移を安定させるためにダイレクトに移動する
        open("/admin/product/product/new");
        return page(ProductNewPage.class);
    }
}
