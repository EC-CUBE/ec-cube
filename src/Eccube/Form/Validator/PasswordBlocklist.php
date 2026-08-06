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

namespace Eccube\Form\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * NIST SP 800-63B-4 に基づき, 推測されやすいパスワード(ブロックリスト)を拒否する制約.
 *
 * 照合対象のリストは eccube_password_blocklist で指定したファイルから読み込む.
 */
#[\Attribute]
class PasswordBlocklist extends Constraint
{
    public string $message = 'form_error.password_blocklisted';
}
