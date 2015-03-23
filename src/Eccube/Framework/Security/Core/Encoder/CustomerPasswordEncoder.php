<?php

namespace Eccube\Framework\Security\Core\Encoder;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class CustomerPasswordEncoder implements PasswordEncoderInterface
{

    /* @var $config array */
    public $config;

    public function __construct (array $config)
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
