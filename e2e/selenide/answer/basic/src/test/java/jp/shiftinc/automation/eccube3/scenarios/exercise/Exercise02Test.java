package jp.shiftinc.automation.eccube3.scenarios.exercise;

import org.testng.annotations.Test;

import static com.codeborne.selenide.Condition.value;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.sleep;

/**
 * テキストの入力＆確認の演習
 *
 * Created by tamagawa on 2017/06/06.
 */
public class Exercise02Test extends AbstractExerciseTest {

    @Test
    public void test() {
        /// 商品名を入力する
        $("#admin_product_name").setValue("Selenide演習");

        /// 入力したテキストの内容を確認する
        $("#admin_product_name").shouldHave(value("Selenide演習"));

        // 動作を分かりやすくするため、5秒間ストップ
        sleep(5000);
    }
}
