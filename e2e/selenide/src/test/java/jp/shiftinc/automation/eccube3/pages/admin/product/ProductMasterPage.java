package jp.shiftinc.automation.eccube3.pages.admin.product;

import jp.shiftinc.automation.eccube3.pages.admin.AdminAuthorizedPage;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.*;

/**
 * 商品マスタページを表すクラスです。
 *
 * Created by tamagawa on 2016/12/13.
 */
public class ProductMasterPage extends AdminAuthorizedPage {

    public ProductMasterPage() {
        $("h1 span").shouldHave(text("商品マスター"));
    }
}
