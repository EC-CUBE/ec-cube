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

namespace Eccube\Stream\Filter;

use Eccube\Util\StringUtil;

class ConvertLineFeedFilter extends \php_user_filter
{
    /**
     * @param resource $in
     * @param resource $out
     * @param int $consumed
     * @param bool $closing
     */
    public function filter($in, $out, &$consumed, $closing): int
    {
        while ($bucket = \stream_bucket_make_writeable($in)) {
            $bucket->data = StringUtil::convertLineFeed($bucket->data);
            $consumed += $bucket->datalen;
            \stream_bucket_append($out, $bucket);
        }

        return \PSFS_PASS_ON;
    }
}
