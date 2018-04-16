package jp.shiftinc.automation.eccube3.pages.front.products;

import com.codeborne.selenide.Condition;
import com.codeborne.selenide.ElementsCollection;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.exist;
import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.$$;

/**
 * 商品の検索結果ページを表すクラスです。
 *
 * Created by tamagawa on 2017/06/09.
 */
public class ProductsListPage {

    public ProductsListPage() {
        $("#productscount").should(exist);
    }

    @Step("商品名{0}が表示されていることを確認する")
    public void shouldHaveProduct(String productName) {
        getItemNames().find(text(productName)).should(exist);
    }

    @Step("商品一覧にある商品がキーワード{0}に合致していることを確認する")
    public void shouldMeetKeywordCondition(String keyword) {
        // 検索結果でループ
        getItemNames().forEach(itemName -> {
            // 1行ごとのチェック
            // 失敗したらその行で終了
            itemName.shouldHave(text(keyword));
        });
    }

    @Step("商品名を取得する")
    public ElementsCollection getItemNames() {
        return $$(".item_name");
    }

}
