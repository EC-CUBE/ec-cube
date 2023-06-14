<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Interactions\DragAndDropBy;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;
    use \Codeception\Lib\Actor\Shared\Retry;

    public Customer $asACustomer;

    public function getScenario()
    {
        return $this->scenario;
    }

    public function loginAsAdmin($user = '', $password = '', $dir = '')
    {
        if (!$user || !$password) {
            $account = Fixtures::get('admin_account');
            $user = $account['member'];
            $password = $account['password'];
        }

        $I = $this;
        $this->goToAdminPage($dir);
        $loggedIn = false;
        $I->comment('Logging in to admin...');
        try {
            $I->seeInSource('login_id');
            $I->seeInSource('password');
        } catch (\Exception $e) {
            $I->comment('Already logged in...');
            $loggedIn = true;
        }

        if ($loggedIn === false) {
            $I->submitForm('#form1', [
                'login_id' => $user,
                'password' => $password,
            ]);
        }

        $I->see('ホーム', '.c-contentsArea .c-pageTitle > .c-pageTitle__titles');
    }

    public function logoutAsAdmin()
    {
        $I = $this;
        $isLogin = $I->grabTextFrom('header.c-headerBar div.c-headerBar__container a.c-headerBar__userMenu span');
        if ($isLogin == '管理者 様') {
            $I->click('header.c-headerBar div.c-headerBar__container a.c-headerBar__userMenu');
            $I->click('body div.popover .popover-body a:last-child');
            $config = Fixtures::get('config');
            $I->amOnPage('/'.$config['eccube_admin_route'].'/logout');
            $I->see('ログイン', '#form1 > div > button');
        }
    }

    public function goToAdminPage($dir = '')
    {
        $I = $this;
        if ($dir == '') {
            $config = Fixtures::get('config');
            $I->amOnPage('/'.$config['eccube_admin_route'].'/');
        } else {
            $I->amOnPage('/'.$dir);
        }
    }

    public function loginAsMember($email = '', $password = '')
    {
        $I = $this;
        $I->amOnPage('/mypage/login');
        $I->submitForm('#login_mypage', [
            'login_email' => $email,
            'login_pass' => $password,
        ]);
        $I->see('新着情報', '.ec-secHeading__ja');
        $I->see('ログアウト', ['css' => 'header.ec-layoutRole__header > div.ec-headerNaviRole > div.ec-headerNaviRole__right > div.ec-headerNaviRole__nav > div > div:nth-child(3) > a > span']);
    }

    public function logoutAsMember()
    {
        $I = $this;
        $I->amOnPage('/');
        $I->waitForElement('.ec-headerNaviRole .ec-headerNav .ec-headerNav__item:nth-child(3) a');
        $isLogin = $I->grabTextFrom('.ec-headerNaviRole .ec-headerNav .ec-headerNav__item:nth-child(3) a');
        if ($isLogin == 'ログアウト') {
            $I->wait(1);
            $I->click('.ec-headerNaviRole .ec-headerNav .ec-headerNav__item:nth-child(3) a');
            $I->see('ログイン', '.ec-headerNaviRole .ec-headerNav .ec-headerNav__item:nth-child(3) a');
        }
    }

    public function setStock($pid, $stock = 0)
    {
        if (!$pid) {
            return;
        }
        $entityManager = Fixtures::get('entityManager');

        if (!is_array($stock)) {
            $pc = $entityManager->getRepository('Eccube\Entity\ProductClass')->findOneBy(['Product' => $pid]);
            $pc->setStock($stock);
            $pc->setStockUnlimited(Constant::DISABLED);
            $ps = $entityManager->getRepository('Eccube\Entity\ProductStock')->findOneBy(['ProductClass' => $pc->getId()]);
            $ps->setStock($stock);
            $entityManager->persist($pc);
            $entityManager->persist($ps);
            $entityManager->flush();
        } else {
            $pcs = $entityManager->getRepository('Eccube\Entity\ProductClass')
                ->createQueryBuilder('o')
                ->where('o.Product = ' . $pid)
                ->andwhere('o.ClassCategory1 > 0')
                ->getQuery()
                ->getResult();
            foreach ($pcs as $key => $pc) {
                $pc->setStock($stock[$key]);
                $pc->setStockUnlimited(Constant::DISABLED);
                $pc->setSaleLimit(2);
                $ps = $entityManager->getRepository('Eccube\Entity\ProductStock')->findOneBy(['ProductClass' => $pc->getId()]);
                $ps->setStock($stock[$key]);
                $entityManager->persist($pc);
                $entityManager->persist($ps);
                $entityManager->flush();
            }
        }
    }

    public function buyThis($num = 1)
    {
        $I = $this;
        $I->fillField(['id' => 'quantity'], $num);
        $I->click('#form1 .btn_area button');
    }

    public function makeEmptyCart()
    {
        $I = $this;
        $I->click('#form_cart .item_box .icon_edit a');
        $I->acceptPopup();
    }

    /**
     * @param string|$fileNameRegex ファイル名のパターン(CI環境で同時実行したときに区別するため)
     *
     * @return string ファイルパス
     *
     * @throws FileNotFoundException 指定したパターンにマッチするファイルがない場合
     */
    public function getLastDownloadFile($fileNameRegex, $retryCount = 3)
    {
        $downloadDir = __DIR__.'/_downloads/';
        $files = scandir($downloadDir);
        $files = array_map(function ($fileName) use ($downloadDir) {
            return $downloadDir.$fileName;
        }, $files);
        $files = array_filter($files, function ($f) use ($fileNameRegex) {
            return is_file($f) && preg_match($fileNameRegex, basename($f));
        });
        usort($files, function ($l, $r) {
            return filemtime($l) - filemtime($r);
        });

        if (empty($files)) {
            if ($retryCount > 0) {
                $this->wait(3);

                return $this->getLastDownloadFile($fileNameRegex, $retryCount - 1);
            }
            throw new FileNotFoundException($fileNameRegex);
        }

        return end($files);
    }

    /**
     * _blankで開いたウィンドウに切り替え
     */
    public function switchToNewWindow()
    {
        $this->wait(1);
        $this->executeInSelenium(function ($webdriver) {
            $handles = $webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
    }

    /**
     * dontSeeElementが遅いのでJSで存在チェックを行う。
     *
     * @param array|$arrayOfSelector IDセレクタの配列
     */
    public function dontSeeElements($arrayOfSelector)
    {
        $self = $this;
        $result = array_filter($arrayOfSelector, function ($element) use ($self) {
            $id = $element['id'];

            return $self->executeJS("return document.getElementById('${id}') != null;");
        });
        $this->assertTrue(empty($result));
    }

    public function dragAndDropBy($selector, $x_offset, $y_offset)
    {
        $this->executeInSelenium(function (Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) use ($selector, $x_offset, $y_offset) {
            $node = $webDriver->findElement(WebDriverBy::cssSelector($selector));
            $action = new DragAndDropBy($webDriver, $node, $x_offset, $y_offset);
            $action->perform();
        });
    }

    public function dragAndDropByXPath($selector, $x_offset, $y_offset, $animation_frames = 1)
    {
        $this->executeInSelenium(function (Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) use ($selector, $x_offset, $y_offset, $animation_frames) {
            $node = $webDriver->findElement(WebDriverBy::xpath($selector));
            $action = new DragAndDropBy($webDriver, $node, $x_offset, $y_offset, $animation_frames);
            $action->perform();
        });
    }

    public function fillDate(string $dateField, Carbon $date, $locale = 'en-us', ?int $clickOffsetX = null, ?int $clickOffsetY = null, bool $noClick = false)
    {
        $I = $this;
        $I->comment(sprintf('I fill date in %s format', $locale));
        if ($noClick === false) {
            $I->clickWithLeftButton($dateField, $clickOffsetX, $clickOffsetY);
        }
        switch ($locale) {
            case('jp'):
                $I->type($date->format('Y'));
                $I->pressKey($dateField, WebDriverKeys::TAB);
                $I->type($date->format('m'));
                $I->type($date->format('d'));
                break;
            case('en-us'):
            default:
                $I->type($date->format('m'));
                $I->type($date->format('d'));
                $I->type($date->format('Y'));
        }

    }

    public function fillDateTime(string $dateTimeField, Carbon $dateTime, $locale = 'en-us')
    {
        $I = $this;
        switch ($locale):
            case('jp'):
                $I->clickWithLeftButton($dateTimeField);
                $I->type($dateTime->format('Y'));
                $I->pressKey($dateTimeField, WebDriverKeys::TAB);
                $I->type($dateTime->format('m'));
                $I->type($dateTime->format('d'));
                $I->type($dateTime->format('H'));
                $I->type($dateTime->format('i'));
                break;
            case('en-us'):
            default:
                $I->clickWithLeftButton($dateTimeField);
                $I->type($dateTime->format('m'));
                $I->type($dateTime->format('d'));
                $I->type($dateTime->format('Y'));
                $I->pressKey($dateTimeField, WebDriverKeys::TAB);
                $I->type($dateTime->format('H'));
                $I->type($dateTime->format('i'));
                break;
        endswitch;
    }

    public function generateCustomerAndLogin()
    {
        $I = $this;
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $this->asACustomer = $customer;
        $I->loginAsMember($customer->getEmail(), 'password');
    }

    public function wantToInstallPlugin(string $pluginName) {
        $I = $this;
        $I->retry(10, 500);
        $I->amOnPage('/admin/store/plugin');
        $targetRow = Locator::contains('//tr', $pluginName);
        $I->click($targetRow . Locator::contains('//a', 'インストール'));
        //
        $I->seeInCurrentUrl('admin/store/plugin/api/install');
        $I->see($pluginName);
        $I->click(Locator::contains('//button[@data-mode="install"]', 'インストール'));
        $I->retrySee('インストール確認');
        $I->click('//button[@id="installBtn"]');
        $I->retrySee('インストールが完了しました。');
        $I->retrySee('完了');
        $I->click(Locator::contains('//a', '完了'));
        //
        $I->seeInCurrentUrl('admin/store/plugin');
    }

    public function wantToInstallPluginLocally(string $filename)
    {
        $I = $this;
        $I->retry(10, 500);
        $I->amOnPage('/admin/store/plugin');
        $I->click(Locator::contains('//a', 'アップロードして新規追加'));
        $I->see('新規プラグインのアップロード');
        $I->attachFile('//input[@type="file"]', $filename);
        $I->click(Locator::contains('//button[@type="submit"]', 'アップロード'));
        $I->retrySeeInCurrentUrl('admin/store/plugin');
        $I->retrySee('プラグインをインストールしました。');
    }

    public function seePluginIsInstalled(string $pluginName, bool $safeEscape = false) {
        $I = $this;
        $I->amOnPage('/admin/store/plugin');
        $targetRow = Locator::contains('//tr', $pluginName);
        if ($safeEscape === true) {
            try {
                $I->dontSee('インストール', $targetRow);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        $I->dontSee('インストール・', $targetRow);
        //  $I->see('無効');
    }

    public function seePluginIsNotInstalled(string $pluginName, bool $safeEscape = false) {
        $I = $this;
        $I->amOnPage('/admin/store/plugin');
        $targetRow = Locator::contains('//tr', $pluginName);
        if ($safeEscape === true) {
            try {
                $I->see('インストール', $targetRow);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        $I->see('インストール', $targetRow);
    }

    public function wantToUninstallLocalPlugin(string $pluginName)
    {
        $I = $this;
        $I->retry(10, 500);
        $I->amOnPage('/admin/store/plugin');
        $targetRow = Locator::contains('//tr', $pluginName);
        $I->click($targetRow . '//a[@data-bs-target="#localPluginDeleteModal"]');
        $I->retrySee('プラグインの削除を確認する');
        $I->click('//div[@class="modal fade show"]//a[@class="btn btn-ec-delete"]');
        $I->retrySee('プラグインを削除しました。');
    }

    public function wantToUninstallPlugin(string $pluginName, bool $safeEscape = false): bool
    {
        try {
            $I = $this;
            $I->retry(10, 500);
            $I->amOnPage('/admin/store/plugin');
            $targetRow = Locator::contains('//tr', $pluginName);
            $I->click($targetRow . '//a[@data-bs-target="#officialPluginDeleteModal"]');
            $I->retrySee('プラグインの削除を確認する');
            $I->click('//button[@id="officialPluginDeleteButton"]');
            $I->retrySee('削除が完了しました。');
            $I->click(Locator::contains('//button', '完了'));
            //
            $I->seeInCurrentUrl('admin/store/plugin');
            return true;
        } catch (\Exception $e) {
            if ($safeEscape === true) {
                var_dump($e->getMessage());
                return false;
            }
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function wantToDisablePlugin(string $pluginName, bool $safeEscape = false): bool
    {
        try {
            $I = $this;
            $I->retry(10, 500);
            $I->amOnPage('/admin/store/plugin');
            $recommendPluginRow = Locator::contains('//tr', $pluginName);
            $I->see($pluginName, $recommendPluginRow);
            $I->see('有効', $recommendPluginRow);
            $I->clickWithLeftButton("(" . $recommendPluginRow . "//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
            $I->see(sprintf('「%s」を無効にしました。', $pluginName));
            $I->see($pluginName, $recommendPluginRow);
            $I->see('無効', $recommendPluginRow);
            return true;
        } catch (\Exception $e) {
            if($safeEscape === true) {
                var_dump($e->getMessage());
                return false;
            }
            throw $e;
        }
    }
}
