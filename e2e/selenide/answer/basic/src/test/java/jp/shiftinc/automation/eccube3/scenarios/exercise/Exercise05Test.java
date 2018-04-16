package jp.shiftinc.automation.eccube3.scenarios.exercise;

import com.codeborne.selenide.SelenideElement;
import org.testng.annotations.Test;

import static com.codeborne.selenide.Condition.checked;
import static com.codeborne.selenide.Selectors.withText;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.sleep;

/**
 * チェックボックスのチェック & 確認
 *
 * Created by tamagawa on 2017/06/06.
 */
public class Exercise05Test extends AbstractExerciseTest {

    @Test
    public void test() {
        /// 「カテゴリを選択」をクリックして開く
        $(withText("カテゴリを選択")).click();

        SelenideElement food = $("#admin_product_Category").$(withText("食品")).$("input");
        /// カテゴリ「食品」にチェックを入れる
        food.setSelected(true);
//        // 別解
//        $("#admin_product_Category_1").setSelected(true);

        /// 選択したチェックボックスを確認する
        food.shouldBe(checked);
//        // 別解
//        $("#admin_product_Category_1").shouldBe(checked);

        // 動作を分かりやすくするため、5秒間ストップ
        sleep(5000);
    }
}
