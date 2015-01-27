<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Rss;

use Eccube\Application;
use Eccube\Page\AbstractPage;
use Eccube\Framework\Helper\DbHelper;
use Eccube\Framework\Helper\NewsHelper;
use Eccube\Framework\Util\Utils;
use Eccube\Framework\View\SiteView;

/**
 * RSS のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Index extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_mainpage = 'rss/index.tpl';
        $this->encode = 'UTF-8';
        $this->description = '新着情報';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $objView = new SiteView(false);

        //新着情報を取得
        $arrNews = $this->lfGetNews();

        //キャッシュしない(念のため)
        header('pragma: no-cache');

        //XMLテキスト(これがないと正常にRSSとして認識してくれないツールがあるため)
        header('Content-type: application/xml');

        //新着情報をセット
        $this->arrNews = $arrNews;

        //店名をセット
        $this->site_title = $arrNews[0]['shop_name'];

        //代表Emailアドレスをセット
        $this->email = $arrNews[0]['email'];

        //セットしたデータをテンプレートファイルに出力
        $objView->assignobj($this);

        //画面表示
        $objView->display($this->tpl_mainpage, true);
    }

    /**
     * 新着情報を取得する
     *
     * @return array $arrNews 取得結果を配列で返す
     */
    public function lfGetNews()
    {
        /* @var $objNews NewsHelper */
        $objNews = Application::alias('eccube.helper.news');
        $arrNews = $objNews->getList();

        /* @var $objDb DbHelper */
        $objDb = Application::alias('eccube.helper.db');
        $arrInfo = $objDb->getBasisData();

        // RSS用に変換
        foreach (array_keys($arrNews) as $key) {
            $netUrlHttpUrl = new \Net_URL(HTTP_URL);

            $row =& $arrNews[$key];
            $row['shop_name'] = $arrInfo['shop_name'];
            $row['email'] = $arrInfo['email04'];
            // 日付
            $row['news_date'] = date('r', strtotime($row['news_date']));
            // 新着情報URL
            if (Utils::isBlank($row['news_url'])) {
                $row['news_url'] = HTTP_URL;
            } elseif ($row['news_url'][0] == '/') {
                // 変換(絶対パス→URL)
                $netUrl = new \Net_URL($row['news_url']);
                $netUrl->protocol = $netUrlHttpUrl->protocol;
                $netUrl->user = $netUrlHttpUrl->user;
                $netUrl->pass = $netUrlHttpUrl->pass;
                $netUrl->host = $netUrlHttpUrl->host;
                $netUrl->port = $netUrlHttpUrl->port;
                $row['news_url'] = $netUrl->getUrl();
            }
        }

        return $arrNews;
    }
}
