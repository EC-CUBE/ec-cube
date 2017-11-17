<?php

if (php_sapi_name() !== 'cli') {
    exit(1);
}

set_time_limit(0);
ini_set('display_errors', 1);

define('COMPOSER_FILE', 'composer.phar');
define('COMPOSER_SETUP_FILE', 'composer-setup.php');

setUseAnsi($argv);

$argv = is_array($argv) ? $argv : array();

$argv[1] = isset($argv[1]) ? $argv[1] : null;
$argv[2] = isset($argv[2]) ? $argv[2] : null;

if (in_array('--help', $argv) || empty($argv[1])) {
    displayHelp($argv);
    exit(0);
}

if (in_array('-v', $argv) || in_array('--version', $argv)) {
    require __DIR__.'/src/Eccube/Common/Constant.php';
    echo 'EC-CUBE '.Eccube\Common\Constant::VERSION.PHP_EOL;
    exit(0);
}

out('EC-CUBE3 installer use database driver of ', null, false);

switch ($argv[1]) {
    case 'mysql':
        $database = 'mysql';
        break;
    case 'pgsql':
    case 'postgres':
    case 'postgresql':
        $database = 'pgsql';
        break;
    case 'sqlite':
    case 'sqlite3':
    default:
        $database = 'sqlite';
}
out($database);

if ($argv[2] != 'none') {
    composerSetup();
    composerInstall();
}

$loader = require __DIR__.'/autoload.php';

initializeDefaultVariables($database);

if (in_array('-V', $argv) || in_array('--verbose', $argv)) {
    displayEnvironmentVariables();
}

out('update permissions...');
updatePermissions($argv);

if (!in_array('--skip-createdb', $argv)) {
    $params = getDatabaseConfig();
    if ($params['driver'] === 'pdo_sqlite') {
        $dbname = $params['path'];
    } else {
        $dbname = $params['dbname'];
    }
    $conn = createConnection($params, true);
    createDatabase($conn, $dbname);
}

out('Created database connection...', 'info');

$conn = createConnection(getDatabaseConfig());

if (!in_array('--skip-initdb', $argv)) {
    $em = createEntityManager($conn);
    initializeDatabase($em);
}

copyConfigFiles();
replaceConfigFiles();

out('EC-CUBE3 install finished successfully!', 'success');
$root_urlpath = env('ECCUBE_ROOT_URLPATH');
if (PHP_VERSION_ID >= 50400 && empty($root_urlpath)) {
    out('PHP built-in web server to run applications, `php -S localhost:8080`', 'info');
    out('Open your browser and access the http://localhost:8080/', 'info');
}
exit(0);

function displayHelp($argv)
{
    echo <<<EOF
EC-CUBE3 Installer
------------------
Usage:
${argv[0]} [mysql|pgsql|sqlite3] [none] [options]

Arguments[1]:
Specify database types

Arguments[2]:
Specifying the "none" to skip the installation of Composer

Options:
-v, --version        print ec-cube version
-V, --verbose        enable verbose output
--skip-createdb      skip to create database
--skip-initdb        skip to initialize database
--help               this help
--ansi               force ANSI color output
--no-ansi            disable ANSI color output

Environment variables:

EOF;
    foreach (getExampleVariables() as $name => $value) {
        echo $name.'='.$value.PHP_EOL;
    }
}

function initializeDefaultVariables($database)
{
    // heroku用定義
    $database_url = env('DATABASE_URL');
    if ($database_url) {
        $url = parse_url($database_url);
        putenv('ECCUBE_DB_DEFAULT='.$url['scheme']);
        putenv('ECCUBE_DB_HOST='.$url['host']);
        putenv('ECCUBE_DB_DATABASE='.substr($url['path'], 1));
        putenv('ECCUBE_DB_USERNAME='.$url['user']);
        putenv('ECCUBE_DB_PORT='.$url['port']);
        putenv('ECCUBE_DB_PASSWORD='.$url['pass']);
    }

    switch ($database) {
        case 'pgsql':
            putenv('ECCUBE_ROOTUSER='.env('ECCUBE_ROOTUSER', env('ECCUBE_DB_USERNAME', 'postgres')));
            putenv('ECCUBE_ROOTPASS='.env('ECCUBE_ROOTPASS', env('ECCUBE_DB_PASSWORD', 'password')));
            break;
        case 'mysql':
            putenv('ECCUBE_ROOTUSER='.env('ROOTUSER', env('ECCUBE_DB_USERNAME', 'root')));
            putenv('ECCUBE_ROOTPASS='.env('ROOTPASS', env('ECCUBE_DB_PASSWORD', 'password')));
            break;
        case 'sqlite':
            putenv('ECCUBE_DB_DATABASE='.__DIR__.'/app/config/eccube/eccube.db');
            break;
        default:
    }
    putenv('ECCUBE_DB_DEFAULT='.$database);
    putenv('ECCUBE_AUTH_MAGIC='.env('ECCUBE_AUTH_MAGIC', \Eccube\Util\Str::random(32)));
    putenv('ECCUBE_ADMIN_USER='.env('ECCUBE_ADMIN_USER', 'admin'));
    putenv('ECCUBE_ADMIN_PASS='.env('ECCUBE_ADMIN_PASS', 'password'));
    putenv('ECCUBE_ADMIN_MAIL='.env('ECCUBE_ADMIN_MAIL', 'admin@example.com'));
    putenv('ECCUBE_SHOP_NAME='.env('ECCUBE_SHOP_NAME', 'EC-CUBE SHOP'));
}

