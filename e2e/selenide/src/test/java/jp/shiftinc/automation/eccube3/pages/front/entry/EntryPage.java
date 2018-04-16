package jp.shiftinc.automation.eccube3.pages.front.entry;

import com.codeborne.selenide.Selenide;
import jp.shiftinc.automation.eccube3.data.User;
import ru.yandex.qatools.allure.annotations.Attachment;
import ru.yandex.qatools.allure.annotations.Step;

import static com.codeborne.selenide.Condition.matchText;
import static com.codeborne.selenide.Selectors.byXpath;
import static com.codeborne.selenide.Selenide.$;

/**
 * Created by tamagawa on 2016/12/12.
 */
public class EntryPage {

    public EntryPage() {
        $("h1").shouldHave(matchText("^新規会員登録$"));
    }

    @Step("十分な情報を入力してユーザを登録する:{0}")
    public EntryConfirmPage registerUser(User user) {
        input(user);
        return Selenide.page(EntryConfirmPage.class);
    }

    @Step("情報が不十分な状態でユーザ登録を行い、登録画面に留まる:{0}")
    public EntryPage registerUserInvalid(User user) {
        input(user);
        return Selenide.page(EntryPage.class);
    }

    @Step("ユーザ情報を入力する")
    private void input(User user) {
        userDetail(user);

        $("#entry_name_name01").setValue(user.getLastName());
        $("#entry_name_name02").setValue(user.getFirstName());
        $("#entry_kana_kana01").setValue(user.getLastNameKana());
        $("#entry_kana_kana02").setValue(user.getFirstNameKana());
        $("#zip01").setValue(user.getZip1());
        $("#zip02").setValue(user.getZip2());
        $("#pref").selectOption(user.getPrefecture());
        $("#addr01").setValue(user.getTown());
        $("#addr02").setValue(user.getAddress());
        $("#entry_tel_tel01").setValue(user.getTel1());
        $("#entry_tel_tel02").setValue(user.getTel2());
        $("#entry_tel_tel03").setValue(user.getTel3());
        $("#entry_fax_fax01").setValue(user.getFax1());
        $("#entry_fax_fax02").setValue(user.getFax2());
        $("#entry_fax_fax03").setValue(user.getFax3());
        $("#entry_email_first").setValue(user.getEmail1());
        $("#entry_email_second").setValue(user.getEmail2());
        $("#entry_password").setValue(user.getPassword());
        $("#entry_birth_year").selectOption(user.getBirthYear());
        $("#entry_birth_month").selectOption(user.getBirthMonth());
        $("#entry_birth_day").selectOption(user.getBirthDate());
        $("#entry_sex").find(byXpath(String.format(".//label[text()='%s']", user.getSex()))).click();
        $("#entry_job").selectOption(user.getJob());

        $(byXpath("//button[@type='submit']")).click();
    }

    @Attachment
    public String userDetail(User user) {
        return user.toString();
    }
}
