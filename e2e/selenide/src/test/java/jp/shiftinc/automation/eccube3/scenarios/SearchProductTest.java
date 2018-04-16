package jp.shiftinc.automation.eccube3.scenarios;

import jp.shiftinc.automation.eccube3.core.BaseTest;
import jp.shiftinc.automation.eccube3.pages.front.FrontPage;
import jp.shiftinc.automation.eccube3.pages.front.products.ProductsListPage;
import jp.shiftinc.automation.util.YamlReader;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Test;
import ru.yandex.qatools.allure.annotations.Features;
import ru.yandex.qatools.allure.annotations.Stories;

import static com.codeborne.selenide.Selenide.open;

/**
 * ※本当はあまりよくないかもしれないが、データは初期データを使用
 */
@Features("商品検索")
public class SearchProductTest extends BaseTest {

    private String keyword;

    @BeforeMethod
    public void setup() {
        keyword = (String) YamlReader.read("/fixtures/eccube3/scenarios/SearchProductTest/keyword.yml");
    }

    @Stories("商品をキーワード検索できる")
    @Test
    public void userCanSearchTheProductWithKeyword() throws Exception {
        FrontPage frontPage = open("/", FrontPage.class);
        ProductsListPage productsListPage = frontPage.searchWithKeyword(keyword);

        productsListPage.shouldMeetKeywordCondition(keyword);
    }
}
