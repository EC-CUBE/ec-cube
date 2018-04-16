package jp.shiftinc.automation.eccube3.scenarios;

import jp.shiftinc.automation.eccube3.constant.ConfigurationKey;
import jp.shiftinc.automation.eccube3.core.BaseTest;
import jp.shiftinc.automation.eccube3.data.Product;
import jp.shiftinc.automation.eccube3.pages.admin.AdminPage;
import jp.shiftinc.automation.eccube3.pages.admin.LoginPage;
import jp.shiftinc.automation.eccube3.pages.admin.product.ProductNewPage;
import jp.shiftinc.automation.eccube3.pages.front.FrontPage;
import jp.shiftinc.automation.eccube3.pages.front.products.ProductsDetailPage;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Test;
import ru.yandex.qatools.allure.annotations.Features;
import ru.yandex.qatools.allure.annotations.Stories;

import static com.codeborne.selenide.Selenide.open;

@Features("商品閲覧")
public class DisplayProductTest extends BaseTest {
    private Product product;

    @BeforeMethod
    public void setUp() {
         // 商品を登録したら
        LoginPage loginPage = open("/admin", LoginPage.class);
        AdminPage adminPage = loginPage.login(configuration.get(ConfigurationKey.ADMIN_ID), configuration.get(ConfigurationKey.ADMIN_PASSWORD));
        ProductNewPage productNewPage = adminPage.openNewProductPage();

        product = Product.getFixture();
        productNewPage.registerProduct(product);
    }

    @Stories("登録済みの商品の詳細ページを開くことができる")
    @Test
    public void ThenICanOpenDetailOfTheProduct() throws Exception {
        // リンクをクリックすると、詳細ページに行ける
        FrontPage frontPage = open("/", FrontPage.class);
        ProductsDetailPage productsDetailPage = frontPage.openProductDetail(product);

        productsDetailPage.shouldHaveProductName(product.getProductName());
    }
}
