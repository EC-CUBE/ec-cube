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

namespace Eccube\Session\Storage\Handler;

use Skorp\Dissua\SameSite;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;

class SameSiteNoneCompatSessionHandler extends StrictSessionHandler
{
    /** @var \SessionHandlerInterface */
    private $handler;
    /** @var bool */
    private $doDestroy;
    /** @var string */
    private $sessionName;
    /** @var string|null */
    private $prefetchId;
    /** @var string|null */
    private $prefetchData;
    /** @var string */
    private $newSessionId;
    /** @var string|null */
    private $igbinaryEmptyData;

    /**
     *  {@inheritdoc}
     */
    public function __construct(\SessionHandlerInterface $handler)
    {
        $this->handler = $handler;

        ini_set('session.cookie_secure', $this->getCookieSecure());
        ini_set('session.cookie_samesite', $this->getCookieSameSite());
        ini_set('session.cookie_path', $this->getCookiePath());
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        $this->sessionName = $sessionName;
        // see https://github.com/symfony/symfony/blob/2adc85d49cbe14e346068fa7e9c2e1f08ab31de6/src/Symfony/Component/HttpFoundation/Session/Storage/Handler/AbstractSessionHandler.php#L35-L37
        if (!headers_sent() && !ini_get('session.cache_limiter') && '0' !== ini_get('session.cache_limiter')) {
            header(sprintf('Cache-Control: max-age=%d, private, must-revalidate', 60 * (int) ini_get('session.cache_expire')));
        }

        return $this->handler->open($savePath, $sessionName);
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead($sessionId)
    {
        return $this->handler->read($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function updateTimestamp($sessionId, $data)
    {
        return $this->write($sessionId, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($sessionId, $data)
    {
        return $this->handler->write($sessionId, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @see https://github.com/symfony/symfony/blob/2adc85d49cbe14e346068fa7e9c2e1f08ab31de6/src/Symfony/Component/HttpFoundation/Session/Storage/Handler/AbstractSessionHandler.php#L126-L167
     */
    public function destroy($sessionId)
    {
        if (\PHP_VERSION_ID < 70000) {
            $this->prefetchData = null;
        }
        if (!headers_sent() && filter_var(ini_get('session.use_cookies'), FILTER_VALIDATE_BOOLEAN)) {
            if (!$this->sessionName) {
                throw new \LogicException(sprintf('Session name cannot be empty, did you forget to call "parent::open()" in "%s"?.', \get_class($this)));
            }
            $sessionCookie = sprintf(' %s=', urlencode($this->sessionName));
            $sessionCookieWithId = sprintf('%s%s;', $sessionCookie, urlencode($sessionId));
            $sessionCookieFound = false;
            $otherCookies = [];
            foreach (headers_list() as $h) {
                if (0 !== stripos($h, 'Set-Cookie:')) {
                    continue;
                }
                if (11 === strpos($h, $sessionCookie, 11)) {
                    $sessionCookieFound = true;

                    if (11 !== strpos($h, $sessionCookieWithId, 11)) {
                        $otherCookies[] = $h;
                    }
                } else {
                    $otherCookies[] = $h;
                }
            }
            if ($sessionCookieFound) {
                header_remove('Set-Cookie');
                foreach ($otherCookies as $h) {
                    header($h, false);
                }
            } else {
                if (\PHP_VERSION_ID < 70300) {
                    setcookie($this->sessionName, '', 0, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), filter_var(ini_get('session.cookie_secure'), FILTER_VALIDATE_BOOLEAN), filter_var(ini_get('session.cookie_httponly'), FILTER_VALIDATE_BOOLEAN));
                } else {
                    setcookie($this->sessionName, '',
                              [
                                  'expires' => 0,
                                  'path' => $this->getCookiePath(),
                                  'domain' => ini_get('session.cookie_domain'),
                                  'secure' => filter_var(ini_get('session.cookie_secure'), FILTER_VALIDATE_BOOLEAN),
                                  'httponly' => filter_var(ini_get('session.cookie_httponly'), FILTER_VALIDATE_BOOLEAN),
                                  'samesite' => $this->getCookieSameSite(),
                              ]
                    );
                }
            }
        }

        return $this->newSessionId === $sessionId || $this->doDestroy($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDestroy($sessionId)
    {
        $this->doDestroy = false;

        return $this->handler->destroy($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return $this->handler->close();
    }

    /**
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return $this->handler->gc($maxlifetime);
    }

    /**
     * @return string
     */
    public function getCookieSameSite()
    {
        if ($this->shouldSendSameSiteNone() && \PHP_VERSION_ID >= 70300 && $this->getCookieSecure()) {
            return Cookie::SAMESITE_NONE;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCookiePath()
    {
        $cookiePath = env('ECCUBE_COOKIE_PATH', '/');
        if ($this->shouldSendSameSiteNone() && \PHP_VERSION_ID < 70300 && $this->getCookieSecure()) {
            return $cookiePath.'; SameSite='.Cookie::SAMESITE_NONE;
        }

        return $cookiePath;
    }

    /**
     * @return string
     */
    public function getCookieSecure()
    {
        $request = Request::createFromGlobals();

        return $request->isSecure() ? '1' : '0';
    }

    /**
     * @return bool
     */
    private function shouldSendSameSiteNone()
    {
        $userAgent = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : null;

        return SameSite::handle($userAgent);
    }
}