function getExampleVariables()
{
    return [
        'ECCUBE_ADMIN_USER' => 'admin',
        'ECCUBE_ADMIN_MAIL' => 'admin@example.com',
        'ECCUBE_SHOP_NAME' => 'EC-CUBE SHOP',
        'ECCUBE_ROOTUSER' => 'root|postgres',
        'ECCUBE_ROOTPASS' => 'password',
        'ECCUBE_AUTH_MAGIC' => '<auth magic>',
        'ECCUBE_FORCE_SSL' => 'false',
        'ECCUBE_ADMIN_ALLOW_HOSTS' => '[]',
        'ECCUBE_COOKIE_LIFETIME' => '0',
        'ECCUBE_COOKIE_NAME' => 'eccube',
        'ECCUBE_LOCALE' => 'ja',
        'ECCUBE_TIMEZONE' => 'Asia/Tokyo',
        'ECCUBE_CURRENCY' => 'JPY',
        'ECCUBE_ROOT_URLPATH' => '<eccube root url>',
        'ECCUBE_TEMPLATE_CODE' => 'default',
        'ECCUBE_ADMIN_ROUTE' => 'admin',
        'ECCUBE_USER_DATA_ROUTE' => 'user_data',
        'ECCUBE_TRUSTED_PROXIES_CONNECTION_ONLY' => 'false',
        'ECCUBE_TRUSTED_PROXIES' => '["127.0.0.1/8", "::1"]',
        'ECCUBE_DB_DEFAULT' => 'mysql',
        'ECCUBE_DB_HOST' => '127.0.0.1',
        'ECCUBE_DB_PORT' => '<database port>',
        'ECCUBE_DB_DATABASE' => 'eccube_db',
        'ECCUBE_DB_USERNAME' => 'eccube_db_user',
        'ECCUBE_DB_PASSWORD' => 'password',
        'ECCUBE_DB_CHARASET' => 'utf8',
        'ECCUBE_DB_COLLATE' => 'utf8_general_ci',
        'ECCUBE_MAIL_TRANSPORT' => 'smtp',
        'ECCUBE_MAIL_HOST' => 'localhost',
        'ECCUBE_MAIL_PORT' => '1025',
        'ECCUBE_MAIL_USERNAME' => '<SMTP AUTH user>',
        'ECCUBE_MAIL_PASSWORD' => '<SMTP AUTH password>',
        'ECCUBE_MAIL_ENCRYPTION' => null,
        'ECCUBE_MAIL_AUTH_MODE' => null,
        'ECCUBE_MAIL_CHARSET_ISO_2022_JP' => 'false',
        'ECCUBE_MAIL_SPOOL' => 'false',
    ];
}

function displayEnvironmentVariables()
{
    echo 'Environment variables:'.PHP_EOL;
    foreach (array_keys(getExampleVariables()) as $name) {
        echo $name.'='.env($name).PHP_EOL;
    }
}

function composerSetup()
{
    if (!file_exists(__DIR__.'/'.COMPOSER_FILE)) {
        if (!file_exists(__DIR__.'/'.COMPOSER_SETUP_FILE)) {
            copy('https://getcomposer.org/installer', COMPOSER_SETUP_FILE);
        }

        $sha = hash_file('SHA384', COMPOSER_SETUP_FILE).PHP_EOL;
        out(COMPOSER_SETUP_FILE.': '.$sha);

        $command = 'php '.COMPOSER_SETUP_FILE;
        out("execute: $command", 'info');
        passthru($command);
        unlink(COMPOSER_SETUP_FILE);
    } else {
        $command = 'php '.COMPOSER_FILE.' self-update';
        passthru($command);
    }
}

function composerInstall()
{
    $command = 'php '.COMPOSER_FILE.' install --dev --no-interaction';
    passthru($command);
}

function getDatabaseConfig()
{
    $config = require __DIR__.'/src/Eccube/Resource/config/database.php';
    $default = $config['database']['default'];

    return $config['database'][$default];
}

