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

namespace Eccube\Service;

use Eccube\Common\EccubeConfig;
use RobThree\Auth\TwoFactorAuth;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class TwoFactorAuthService
{
    /**
     * @var int デフォルトの認証の有効日数
     */
    public const DEFAULT_EXPIRE_DATE = 14;

    /**
     * @var string Cookieに保存する時のキー名
     */
    public const DEFAULT_COOKIE_NAME = 'eccube_2fa';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Encoder
     */
    protected $encoder;

    /**
     * @var string
     */
    protected $cookieName = self::DEFAULT_COOKIE_NAME;

    /**
     * @var int
     */
    protected $expire = self::DEFAULT_EXPIRE_DATE;

    /**
     * @var TwoFactorAuth
     */
    protected $tfa;

    /**
     * constructor.
     *
     * @param ContainerInterface $container
     * @param EccubeConfig $eccubeConfig
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        ContainerInterface $container,
        EccubeConfig $eccubeConfig,
        EncoderFactoryInterface $encoderFactory,
        RequestStack $requestStack
    ) {
        $this->container = $container;
        $this->eccubeConfig = $eccubeConfig;
        $this->encoderFactory = $encoderFactory;
        $this->requestStack = $requestStack;
        $this->request = $requestStack->getCurrentRequest();
        $this->encoder = $this->encoderFactory->getEncoder('Eccube\\Entity\\Member');
        $this->tfa = new TwoFactorAuth();

        if ($this->eccubeConfig->get('eccube_2fa_cookie_name')) {
            $this->cookieName = $this->eccubeConfig->get('eccube_2fa_cookie_name');
        }

        $expire = $this->eccubeConfig->get('eccube_2fa_expire');
        if ($expire || $expire === '0') {
            $this->expire = (int) $expire;
        }
    }

    /**
     * @param Eccube\Entity\Member
     *
     * @return boolean
     */
    public function isAuth($Member)
    {
        if (($json = $this->request->cookies->get($this->cookieName))) {
            $configs = json_decode($json);
            $encodedString = $this->encoder->encodePassword($Member->getId().$Member->getTwoFactorAuthKey(), $Member->getSalt());
            if (
                $configs
                && isset($configs->{$Member->getId()})
                && ($config = $configs->{$Member->getId()})
                && property_exists($config, 'key')
                && $config->key === $encodedString
                && (
                    $this->expire == 0
                    || (property_exists($config, 'date') && ($config->date && $config->date > date('U', strtotime('-'.$this->expire.' day'))))
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Eccube\Entity\Member
     *
     * @return Cookie
     */
    public function createAuthedCookie($Member)
    {
        $encodedString = $this->encoder->encodePassword($Member->getId().$Member->getTwoFactorAuthKey(), $Member->getSalt());

        $configs = json_decode('{}');
        if (($json = $this->request->cookies->get($this->cookieName))) {
            $configs = json_decode($json);
        }
        $configs->{$Member->getId()} = [
            'key' => $encodedString,
            'date' => time(),
        ];

        $cookie = new Cookie(
            $this->cookieName, // name
            json_encode($configs), // value
            ($this->expire == 0 ? 0 : time() + ($this->expire * 24 * 60 * 60)), // expire
            $this->request->getBasePath().'/'.$this->eccubeConfig->get('eccube_admin_route'), // path
            null, // domain
            ($this->eccubeConfig->get('eccube_force_ssl') ? true : false), // secure
            true, // httpOnly
            false, // raw
            ($this->eccubeConfig->get('eccube_force_ssl') ? Cookie::SAMESITE_NONE : null) // sameSite
        );

        return $cookie;
    }

    /**
     * @param Eccube\Entity\Member
     * @param string
     *
     * @return boolean
     */
    public function verifyCode($authKey, $token)
    {
        return $this->tfa->verifyCode($authKey, $token, 2);
    }

    /**
     * @return string
     */
    public function createSecret()
    {
        return $this->tfa->createSecret();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $enabled = $this->eccubeConfig->get('eccube_2fa_enabled');
        if (is_string($enabled) && $enabled === '0' || $enabled === false) {
            return false;
        }

        return true;
    }
}
