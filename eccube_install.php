<?php

if (php_sapi_name() !== 'cli') {
    exit(1);
}

require __DIR__.'/autoload.php';
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

$database_driver = 'pdo_sqlite';
switch($argv[1]) {
    case 'mysql':
        $database_driver = 'pdo_mysql';
        break;
    case 'pgsql':
        $database_driver = 'pdo_pgsql';
        break;
    default:
    case 'sqlite':
    case 'sqlite3':
    case 'sqlite3-in-memory':
        $database_driver = 'pdo_sqlite';
}
out($database_driver);

initializeDefaultVariables($database_driver);

if (in_array('-V', $argv) || in_array('--verbose', $argv)) {
    displayEnvironmentVariables();
}

$database = getDatabaseConfig($database_driver);
$connectionParams = $database['database'];

if ($argv[2] != 'none') {
    composerSetup();
    composerInstall();
}

createConfigFiles($database_driver);

if (!in_array('--skip-createdb', $argv)) {
    createDatabase($connectionParams);
}

$app = createApplication();
initializeDatabase($app);
out('EC-CUBE3 install finished successfully!', 'success');
$root_urlpath = getenv('ROOT_URLPATH');
if (PHP_VERSION_ID >= 50400 && empty($root_urlpath)) {
    out('PHP built-in web server to run applications, `php -S localhost:8080 -t html`', 'info');
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
--help               this help
--ansi               force ANSI color output
--no-ansi            disable ANSI color output

Environment variables:

EOF;
    foreach (getExampleVariables() as $name => $value) {
        echo $name.'='.$value.PHP_EOL;
    }
}

function initializeDefaultVariables($database_driver)
{
    switch ($database_driver) {
        case 'pdo_pgsql':
            putenv('ROOTUSER='.(getenv('ROOTUSER') ? getenv('ROOTUSER') : (getenv('DBUSER') ? getenv('DBUSER') : 'postgres')));
            putenv('ROOTPASS='.(getenv('ROOTPASS') ? getenv('ROOTPASS') : (getenv('DBPASS') ? getenv('DBPASS') : 'password')));
            putenv('DBSERVER='.(getenv('DBSERVER') ? getenv('DBSERVER') : 'localhost'));
            putenv('DBNAME='.(getenv('DBNAME') ? getenv('DBNAME') : 'cube3_dev'));
            putenv('DBUSER='.(getenv('DBUSER') ? getenv('DBUSER') : 'cube3_dev_user'));
            putenv('DBPORT='.(getenv('DBPORT') ? getenv('DBPORT') : '5432'));
            putenv('DBPASS='.(getenv('DBPASS') ? getenv('DBPASS') : 'password'));
            break;
        case 'pdo_mysql':
            putenv('ROOTUSER='.(getenv('ROOTUSER') ? getenv('ROOTUSER') : (getenv('DBUSER') ? getenv('DBUSER') : 'root')));
            putenv('DBSERVER='.(getenv('DBSERVER') ? getenv('DBSERVER') : 'localhost'));
            putenv('DBNAME='.(getenv('DBNAME') ? getenv('DBNAME') : 'cube3_dev'));
            putenv('DBUSER='.(getenv('DBUSER') ? getenv('DBUSER') : 'cube3_dev_user'));
            putenv('DBPORT='.(getenv('DBPORT') ? getenv('DBPORT') : '3306'));
            putenv('DBPASS='.(getenv('DBPASS') ? getenv('DBPASS') : 'password'));
            if (getenv('TRAVIS')) {
                putenv('DBPASS=');
                putenv('ROOTPASS=');
            } else {
                putenv('DBPASS='.(getenv('DBPASS') ? getenv('DBPASS') : 'password'));
                putenv('ROOTPASS='.(getenv('ROOTPASS') ? getenv('ROOTPASS') : (getenv('DBPASS') ? getenv('DBPASS') : 'password')));
            }
            break;
        default:
        case 'pdo_sqlite':
            break;
    }
    putenv('SHOP_NAME='.(getenv('SHOP_NAME') ? getenv('SHOP_NAME') : 'EC-CUBE SHOP'));
    putenv('ADMIN_MAIL='.(getenv('ADMIN_MAIL') ? getenv('ADMIN_MAIL') : 'admin@example.com'));
    putenv('ADMIN_USER='.(getenv('ADMIN_USER') ? getenv('ADMIN_USER') : 'admin'));
    putenv('ADMIN_PASS='.(getenv('ADMIN_PASS') ? getenv('ADMIN_PASS') : 'password'));
    putenv('MAIL_BACKEND='.(getenv('MAIL_BACKEND') ? getenv('MAIL_BACKEND') : 'smtp'));
    putenv('MAIL_HOST='.(getenv('MAIL_HOST') ? getenv('MAIL_HOST') : 'localhost'));
    putenv('MAIL_PORT='.(getenv('MAIL_PORT') ? getenv('MAIL_PORT') : 25));
    putenv('MAIL_USER='.(getenv('MAIL_USER') ? getenv('MAIL_USER') : null));
    putenv('MAIL_PASS='.(getenv('MAIL_PASS') ? getenv('MAIL_PASS') : null));
    putenv('ADMIN_ROUTE='.(getenv('ADMIN_ROUTE') ? getenv('ADMIN_ROUTE') : 'admin'));
    putenv('ROOT_URLPATH='.(getenv('ROOT_URLPATH') ? getenv('ROOT_URLPATH') : null));
}

