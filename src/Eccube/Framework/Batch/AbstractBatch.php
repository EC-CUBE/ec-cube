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

namespace Eccube\Framework\Batch;

/**
 * バッチ処理用 の基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
abstract class AbstractBatch
{
    /**
     * バッチ処理を実行する
     *
     * @param  mixed $argv コマンドライン引数
     * @return mixed バッチの実行結果
     */
    public function execute($argv = '')
    {
    }
}
