package jp.shiftinc.automation.eccube3.scenarios.exercise;

import org.testng.annotations.Test;

import static com.codeborne.selenide.Condition.visible;
import static com.codeborne.selenide.Selenide.$;
import static com.codeborne.selenide.Selenide.sleep;

/**
 * ボタンクリック＆表示の確認の演習
 *
 * Created by tamagawa on 2017/06/06.
 */
public class Exercise01Test extends AbstractExerciseTest {

    @Test
    public void test() {
        /// 【商品を登録】ボタンをクリックする
        $("button.btn-primary").click();

        /// 【登録できませんでした。】というエラーメッセージが表示されることを確認する
        $("div.alert").shouldBe(visible);

        // 動作を分かりやすくするため、5秒間ストップ
        sleep(5000);
    }
}
