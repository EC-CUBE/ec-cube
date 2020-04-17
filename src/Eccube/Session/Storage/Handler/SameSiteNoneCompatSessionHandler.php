<?php

namespace Eccube\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;

class SameSiteNoneCompatSessionHandler extends StrictSessionHandler
{
    private $sessionName;
    private $prefetchId;
    private $prefetchData;
    private $newSessionId;
    private $igbinaryEmptyData;

    public function __construct(\SessionHandlerInterface $handler)
    {
        parent::__construct($handler);
        $this->handler = $handler;
        // TODO UA や PHP バージョンで分岐する
        ini_set('session.cookie_path', '/; SameSite=None');
        ini_set('session.cookie_secure', 1);
    }

    public function open($savePath, $sessionName)
    {
        $this->sessionName = $sessionName;
        return parent::open($savePath, $sessionName);
    }
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
                                  'path' => '/', // TODO
                                  'domain' => ini_get('session.cookie_domain'),
                                  'secure' =>  filter_var(ini_get('session.cookie_secure'), FILTER_VALIDATE_BOOLEAN),
                                  'httponly' => filter_var(ini_get('session.cookie_httponly'), FILTER_VALIDATE_BOOLEAN),
                                  'samesite' => 'None' // TODO UA で分岐する
                              ]
                    );
                }
            }
        }

        return $this->newSessionId === $sessionId || $this->doDestroy($sessionId);
    }
}
