<?php

use Eccube\Kernel;
use Eccube\Service\SystemService;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

// システム要件チェック
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    die('Your PHP installation is too old. EC-CUBE requires at least PHP 7.4.0. See the <a href="https://doc4.ec-cube.net/quickstart/requirement" target="_blank">system requirements</a> page for more information.');
}

$autoload = __DIR__.'/vendor/autoload.php';

if (!file_exists($autoload) && !is_readable($autoload)) {
    die('Composer is not installed.');
}
require $autoload;

// The check is to ensure we don't use .env in production
$dotenvExists = file_exists(__DIR__.'/.env')
    || file_exists(__DIR__.'/.env.local')
    || file_exists(__DIR__.'/.env.local.php');

if (!isset($_SERVER['APP_ENV'])) {
    // OS 環境変数 APP_ENV が未設定の環境: .env を設定の源泉として読み込む。
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }

    if ($dotenvExists) {
        // bootEnv は .env.local.php（dump-env の最適化済みスナップショット）を優先し、
        // 無ければ .env → .env.local → .env.$env → .env.$env.local をカスケード読み込みする。
        // 第4引数 true = .env 系の値が既存値より優先（従来の overload 相当）。
        (new Dotenv())->bootEnv(__DIR__.'/.env', 'dev', ['test'], true);

        if (str_contains($_SERVER['DATABASE_URL'] ?? '', 'sqlite') && !extension_loaded('pdo_sqlite')) {
            (new Dotenv())->overload(__DIR__.'/.env.install');
        }
    } else {
        (new Dotenv())->overload(__DIR__.'/.env.install');
    }
} elseif (class_exists(Dotenv::class) && $dotenvExists) {
    // OS 環境変数 APP_ENV が設定済みの環境（Docker など）でも .env を読み込む。
    // 第4引数 false = 既存の OS 環境変数（Docker で設定済みのもの）は上書きしない。
    // OS 環境変数を保護しつつ、.env 側の非 OS 変数（ECCUBE_TEMPLATE_CODE 等）は反映される。
    // これにより管理画面からのテンプレート切り替えが .env への書き込みで反映される。
    // putenv は使わない（スレッド安全性のため）ので、env() 関数（$_ENV / $_SERVER 優先）と整合する。
    (new Dotenv())->bootEnv(__DIR__.'/.env', 'dev', ['test'], false);
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
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST |Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO ^ Request::HEADER_X_FORWARDED_HOST);
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

            http_response_code(503);
            require __DIR__.'/maintenance.php';
            return;
        }
    }
}

$kernel = new Kernel($env, $debug);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