function createDatabase(\Doctrine\DBAL\Connection $conn, $dbname)
{
    $sm = $conn->getSchemaManager();

    if ($conn->getDatabasePlatform()->getName() === 'sqlite') {
        out('unlink database to '.$dbname, 'info');
        if (file_exists($dbname)) {
            unlink($dbname);
        }
    } else {
        $databases = $sm->listDatabases();
        if (in_array($dbname, $databases)) {
            out('database exists '.$dbname, 'info');
            out('drop database to '.$dbname, 'info');
            $sm->dropDatabase($dbname);
        }
    }
    out('create database to '.$dbname, 'info');
    $sm->createDatabase($dbname);
}

function createConnection(array $params, $noDb = false)
{
    if ($noDb) {
        unset($params['dbname']);
    }
    return \Doctrine\DBAL\DriverManager::getConnection($params);
}

function createEntityManager(\Doctrine\DBAL\Connection $conn)
{
    $paths = [
        __DIR__.'/src/Eccube/Entity',
        __DIR__.'/app/Acme/Entity',
    ];
    // todo プロキシ, プラグインの対応
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, true, null, null, false);

    return \Doctrine\ORM\EntityManager::create($conn, $config);
}

function createMigration(\Doctrine\DBAL\Connection $conn)
{
    $config = new \Doctrine\DBAL\Migrations\Configuration\Configuration($conn);
    $config->setMigrationsNamespace('DoctrineMigrations');
    $migrationDir = __DIR__.'/src/Eccube/Resource/doctrine/migration';
    $config->setMigrationsDirectory($migrationDir);
    $config->registerMigrationsFromDirectory($migrationDir);

    $migration = new \Doctrine\DBAL\Migrations\Migration($config);
    $migration->setNoMigrationException(true);

    return $migration;
}

function initializeDatabase(\Doctrine\ORM\EntityManager $em)
{
    // Clear Doctrine to be safe
    $em->getConnection()->getConfiguration()->setSQLLogger(null);
    $em->clear();
    gc_collect_cycles();

    // Schema Tool to process our entities
    $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
    $classes = $em->getMetaDataFactory()->getAllMetaData();

    // Drop all classes and re-build them for each test case
    out('Dropping database schema...', 'info');
    $tool->dropSchema($classes);
    out('Creating database schema...', 'info');
    $tool->createSchema($classes);
    out('Database schema created successfully!', 'success');

    $loader = new \Eccube\Doctrine\Common\CsvDataFixtures\Loader();
    $loader->loadFromDirectory(__DIR__.'/src/Eccube/Resource/doctrine/import_csv');
    $executer = new \Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor($em);
    $fixtures = $loader->getFixtures();
    $executer->execute($fixtures);

    out('Migrating database schema...', 'info');
    $migration = createMigration($em->getConnection());
    $migration->migrate();
    out('Database migration successfully!', 'success');

    out('Creating admin accounts...', 'info');
    $login_id = env('ECCUBE_ADMIN_USER');
    $login_password = env('ECCUBE_ADMIN_PASS');

    $encoder = new \Eccube\Security\Core\Encoder\PasswordEncoder([
        'auth_type' => '',
        'auth_magic' => env('ECCUBE_AUTH_MAGIC'),
        'password_hash_algos' => 'sha256',
    ]);
    $salt = \Eccube\Util\Str::random(32);
    $password = $encoder->encodePassword($login_password, $salt);

    $conn = $em->getConnection();
    $member_id = ('postgresql' === $conn->getDatabasePlatform()->getName())
        ? $conn->fetchColumn("select nextval('dtb_member_id_seq')")
        : null;

    $conn->insert('dtb_member', [
        'id' => $member_id,
        'login_id' => $login_id,
        'password' => $password,
        'salt' => $salt,
        'work_id' => 1,
        'authority_id' => 0,
        'creator_id' => 1,
        'rank' => 1,
        'update_date' => new \DateTime(),
        'create_date' => new \DateTime(),
        'name' => '管理者',
        'department' => 'EC-CUBE SHOP',
        'discriminator_type' => 'member',
    ], [
        'update_date' => Doctrine\DBAL\Types\Type::DATETIME,
        'create_date' => Doctrine\DBAL\Types\Type::DATETIME,
    ]);

    $shop_name = env('ECCUBE_SHOP_NAME');
    $admin_mail = env('ECCUBE_ADMIN_MAIL');

    $id = ('postgresql' === $conn->getDatabasePlatform()->getName())
        ? $conn->fetchColumn("select nextval('dtb_base_info_id_seq')")
        : null;

    $conn->insert('dtb_base_info', [
        'id' => $id,
        'shop_name' => $shop_name,
        'email01' => $admin_mail,
        'email02' => $admin_mail,
        'email03' => $admin_mail,
        'email04' => $admin_mail,
        'update_date' => new \DateTime(),
        'discriminator_type' => 'baseinfo',
    ], [
        'update_date' => \Doctrine\DBAL\Types\Type::DATETIME,
    ]);
}

