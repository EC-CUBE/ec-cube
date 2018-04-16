package jp.shiftinc.automation.eccube3.core;

import com.codeborne.selenide.WebDriverRunner;
import jp.shiftinc.automation.util.Configuration;
import org.testng.IHookCallBack;
import org.testng.IHookable;
import org.testng.ITestResult;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeClass;

import java.io.IOException;

import static jp.shiftinc.automation.util.ImageUtils.screenshot;

/**
 * Created by kenichiro_ota on 2014/04/21.
 */
public class BaseTest implements IHookable {
    protected static Configuration configuration;
    @BeforeClass
    public static void setUpClass() throws Exception {
        configuration = Configuration.getInstance();
    }

    @AfterMethod
    public void tearDown() throws IOException {
        WebDriverRunner.webdriverContainer.closeWebDriver();
    }

    @Override
    public void run(IHookCallBack callBack, ITestResult testResult) {

        callBack.runTestMethod(testResult);
        if (testResult.getThrowable() != null) {
            try {
                screenshot(false);
            } catch (Throwable e) {
                e.printStackTrace();
            }
        }
    }
}
