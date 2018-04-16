package jp.shiftinc.automation.eccube3.pages.admin.product;

import com.codeborne.selenide.Selenide;
import jp.shiftinc.automation.eccube3.data.Product;
import jp.shiftinc.automation.eccube3.pages.admin.AdminAuthorizedPage;
import ru.yandex.qatools.allure.annotations.Attachment;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.*;

/**
 * 商品登録画面を表すクラスです。
 *
 * Created by kenichiro_ota on 2015/12/15.
 */
public class ProductNewPage extends AdminAuthorizedPage {

    public ProductNewPage() {
        $("h1 span").shouldHave(text("商品登録"));
    }

    @Step("商品を登録する:{0}")
    public ProductMasterPage registerProduct(Product product) {
        productDetail(product);

        $("#admin_product_name").setValue(product.getProductName());
        $("#admin_product_class_product_type_" + Integer.toString(product.getProductType())).click();
        $("#admin_product_description_detail").setValue(product.getProductDescriptionDetail());
        $("#admin_product_class_price02").setValue(Integer.toString(product.getSellingPrice()));
        $("#admin_product_class_stock").setValue(Integer.toString(product.getProductStock()));
        $("#admin_product_Category").parent().parent().$("a").click(); //商品カテゴリのアコーディオンパネルを開く
        $("#admin_product_Category_" + Integer.toString(product.getProductCategory())).setSelected(true);

        executeJavaScript("scroll(0, 0);"); // 公開、非公開を押せるように画面を一番上に戻す
        $("#admin_product_Status_" + Integer.toString(product.getProductStatus())).click();

        $$("#aside_column button").first().click();
        return Selenide.page(ProductMasterPage.class);
    }

    @Attachment
    private String productDetail(Product product) {
        return product.toString();
    }
}
