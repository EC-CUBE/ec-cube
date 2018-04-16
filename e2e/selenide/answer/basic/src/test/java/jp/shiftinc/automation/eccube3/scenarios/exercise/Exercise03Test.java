package jp.shiftinc.automation.eccube3.scenarios.exercise;

import org.testng.annotations.Test;

import static com.codeborne.selenide.Condition.text;
import static com.codeborne.selenide.Condition.value;
import static com.codeborne.selenide.Selectors.byText;
import static com.codeborne.selenide.Selectors.byValue;
import static com.codeborne.selenide.Selectors.withText;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.sleep;

/**
 * プルダウンの選択 & 確認
 *
 * Created by tamagawa on 2017/06/06.
 */
public class Exercise03Test extends AbstractExerciseTest {

    @Test
    public void test() {
        /// 「詳細な設定」をクリックして開く
        $(withText("詳細な設定")).click();

        /// 発送日目安を選択する
        $("#admin_product_DeliveryDate").selectOption("1～2日後");

        /// 選択したプルダウンの内容を確認する
        $("#admin_product_DeliveryDate").getSelectedOption().shouldHave(text("1～2日後"));
//        // 別解
//        $("#admin_product_DeliveryDate").$(byText("1～2日後")).shouldBe(selected);
//        $("#admin_product_DeliveryDate").$(byValue("2")).shouldBe(selected);

        // 動作を分かりやすくするため、5秒間ストップ
        sleep(5000);
    }
}
