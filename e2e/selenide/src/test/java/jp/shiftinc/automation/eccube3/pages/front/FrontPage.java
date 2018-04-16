package jp.shiftinc.automation.eccube3.pages.front;

import jp.shiftinc.automation.eccube3.data.Product;
import jp.shiftinc.automation.eccube3.pages.front.products.ProductsDetailPage;
import jp.shiftinc.automation.eccube3.pages.front.products.ProductsListPage;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.exist;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.page;

/**
 * フロント側のトップページを表すクラスです。
 *
 * Created by kenichiro_ota on 2015/12/16.
 */
public class FrontPage {

    public FrontPage() {
        $("div.slick-slider").should(exist);
    }

    @Step("商品を商品名で検索する:{0}")
    public ProductsListPage search(Product product) {
        return searchWithKeyword(product.getProductName());
    }

    @Step("商品をキーワードで検索する:{0}")
    public ProductsListPage searchWithKeyword(String keyword) {
        $("#name").setValue(keyword);
        $(".bt_search").click();

        return page(ProductsListPage.class);
    }

    @Step("商品の詳細ページを開く:{0}")
    public ProductsDetailPage openProductDetail(Product product) {
        search(product);
        $(".item_name").click();

        return page(ProductsDetailPage.class);
    }
}
