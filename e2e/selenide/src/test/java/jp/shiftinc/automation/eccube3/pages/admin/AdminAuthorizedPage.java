package jp.shiftinc.automation.eccube3.pages.admin;

import com.codeborne.selenide.Selenide;

/**
 * 管理側のログイン済みページを表すクラスです。
 *
 * Created by tamagawa on 2016/12/16.
 */
public abstract class AdminAuthorizedPage {

    public HeaderModule header() {
        return Selenide.page(HeaderModule.class);
    }
}
