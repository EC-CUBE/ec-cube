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

namespace Page\Admin;

class PluginManagePage extends AbstractAdminPageStyleGuide
{
    public const 完了メッセージ = '#page_admin_store_plugin > div.c-container > div.c-contentsArea > div.alert:not(.alert-primary).alert-dismissible.fade.show.m-3 > span';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('インストールプラグイン一覧オーナーズストア');
    }

    /**
     * @param $pluginCode
     * @param string $message
     *
     * @return PluginManagePage
     */
    public function ストアプラグイン_有効化($pluginCode, $message = '有効にしました。')
    {
        $this->ページ遷移を伴うクリック(function () use ($pluginCode) {
            $this->ストアプラグイン_ボタンクリック($pluginCode, '有効化');
        });
        $this->tester->waitForText($message, 30, self::完了メッセージ);

        return $this;
    }

    /**
     * @param $pluginCode
     * @param string $message
     *
     * @return PluginManagePage
     */
    public function ストアプラグイン_無効化($pluginCode, $message = '無効にしました。')
    {
        $this->ページ遷移を伴うクリック(function () use ($pluginCode) {
            $this->ストアプラグイン_ボタンクリック($pluginCode, '無効化');
        });
        $this->tester->waitForText($message, 30, self::完了メッセージ);

        return $this;
    }

    /**
     * @param $pluginCode
     * @param string $message
     *
     * @return PluginManagePage
     *
     * @throws \Exception
     */
    public function ストアプラグイン_削除($pluginCode, $message = '削除が完了しました。')
    {
        $this->ストアプラグイン_ボタンクリック($pluginCode, '削除');
        $this->tester->waitForElementVisible(['id' => 'officialPluginDeleteButton'], 60);
        $this->tester->click(['id' => 'officialPluginDeleteButton']);
        $this->tester->waitForText($message, 30, ['css' => '#officialPluginDeleteModal > div > div > div.modal-body.text-start > p']);
        $this->tester->click(['css' => '#officialPluginDeleteModal > div > div > div.modal-footer > button:nth-child(3)']);

        return $this;
    }

    /**
     * @param $pluginCode
     *
     * @return PluginStoreUpgradePage
     */
    public function ストアプラグイン_アップデート($pluginCode)
    {
        echo $this->tester->grabTextFrom(['xpath' => '//*[@id="page_admin_store_plugin"]']);
        $this->tester->click(['xpath' => $this->ストアプラグイン_セレクタ($pluginCode).'/../../td[5]/a']);

        return PluginStoreUpgradePage::at($this->tester);
    }

    private function ストアプラグイン_ボタンクリック($pluginCode, $label)
    {
        $xpathStr = $this->ストアプラグイン_セレクタ($pluginCode).'/../../td[6]//i[@data-bs-original-title="'.$label.'"]/parent::node()';
        $this->tester->scrollTo(['xpath' => $xpathStr]);
        // tooltip やアラートを除去した上で JS クリックすることで、
        // WebDriver のマウス移動による tooltip 再出現を回避する
        $this->tester->executeJS(
            "document.querySelectorAll('.tooltip, .alert-dismissible').forEach(e => e.remove());"
            .'document.evaluate(arguments[0], document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click()',
            [$xpathStr]
        );

        return $this;
    }

    public function ストアプラグイン_セレクタ($pluginCode)
    {
        return '//*[@id="page_admin_store_plugin"]//div/h5[contains(text(), "オーナーズストアのプラグイン")]/../..//table/tbody//td[3]/p[contains(text(), "'.$pluginCode.'")]';
    }

    public function 独自プラグイン_有効化($pluginCode)
    {
        $this->ページ遷移を伴うクリック(function () use ($pluginCode) {
            $this->独自プラグイン_ボタンクリック($pluginCode, '有効化');
        });
        $this->tester->waitForText('有効にしました。', 30, self::完了メッセージ);

        return $this;
    }

    public function 独自プラグイン_無効化($pluginCode)
    {
        $this->ページ遷移を伴うクリック(function () use ($pluginCode) {
            $this->独自プラグイン_ボタンクリック($pluginCode, '無効化');
        });
        $this->tester->waitForText('無効にしました。', 30, self::完了メッセージ);

        return $this;
    }

    public function 独自プラグイン_削除($pluginCode)
    {
        $this->独自プラグイン_ボタンクリック($pluginCode, '削除');
        $this->tester->waitForElementVisible(['css' => '#localPluginDeleteModal .modal-footer a']);
        $this->ページ遷移を伴うクリック(function () {
            $this->tester->click(['css' => '#localPluginDeleteModal .modal-footer a']);
        });

        return $this;
    }

    public function 独自プラグイン_アップデート($pluginCode, $pluginDirName)
    {
        $this->tester->compressPlugin($pluginDirName, codecept_data_dir('plugins'));
        $this->tester->attachFile(['xpath' => $this->独自プラグイン_セレクタ($pluginCode).'/../td[5]//input[@type="file"]'], 'plugins/'.$pluginDirName.'.tgz');
        $this->ページ遷移を伴うクリック(function () use ($pluginCode) {
            $this->tester->click(['xpath' => $this->独自プラグイン_セレクタ($pluginCode).'/../td[5]//button']);
        });
        $this->tester->waitForText('アップデートしました。', 30, self::完了メッセージ);

        return $this;
    }

    private function 独自プラグイン_ボタンクリック($pluginCode, $label)
    {
        $xpathStr = $this->独自プラグイン_セレクタ($pluginCode).'/../td[6]//i[@data-bs-original-title="'.$label.'"]/parent::node()';
        $this->tester->scrollTo(['xpath' => $xpathStr]);
        $this->tester->executeJS(
            "document.querySelectorAll('.tooltip, .alert-dismissible').forEach(e => e.remove());"
            .'document.evaluate(arguments[0], document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click()',
            [$xpathStr]
        );

        return $this;
    }

    private function 独自プラグイン_セレクタ($pluginCode)
    {
        return '//*[@id="page_admin_store_plugin"]//div/h5[contains(text(), "ユーザー独自プラグイン")]/../..//table/tbody//td[3][contains(text(), "'.$pluginCode.'")]/';
    }

    /**
     * ページ遷移を伴うクリック操作を実行する。
     *
     * 1. JS マーカーを設定
     * 2. クリック操作を実行
     * 3. マーカーが消えるのを待機（= ページ再読み込み検出）
     * 4. マーカーが消えない場合はクリックをリトライ
     *
     * リトライが必要になるケース:
     * - ページの JavaScript（function.js の data-method="post" ハンドラ）が
     *   まだ初期化されていない状態でクリックした場合
     * - フラッシュメッセージ等に遮られてクリックが届かなかった場合
     *
     * クリックハンドラが発火済み（pointer-events:none が付与された）場合は
     * フォーム送信済みなので再クリックせず、サーバー応答を待つ。
     * サーバー側の clearCache() 等で 10 秒以上かかる場合がある。
     */
    private function ページ遷移を伴うクリック(callable $clickAction)
    {
        $maxRetries = 3;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $this->tester->executeJS('window.__eccubeNavMarker = true');
            } catch (\Exception $e) {
                // マーカー設定失敗 = 前回のクリックでページ遷移中
                break;
            }

            // function.js の click ハンドラが DOMContentLoaded 後にバインドされるため、
            // ページの全リソース読み込み完了を待ってからクリックする
            $this->tester->executeInSelenium(function ($webDriver) {
                $webDriver->wait(10)->until(function ($driver) {
                    return $driver->executeScript('return document.readyState') === 'complete';
                });
            });

            $clickAction();

            // function.js の click ハンドラが発火すると対象の <a> に pointer-events:none が
            // 設定される。これが検出できればフォーム送信は成功しており、あとはサーバー応答
            // （clearCache 等で時間がかかる場合がある）を待つだけなので再クリックしない。
            $formSubmitted = false;
            try {
                $formSubmitted = (bool) $this->tester->executeJS(
                    "return document.querySelector('a[token-for-anchor][style*=\"pointer-events\"]') !== null"
                );
            } catch (\Exception $e) {
                // JS 実行失敗 = ページ遷移中
                break;
            }

            $navigated = false;
            // フォーム送信済みならサーバー応答待ち (最大60秒)、未送信なら短めに待ってリトライ
            $timeout = $formSubmitted ? 60 : (($attempt < $maxRetries) ? 5 : 30);

            $this->tester->executeInSelenium(function ($webDriver) use (&$navigated, $timeout) {
                $deadline = microtime(true) + $timeout;
                while (microtime(true) < $deadline) {
                    try {
                        $result = $webDriver->executeScript('return window.__eccubeNavMarker === true');
                        if (!$result) {
                            $navigated = true;
                            break;
                        }
                    } catch (\Exception $e) {
                        $navigated = true; // JS 実行エラー = ページ遷移中
                        break;
                    }
                    usleep(500000); // 500ms
                }
            });

            if ($navigated || $formSubmitted) {
                break;
            }
        }

        $this->atPage('インストールプラグイン一覧オーナーズストア');

        return $this;
    }
}
