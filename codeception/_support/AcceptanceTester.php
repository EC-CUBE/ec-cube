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

use Codeception\Util\Fixtures;
use Eccube\Common\Constant;
use Facebook\WebDriver\WebDriverBy;
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

        $I->submitForm('#form1', [
            'login_id' => $user,
            'password' => $password,
        ]);

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
            $I->see('ログイン', '#form1 > button');
        }
    }

    public function goToAdminPage($dir = '')
    {
        $I = $this;
        if ($dir == '') {
            $config = Fixtures::get('config');
            $I->amOnPage('/'.$config['eccube_admin_route']);
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
        $I->see('ログアウト', ['css' => 'div.ec-layoutRole__header > div.ec-headerNaviRole > div.ec-headerNaviRole__right > div.ec-headerNaviRole__nav > div > div:nth-child(3) > a > span']);
    }

    public function logoutAsMember()
    {
        $I = $this;
        $I->amOnPage('/');
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
                ->where('o.Product = '.$pid)
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
}
