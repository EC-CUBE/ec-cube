<?php

use Eccube\Kernel;
use Eccube\Service\SystemService;
use Symfony\Component\ErrorHandler\Debug;
use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

// システム要件チェック
if (version_compare(PHP_VERSION, '7.3.0') < 0) {
    die('Your PHP installation is too old. EC-CUBE requires at least PHP 7.3.0. See the <a href="http://www.ec-cube.net/product/system.php" target="_blank">system requirements</a> page for more information.');
}

$autoload = __DIR__.'/vendor/autoload.php';

if (!file_exists($autoload) && !is_readable($autoload)) {
    die('Composer is not installed.');
}
require $autoload;

// The check is to ensure we don't use .env in production
if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }

    if (file_exists(__DIR__.'/.env')) {
        (new Dotenv(__DIR__))->overload();

        if (strpos(getenv('DATABASE_URL'), 'sqlite') !== false && !extension_loaded('pdo_sqlite')) {
            (new Dotenv(__DIR__, '.env.install'))->overload();
        }
    } else {
        (new Dotenv(__DIR__, '.env.install'))->overload();
    }
}
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$env = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'dev';
$debug = isset($_SERVER['APP_DEBUG']) ? $_SERVER['APP_DEBUG'] : ('prod' !== $env);

if ($debug) {
    umask(0000);

    Debug::enable();
}

$trustedProxies = isset($_SERVER['TRUSTED_PROXIES']) ? $_SERVER['TRUSTED_PROXIES'] : false;
if ($trustedProxies) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

$trustedHosts = isset($_SERVER['TRUSTED_HOSTS']) ? $_SERVER['TRUSTED_HOSTS'] : false;
if ($trustedHosts) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

$request = Request::createFromGlobals();

$maintenanceFile = env('ECCUBE_MAINTENANCE_FILE_PATH', __DIR__.'/.maintenance');

if (file_exists($maintenanceFile)) {
    $pathInfo = \rawurldecode($request->getPathInfo());
    $adminPath = env('ECCUBE_ADMIN_ROUTE', 'admin');
    $adminPath = '/'.\trim($adminPath, '/').'/';
    if (\strpos($pathInfo, $adminPath) !== 0) {
        $maintenanceContents = file_get_contents($maintenanceFile);
        $maintenanceToken = explode(':', $maintenanceContents)[1] ?? null;
        $tokenInCookie = $request->cookies->get(SystemService::MAINTENANCE_TOKEN_KEY);
        if ($tokenInCookie === null || $tokenInCookie !== $maintenanceToken) {
            $locale = env('ECCUBE_LOCALE');
            $templateCode = env('ECCUBE_TEMPLATE_CODE');
            $baseUrl = \htmlspecialchars(\rawurldecode($request->getBaseUrl()), ENT_QUOTES);

            header('HTTP/1.1 503 Service Temporarily Unavailable');
            require __DIR__.'/maintenance.php';
            return;
        }
    }
}

$kernel = new Kernel($env, $debug);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