function getExampleVariables()
{
    return array(
        'ADMIN_USER' => 'admin',
        'ADMIN_MAIL' => 'admin@example.com',
        'SHOP_NAME' => 'EC-CUBE SHOP',
        'ADMIN_ROUTE' => 'admin',
        'ROOT_URLPATH' => '<ec-cube install path>',
        'DBSERVER' => '127.0.0.1',
        'DBNAME' => 'cube3_dev',
        'DBUSER' => 'cube3_dev_user',
        'DBPASS' => 'password',
        'DBPORT' => '<database port>',
        'ROOTUSER' => 'root|postgres',
        'ROOTPASS' => 'password',
        'MAIL_BACKEND' => 'smtp',
        'MAIL_HOST' => 'localhost',
        'MAIL_PORT' => '25',
        'MAIL_USER' => '<SMTP AUTH user>',
        'MAIL_PASS' => '<SMTP AUTH password>'
    );
}


function displayEnvironmentVariables()
{
    echo 'Environment variables:'.PHP_EOL;
    foreach (array_keys(getExampleVariables()) as $name) {
        echo $name.'='.getenv($name).PHP_EOL;
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

function createDatabase(array $connectionParams)
{
    $dbname = $connectionParams['dbname'];
    switch ($connectionParams['driver']) {
        case 'pdo_pgsql':
            $connectionParams['dbname'] = 'postgres';
            $connectionParams['user'] = getenv('ROOTUSER');
            $connectionParams['password'] = getenv('ROOTPASS');
            break;
        case 'pdo_mysql':
            $connectionParams['dbname'] = 'mysql';
            $connectionParams['user'] = getenv('ROOTUSER');
            $connectionParams['password'] = getenv('ROOTPASS');
            break;
        default:
        case 'pdo_sqlite':
            $connectionParams['dbname'] = '';
            if (file_exists($dbname)) {
                out('remove database to '.$dbname, 'info');
                unlink($dbname);
            }
            break;
    }

    $config = new \Doctrine\DBAL\Configuration();
    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    $sm = $conn->getSchemaManager();
    out('Created database connection...', 'info');

    if ($connectionParams['driver'] != 'pdo_sqlite') {
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

/**
 * @return \Eccube\Application
 */
function createApplication()
{
    $app = \Eccube\Application::getInstance();
    $app['debug'] = true;
    $app->initDoctrine();
    $app->initSecurity();
    $app->register(new \Silex\Provider\FormServiceProvider());
    $app->register(new \Eccube\ServiceProvider\EccubeServiceProvider());
    $app->boot();
    return $app;
}

function initializeDatabase(\Eccube\Application $app)
{
    // Get an instance of your entity manager
    $entityManager = $app['orm.em'];

    $pdo = $entityManager->getConnection()->getWrappedConnection();

    // Clear Doctrine to be safe
    $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    $entityManager->clear();
    gc_collect_cycles();

    // Schema Tool to process our entities
    $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
    $classes = $entityManager->getMetaDataFactory()->getAllMetaData();

    // Drop all classes and re-build them for each test case
    out('Dropping database schema...', 'info');
    $tool->dropSchema($classes);
    out('Creating database schema...', 'info');
    $tool->createSchema($classes);
    out('Database schema created successfully!', 'success');
    $config = new \Doctrine\DBAL\Migrations\Configuration\Configuration($app['db']);
    $config->setMigrationsNamespace('DoctrineMigrations');

    $migrationDir = __DIR__.'/src/Eccube/Resource/doctrine/migration';
    $config->setMigrationsDirectory($migrationDir);
    $config->registerMigrationsFromDirectory($migrationDir);

    $migration = new \Doctrine\DBAL\Migrations\Migration($config);
    $migration->migrate();
    out('Database migration successfully!', 'success');

    $login_id = getenv('ADMIN_USER');
    $login_password = getenv('ADMIN_PASS');
    $passwordEncoder = new \Eccube\Security\Core\Encoder\PasswordEncoder($app['config']);
    $salt = \Eccube\Util\Str::random(32);
    $encodedPassword = $passwordEncoder->encodePassword($login_password, $salt);

    out('Creating admin accounts...', 'info');
    $sql = "INSERT INTO dtb_member (member_id, login_id, password, salt, work, del_flg, authority, creator_id, rank, update_date, create_date,name,department) VALUES (2, :login_id, :admin_pass , :salt , '1', '0', '0', '1', '1', current_timestamp, current_timestamp,'管理者', 'EC-CUBE SHOP');";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array(
            ':login_id' => $login_id,
            ':admin_pass' => $encodedPassword,
            ':salt' => $salt
        )
    );
    $stmt->closeCursor();

    $shop_name = getenv('SHOP_NAME');
    $admin_mail = getenv('ADMIN_MAIL');
    $sql = "INSERT INTO dtb_base_info (id, shop_name, email01, email02, email03, email04, update_date, option_product_tax_rule) VALUES (1, :shop_name, :admin_mail1, :admin_mail2, :admin_mail3, :admin_mail4, current_timestamp, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array(
            ':shop_name' => $shop_name,
            ':admin_mail1' => $admin_mail,
            ':admin_mail2' => $admin_mail,
            ':admin_mail3' => $admin_mail,
            ':admin_mail4' => $admin_mail,
        )
    );
    $stmt->closeCursor();
}

function createConfigFiles($database_driver)
{
    $config_path = __DIR__.'/app/config/eccube';
    createYaml(getConfig(), $config_path.'/config.yml');
    createYaml(getDatabaseConfig($database_driver), $config_path.'/database.yml');
    createYaml(getMailConfig(), $config_path.'/mail.yml');
    createYaml(getPathConfig(), $config_path.'/path.yml');
}

function createYaml($config, $path)
{
    $content = \Symfony\Component\Yaml\Yaml::dump($config);
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $fs->dumpFile($path, $content);
}

function getConfig()
{
    $config = array (
        'auth_magic' => \Eccube\Util\Str::random(32),
        'password_hash_algos' => 'sha256',
        'shop_name' => getenv('SHOP_NAME'),
        'force_ssl' => NULL,
        'admin_allow_host' =>
        array (
        ),
        'cookie_lifetime' => 0,
        'locale' => 'ja',
        'timezone' => 'Asia/Tokyo',
        'eccube_install' => 1,
    );
    return $config;
}

function getDatabaseConfig($database_driver)
{
    $database = array (
        'database' =>
        array (
            'driver' => $database_driver,
        )
    );

    switch ($database_driver) {
        case 'pdo_sqlite':
            $database['database']['dbname'] = $database['database']['path'] = __DIR__.'/app/config/eccube/eccube.db';

            break;
        case 'pdo_pgsql':
        case 'pdo_mysql':
            $database['database']['host'] = getenv('DBSERVER');
            $database['database']['dbname'] = getenv('DBNAME');
            $database['database']['user'] = getenv('DBUSER');
            $database['database']['port'] = getenv('DBPORT');
            $database['database']['password'] = getenv('DBPASS');
            $database['database']['port'] = getenv('DBPORT');
            break;
    }
    $database['database']['charset'] = 'utf8';
    $database['database']['defaultTableOptions'] = array('collate' => 'utf8_general_ci');
    return $database;
}

function getMailConfig()
{
    $mail = array (
        'mail' =>
        array (
            'transport' => getenv('MAIL_BACKEND'),
            'host' => getenv('MAIL_HOST'),
            'port' => getenv('MAIL_PORT'),
            'username' => getenv('MAIL_USER'),
            'password' => getenv('MAIL_PASS'),
            'encryption' => NULL,
            'auth_mode' => NULL,
            'charset_iso_2022_jp' => false,
        ),
    );
    return $mail;
}

function getPathConfig()
{
    $root_dir = realpath(__DIR__);
    $root_urlpath = getenv('ROOT_URLPATH');
    // TODO path.yml.dist から取得したい
    $path = array (
        'root' => $root_urlpath.'/',
        'admin_dir' => getenv('ADMIN_ROUTE'),
        'tpl' => $root_urlpath.'/user_data/packages/default/',
        'admin_tpl' => $root_urlpath.'/user_data/packages/admin/',
        'image_path' => $root_urlpath.'/upload/save_image/',
        'root_dir' => $root_dir,
        'root_urlpath' => $root_urlpath,
        'template_code' => 'default',
        'admin_route' => 'admin',
        'user_data_route' => 'user_data',
        'public_path' => '/html',
        'public_path_realdir' => $root_dir.'/html',
        'image_save_realdir' => $root_dir.'/html/upload/save_image',
        'image_temp_realdir' => $root_dir.'/html/upload/temp_image',
        'user_data_realdir' => $root_dir.'/html/user_data',
        'block_default_realdir' => $root_dir.'/src/Eccube/Resource/template/default/Block',
        'block_realdir' => $root_dir.'/app/template/default/Block',
        'template_default_realdir' => $root_dir.'/src/Eccube/Resource/template/default',
        'template_default_html_realdir' => $root_dir.'/html/template/default',
        'template_admin_realdir' => $root_dir.'/src/Eccube/Resource/template/admin',
        'template_admin_html_realdir' => $root_dir.'/html/template/admin',
        'template_realdir' => $root_dir.'/app/template/default',
        'template_html_realdir' => $root_dir.'/html/template/default',
        'template_temp_realdir' => $root_dir.'/app/cache/eccube/template',
        'csv_temp_realdir' => $root_dir.'/app/cache/eccube/csv',
        'plugin_realdir' => $root_dir.'/app/Plugin',
        'plugin_temp_realdir' => $root_dir.'/app/cache/plugin',
        'plugin_html_realdir' => $root_dir.'/html/plugin',
        'admin_urlpath' => $root_urlpath.'/template/admin',
        'front_urlpath' => $root_urlpath.'/template/default',
        'image_save_urlpath' => $root_urlpath.'/upload/save_image',
        'image_temp_urlpath' => $root_urlpath.'/upload/temp_image',
        'user_data_urlpath' => $root_urlpath.'/user_data',
        'plugin_urlpath' => $root_urlpath.'/plugin',
    );
    return $path;
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
        'info' => "\033[33;33m%s\033[0m"
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
