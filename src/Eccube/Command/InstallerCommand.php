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

namespace Eccube\Command;

use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;
use Eccube\Util\StringUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class InstallerCommand extends Command
{
    protected static $defaultName = 'eccube:install';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var string
     */
    protected $databaseUrl;

    private $envFileUpdater;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;

        /* env更新処理無名クラス */
        $this->envFileUpdater = new class() {
            public $appEnv;
            public $appDebug;
            public $databaseUrl;
            public $serverVersion;
            public $mailerUrl;
            public $authMagic;
            public $adminRoute;
            public $templateCode;
            public $locale;
            public $trustedHosts;

            public $envDir;

            private function getEnvParameters()
            {
                return [
                            'APP_ENV' => $this->appEnv,
                            'APP_DEBUG' => $this->appDebug,
                            'DATABASE_URL' => $this->databaseUrl,
                            'DATABASE_SERVER_VERSION' => $this->serverVersion,
                            'MAILER_URL' => $this->mailerUrl,
                            'ECCUBE_AUTH_MAGIC' => $this->authMagic,
                            'ECCUBE_ADMIN_ROUTE' => $this->adminRoute,
                            'ECCUBE_TEMPLATE_CODE' => $this->templateCode,
                            'ECCUBE_LOCALE' => $this->locale,
                            'TRUSTED_HOSTS' => $this->trustedHosts,
                        ];
            }

            /**
             * envファイル更新処理
             */
            public function updateEnvFile()
            {
                // $envDir = $this->container->getParameter('kernel.project_dir');
                $envFile = $this->envDir.'/.env';
                $envDistFile = $this->envDir.'/.env.dist';

                $env = file_exists($envFile)
                            ? file_get_contents($envFile)
                            : file_get_contents($envDistFile);

                $env = StringUtil::replaceOrAddEnv($env, $this->getEnvParameters());

                file_put_contents($envFile, $env);
            }
        };
    }

    protected function configure()
    {
        $this
            ->setDescription('Install EC-CUBE');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('EC-CUBE Installer Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, define the environment valiables as follows:',
            '',
            ' $ export APP_ENV=prod',
            ' $ export APP_DEBUG=0',
            ' $ export DATABASE_URL=database_url',
            ' $ export DATABASE_SERVER_VERSION=server_version',
            ' $ export MAILER_URL=mailer_url',
            ' $ export ECCUBE_AUTH_MAGIC=auth_magic',
            ' ... and more',
            ' $ php bin/console eccube:install --no-interaction',
            '',
        ]);

        // TRUSTED_HOSTS
        $trustedHosts = env('TRUSTED_HOSTS', '^127\\.0\\.0\\.1$,^localhost$');
        $this->envFileUpdater->trustedHosts = $this->io->ask('Trusted hosts. ex) www.example.com, localhost ...etc', $trustedHosts);

        // DATABASE_URL
        $databaseUrl = $this->container->getParameter('eccube_database_url');
        if (empty($databaseUrl)) {
            $databaseUrl = 'sqlite:///var/eccube.db';
        }
        $this->envFileUpdater->databaseUrl = $this->io->ask('Database Url', $databaseUrl);
        $databaseUrl = $this->envFileUpdater->databaseUrl;

        // DATABASE_SERVER_VERSION
        $this->envFileUpdater->serverVersion = $this->getDatabaseServerVersion($databaseUrl);

        // MAILER_URL
        $mailerUrl = $this->container->getParameter('eccube_mailer_url');
        if (empty($mailerUrl)) {
            $mailerUrl = 'null://localhost';
        }
        $this->envFileUpdater->mailerUrl = $this->io->ask('Mailer Url', $mailerUrl);

        // ECCUBE_AUTH_MAGIC
        $authMagic = $this->container->getParameter('eccube_auth_magic');
        if (empty($authMagic) || $authMagic === '<change.me>') {
            $authMagic = StringUtil::random();
        }
        $this->envFileUpdater->authMagic = $this->io->ask('Auth Magic', $authMagic);

        // 以下環境変数に規定済の設定値があれば利用する
        // APP_ENV
        $appEnv = env('APP_ENV', 'prod');
        // .envが存在しない状態では規定値'install'となっているため、prodに更新する
        if ($appEnv === 'install') {
            $appEnv = 'prod';
        }
        $this->envFileUpdater->appEnv = $appEnv;

        // APP_DEBUG
        $this->envFileUpdater->appDebug = env('APP_DEBUG', '0');

        // ECCUBE_ADMIN_ROUTE
        $adminRoute = $this->container->getParameter('eccube_admin_route');
        if (empty($adminRoute)) {
            $adminRoute = 'admin';
        }
        $this->envFileUpdater->adminRoute = $adminRoute;

        // ECCUBE_TEMPLATE_CODE
        $templateCode = $this->container->getParameter('eccube_theme_code');
        if (empty($templateCode)) {
            $templateCode = 'default';
        }
        $this->envFileUpdater->templateCode = $templateCode;

        // ECCUBE_LOCALE
        $locale = $this->container->getParameter('locale');
        if (empty($locale)) {
            $locale = 'ja';
        }
        $this->envFileUpdater->locale = $locale;

        $this->io->caution('Execute the installation process. All data is initialized.');
        $question = new ConfirmationQuestion('Is it OK?');
        if (!$this->io->askQuestion($question)) {
            // `no`の場合はキャンセルメッセージを出力して終了する
            $this->setCode(function () {
                $this->io->success('EC-CUBE installation stopped.');
            });

            return;
        }

        // envファイルへの更新反映処理
        $this->envFileUpdater->envDir = $this->container->getParameter('kernel.project_dir');
        $this->envFileUpdater->updateEnvFile();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Process実行時に, APP_ENV/APP_DEBUGが子プロセスに引き継がれてしまうため,
        // 生成された.envをロードして上書きする.
        if ($input->isInteractive()) {
            $envDir = $this->container->getParameter('kernel.project_dir');
            if (file_exists($envDir.'/.env')) {
                (new Dotenv($envDir))->overload();
            }
        }

        // 対話モード実行時, container->getParameter('eccube_database_url')では
        // 更新後の値が取得できないため, getenv()を使用する.
        $databaseUrl = getenv('DATABASE_URL');
        $databaseName = $this->getDatabaseName($databaseUrl);
        $ifNotExists = $databaseName === 'sqlite' ? '' : ' --if-not-exists';

        // データベース作成, スキーマ作成, 初期データの投入を行う.
        $commands = [
            'doctrine:database:create'.$ifNotExists,
            'doctrine:schema:drop --force',
            'doctrine:schema:create',
            'eccube:fixtures:load',
            'cache:clear --no-warmup',
        ];

        // コンテナを再ロードするため別プロセスで実行する.
        foreach ($commands as $command) {
            try {
                $this->io->text(sprintf('<info>Run %s</info>...', $command));
                $process = new Process('bin/console '.$command);
                $process->mustRun();
                $this->io->text($process->getOutput());
            } catch (ProcessFailedException $e) {
                $this->io->error($e->getMessage());

                return;
            }
        }

        $this->io->success('EC-CUBE installation successful.');

        return 0;
    }

    protected function getDatabaseName($databaseUrl)
    {
        if (0 === strpos($databaseUrl, 'sqlite')) {
            return 'sqlite';
        }
        if (0 === strpos($databaseUrl, 'postgres') || 0 === strpos($databaseUrl, 'pgsql')) {
            return 'postgres';
        }
        if (0 === strpos($databaseUrl, 'mysql')) {
            return 'mysql';
        }

        throw new \LogicException(sprintf('Database Url %s is invalid.', $databaseUrl));
    }

    protected function getDatabaseServerVersion($databaseUrl)
    {
        try {
            $conn = DriverManager::getConnection([
                'url' => $databaseUrl,
            ]);
        } catch (\Exception $e) {
            throw new \LogicException(sprintf('Database Url %s is invalid.', $databaseUrl));
        }
        $platform = $conn->getDatabasePlatform()->getName();
        switch ($platform) {
            case 'sqlite':
                $sql = 'SELECT sqlite_version() AS server_version';
                break;
            case 'mysql':
                $sql = 'SELECT version() AS server_version';
                break;
            case 'postgresql':
            default:
                $sql = 'SHOW server_version';
        }
        $stmt = $conn->executeQuery($sql);
        $version = $stmt->fetchColumn();

        if ($platform === 'postgresql') {
            preg_match('/\A([\d+\.]+)/', $version, $matches);
            $version = $matches[1];
        }

        return $version;
    }
}