function updatePermissions($argv)
{
    $finder = \Symfony\Component\Finder\Finder::create();
    $finder
        ->in('html')->notName('.htaccess')
        ->in('app')->notName('console');

    $verbose = false;
    if (in_array('-V', $argv) || in_array('--verbose', $argv)) {
        $verbose = true;
    }
    foreach ($finder as $content) {
        $permission = $content->getPerms();
        // see also http://www.php.net/fileperms
        if (!($permission & 0x0010) || !($permission & 0x0002)) {
            $realPath = $content->getRealPath();
            if ($verbose) {
                out(sprintf('%s %s to ', $realPath, substr(sprintf('%o', $permission), -4)), 'info', false);
            }
            $permission = !($permission & 0x0020) ? $permission += 040 : $permission; // g+r
            $permission = !($permission & 0x0010) ? $permission += 020 : $permission; // g+w
            $permission = !($permission & 0x0004) ? $permission += 04 : $permission;  // o+r
            $permission = !($permission & 0x0002) ? $permission += 02 : $permission;  // o+w
            $result = chmod($realPath, $permission);
            if ($verbose) {
                if ($result) {
                    out(substr(sprintf('%o', $permission), -4), 'info');
                } else {
                    out('failure', 'error');
                }
            }
        }
    }
}

function copyConfigFiles()
{
    $src = __DIR__.'/src/Eccube/Resource/config';
    $dist = __DIR__.'/app/config/eccube';
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $fs->mirror($src, $dist, null, ['override' => true]);
}

function replaceConfig($keys, $content)
{
    $patternFormat = "/(env\('%s'.*?\))/s";
    $replacementFormat = "env('%s', %s)";

    foreach ($keys as $key) {
        // 環境変数が未定義の場合はスキップ.
        $value = getenv($key);
        if ($value === false) {
            continue;
        }
        // インストール時のみ必要な環境はスキップ.
        $installOnly = [
            'ECCUBE_ADMIN_USER',
            'ECCUBE_ADMIN_MAIL',
            'ECCUBE_SHOP_NAME',
        ];
        if (in_array($key, $installOnly)) {
            continue;
        }

        $value = env($key);

        if (is_bool($value)
            || is_null($value)
            || is_array($value)
            || is_numeric($value)
        ) {
            $value = var_export($value, true);
        } else {
            $value = "'".$value."'";
        }

        $pattern = sprintf($patternFormat, $key);
        $replacement = sprintf($replacementFormat, $key, $value);

        $content = preg_replace($pattern, $replacement, $content);
        if (is_null($content)) {
            out('config replace failed.', 'error');
            out("-> $key : $value", 'error');
            exit(1);
        }
    }

    return $content;
}

function replaceConfigFiles()
{
    $dir = __DIR__.'/app/config/eccube';
    $files = [
        $dir.'/config.php',
        $dir.'/database.php',
        $dir.'/mail.php',
        $dir.'/path.php',
    ];
    $keys = array_keys(getExampleVariables());

    putenv('ECCUBE_INSTALL=1');
    $keys[] = 'ECCUBE_INSTALL';

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $replaced = replaceConfig($keys, $content);
        if ($content !== $replaced) {
            file_put_contents($file, $replaced);
        }
    }
}
/**
 * @link https://github.com/composer/windows-setup/blob/master/src/php/installer.php
 */
function setUseAnsi($argv)
{
    // --no-ansi wins over --ansi
    if (in_array('--no-ansi', $argv)) {
        define('USE_ANSI', false);
    } elseif (in_array('--ansi', $argv)) {
        define('USE_ANSI', true);
    } else {
        // On Windows, default to no ANSI, except in ANSICON and ConEmu.
        // Everywhere else, default to ANSI if stdout is a terminal.
        define(
            'USE_ANSI',
            (DIRECTORY_SEPARATOR == '\\')
                ? (false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI'))
                : (function_exists('posix_isatty') && posix_isatty(1))
        );
    }
}

/**
 * @link https://github.com/composer/windows-setup/blob/master/src/php/installer.php
 */
function out($text, $color = null, $newLine = true)
{
    $styles = array(
        'success' => "\033[0;32m%s\033[0m",
        'error' => "\033[31;31m%s\033[0m",
        'info' => "\033[33;33m%s\033[0m",
    );
    $format = '%s';
    if (isset($styles[$color]) && USE_ANSI) {
        $format = $styles[$color];
    }
    if ($newLine) {
        $format .= PHP_EOL;
    }
    printf($format, $text);
}
