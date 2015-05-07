<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Security\Core\Encoder;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordEncoder implements PasswordEncoderInterface
{

    /* @var $config array */
    public $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Encodes the raw password.
     *
     * @param string $raw  The password to encode
     * @param string $salt The salt
     *
     * @return string The encoded password
     */
    public function encodePassword($raw, $salt)
    {
        if ($salt == '') {
            $salt = $this->config['auth_magic'];
        }
        if ($this->config['auth_type'] == 'PLAIN') {
            $res = $raw;
        } else {
            $res = hash_hmac($this->config['password_hash_algos'], $raw . ':' . $this->config['auth_magic'], $salt);
        }

        return $res;
    }

    /**
     * Checks a raw password against an encoded password.
     *
     * @param string $encoded An encoded password
     * @param string $raw     A raw password
     * @param string $salt    The salt
     *
     * @return bool true if the password is valid, false otherwise
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($encoded == '') {
            return false;
        }

        if ($this->config['auth_type'] == 'PLAIN') {
            if ($raw === $encoded) {
                return true;
            }
        } else {
            // 旧バージョン(2.11未満)からの移行を考慮
            if (empty($salt)) {
                $hash = sha1($raw . ':' . $this->config['auth_magic']);
            } else {
                $hash = $this->encodePassword($raw, $salt);
            }

            if ($hash === $encoded) {
                return true;
            }
        }

        return false;
    }

}
