<?php

namespace Eccube\EventListener;

use Eccube\Application;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * リクエストログ出力ため Listener
 *
 * ログ出力を除外したいキーは log.yml の exclude_keys で設定します.
 * addExcludeKey(), removeExcludeKey() でも設定できます.
 *
 * @author Kentaro Ohkouchi
 */
class RequestDumpListener implements EventSubscriberInterface
{
    private $app;
    private $excludeKeys;

    /**
     * Constructor function.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->excludeKeys = $app['config']['log']['exclude_keys'];
    }

    /**
     * Kernel request listener callback.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $log = '** before *****************************************:'.PHP_EOL;
        $request = $event->getRequest();
        $log .= $this->logRequest($request);
        $Session = $request->getSession();
        if ($request->hasSession()) {
            $log .= $this->logSession($Session);
        }
        $this->app->log($log, array(), Logger::DEBUG);
        log_debug($log);
    }

    /**
     * Kernel response listener callback.
     *
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $log = '** after *****************************************:'.PHP_EOL;
        $response = $event->getResponse();
        $log .= $this->logResponse($response);
        $request = $event->getRequest();
        $log .= $this->logRequest($request);
        $Session = $request->getSession();
        if ($request->hasSession()) {
            $log .= $this->logSession($Session);
        }
        $this->app->log($log, array(), Logger::DEBUG);
        log_debug($log);
    }

    /**
     * Return the events to subscribe to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onResponse',
        );
    }

    /**
     * ログ出力を除外するキーを追加します.
     *
     * @param string $key 除外対象のキー
     */
    protected function addExcludeKey($key)
    {
        $this->excludeKeys[] = $key;
    }

    /**
     * ログ出力を除外するキーを削除します.
     *
     * @param string $key 削除対象のキー
     */
    protected function removeExcludeKey($key)
    {
        if (array_key_exists($key, $this->excludeKeys)) {
            unset($this->excludeKeys[$key]);
        }
    }

    /**
     * Request のログを出力する.
     *
     * @param Request $request
     * @return string Request のログ
     */
    protected function logRequest(Request $request)
    {
        $log = '';
        $log .= $this->logKeyValuePair('REMOTE_ADDR', $request->getClientIp());
        $log .= $this->logKeyValuePair('SCRIPT_NAME', $request->getScriptName());
        $log .= $this->logKeyValuePair('PATH_INFO', $request->getPathInfo());
        $log .= $this->logKeyValuePair('BASE_PATH', $request->getBasePath());
        $log .= $this->logKeyValuePair('BASE_URL', $request->getBaseUrl());
        $log .= $this->logKeyValuePair('SCHEME', $request->getScheme());
        $log .= $this->logKeyValuePair('REMOTE_USER', $request->getUser());
        $log .= $this->logKeyValuePair('HTTP_HOST', $request->getSchemeAndHttpHost());
        $log .= $this->logKeyValuePair('REQUEST_URI', $request->getRequestUri());
        $log .= $this->logKeyValuePair('METHOD', $request->getRealMethod());
        $log .= $this->logKeyValuePair('LOCALE', $request->getLocale());
        // $log .= $this->logArray($request->server->all(), '[server]'); // 大量にログ出力される...
        $log .= $this->logArray($request->headers->all(), '[header]');
        $log .= $this->logArray($request->query->all(), '[get]');
        $log .= $this->logArray($request->request->all(), '[post]');
        $log .= $this->logArray($request->attributes->all(), '[attributes]');
        $log .= $this->logArray($request->cookies->all(), '[cookie]');
        $log .= $this->logArray($request->files->all(), '[files]');

        return $log;
    }

    /**
     * Response のログを出力する.
     *
     * @param Response $response
     * @return string Response のログ
     */
    protected function logResponse(Response $response)
    {
        $log = '';
        $log .= $this->logKeyValuePair('HTTP_STATUS', $response->getStatusCode());

        return $log;
    }

    /**
     * Session のログを出力する.
     */
    protected function logSession(SessionInterface $Session)
    {
        return $this->logArray($Session->all(), '[session]');
    }

    /**
     * 配列をログ出力する.
     */
    protected function logArray(array $values, $prefix = '')
    {
        $log = '';
        foreach ($values as $key => $val) {
            $log .= $this->logKeyValuePair($key, $val, $prefix);
        }

        return $log;
    }

    /**
     * キーと値のペアをログ出力する.
     *
     * 除外キーに該当する値は, マスクをかける
     */
    protected function logKeyValuePair($key, $value, $prefix = '')
    {
        if (in_array($key, $this->excludeKeys)) {
            return '';
        }
        if (is_null($value) || is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            $copy_value = $value;
        } elseif (is_object($value)) {
            try {
                $copy_value = '[object '.serialize($value).']';
            } catch (\Exception $e) {
                return $e->getMessage().PHP_EOL;
            }
        } else {
            $copy_value = $value;
            if (is_array($copy_value)) {
                foreach ($copy_value as $key => &$val) {
                    if (in_array($key, $this->excludeKeys)
                        && $prefix != '[header]') { // XXX header にもマスクがかかってしまう
                        $val = '******';
                    }
                }
            }
            try {
                $copy_value = '['.serialize($copy_value).']';
            } catch (\Exception $e) {
                return $e->getMessage().PHP_EOL;
            }
        }

        return '  '.$prefix.' '.$key.'='.$copy_value.PHP_EOL;
    }
}
