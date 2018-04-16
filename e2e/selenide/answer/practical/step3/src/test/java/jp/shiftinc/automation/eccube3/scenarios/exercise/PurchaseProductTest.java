package jp.shiftinc.automation.eccube3.scenarios.exercise;

import jp.shiftinc.automation.eccube3.constant.ConfigurationKey;
import jp.shiftinc.automation.eccube3.constant.PropertyKey;
import jp.shiftinc.automation.eccube3.core.BaseTest;
import jp.shiftinc.automation.eccube3.data.Product;
import jp.shiftinc.automation.eccube3.data.User;
import jp.shiftinc.automation.eccube3.data.UserForExercise;
import jp.shiftinc.automation.eccube3.pages.admin.AdminPage;
import jp.shiftinc.automation.eccube3.pages.admin.LoginPage;
import jp.shiftinc.automation.eccube3.pages.admin.product.ProductNewPage;
import jp.shiftinc.automation.eccube3.pages.front.CartPage;
import jp.shiftinc.automation.eccube3.pages.front.FrontPage;
import jp.shiftinc.automation.eccube3.pages.front.mypage.MyPage;
import jp.shiftinc.automation.eccube3.pages.front.products.ProductsDetailPage;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Test;
import ru.yandex.qatools.allure.annotations.Features;
import ru.yandex.qatools.allure.annotations.Stories;

import javax.mail.Message;

import static com.codeborne.selenide.Selenide.open;

@Features("商品を購入する")
public class PurchaseProductTest extends BaseTest {
    private Product product;
    private UserForExercise user;

    @BeforeMethod
    public void setUp() {
        // システムプロパティに従ってデータの取得先を決める
        String resourceDir = System.getProperty(PropertyKey.RESOURCE_DIR);
        user = UserForExercise.fromYaml(String.format(
                "/fixtures/eccube3/%s/scenarios/exercise/PurchaseProductTest/user.yml",
                resourceDir == null ? "" : (resourceDir + "/")));

        // 商品を登録したら
        LoginPage loginPage = open("/admin", LoginPage.class);
        AdminPage adminPage = loginPage.login(configuration.get(ConfigurationKey.ADMIN_ID), configuration.get(ConfigurationKey.ADMIN_PASSWORD));
        ProductNewPage productNewPage = adminPage.openNewProductPage();

        product = Product.getFixture();
        productNewPage.registerProduct(product);
    }

    @Stories("商品を購入できる")
    @Test
    public void AuthenticatedUserCanPurchaseProduct() throws Exception {
        // 登録した商品の詳細画面を開く
        FrontPage frontPage = open("/", FrontPage.class);
        ProductsDetailPage productsDetailPage = frontPage.openProductDetail(product);

        // 数量を入力して商品をカートに入れる
        CartPage cartPage = productsDetailPage
                .setQuantity("2")
                .addToCart();

        // カート画面にて、合計金額が正しいことを確認する
        // 「購入手続きへ」ボタンをクリックし、ログイン画面に遷移することを確認する
        /// 消費税の計算は四捨五入になっているようなので、とりあえずそれに従う
        int price = (int) Math.round(product.getSellingPrice() * 1.08) * 2;
        cartPage.shouldHaveTotalPrice(price)
                .purchaseByNotAuthenticatedUser()
                .login(user.getEmail1(), user.getPassword());

        // いったんトップページに移動しているので、カートを開き直してから購入
        open("/cart", CartPage.class)
                .purchaseByAuthenticatedUser()
                .confirm();

        // マイページで履歴を確認
        open("/mypage", MyPage.class)
                .openLatestPurchaseHistory()
                .shouldHaveProductName(product.getProductName());
    }
}
