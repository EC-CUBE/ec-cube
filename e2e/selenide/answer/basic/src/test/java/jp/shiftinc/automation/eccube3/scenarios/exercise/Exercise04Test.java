package jp.shiftinc.automation.eccube3.scenarios.exercise;

import org.testng.annotations.Test;

import static com.codeborne.selenide.Condition.selected;
import static com.codeborne.selenide.Selectors.byName;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.sleep;

/**
 * ラジオボタンの選択 & 確認
 *
 * Created by tamagawa on 2017/06/06.
 */
public class Exercise04Test extends AbstractExerciseTest {

    @Test
    public void test() {
        /// 商品種別を選択する
        $(byName("admin_product[class][product_type]")).setValue("2");
//        // 別解
//        $(byName("admin_product[class][product_type]")).selectRadio("2");

        /// 選択したラジオボタンを確認する
        $("#admin_product_class_product_type_2").shouldBe(selected);

        // 動作を分かりやすくするため、5秒間ストップ
        sleep(5000);
    }
}
