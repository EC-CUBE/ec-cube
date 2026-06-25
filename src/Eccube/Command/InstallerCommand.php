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

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Tools\DsnParser;
use Dotenv\Dotenv;
use Eccube\Common\EccubeConfig;
use Eccube\Util\StringUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'eccube:install', description: 'Install EC-CUBE')]
class InstallerCommand extends Command
{
    protected SymfonyStyle $io;

    protected string $databaseUrl;

    private readonly object $envFileUpdater;

    public function __construct(protected EccubeConfig $eccubeConfig)
    {
        parent::__construct();

        /* env更新処理無名クラス */
        $this->envFileUpdater = new class {
            /**
             * @var array<mixed>|false|string
             */
            public array|bool|string $appEnv;

            /**
             * @var array<mixed>|false|string
             */
            public array|bool|string $appDebug;

            public bool|float|int|string|null $databaseUrl = null;

            public false|string $serverVersion;

            public string $databaseCharset;

            public ?string $mailerDsn = null;

            public ?string $authMagic = null;

            public ?string $adminRoute = null;

            public ?string $templateCode = null;

            public ?string $locale = null;

            public ?string $trustedHosts = null;

            public ?string $envDir = null;

            /**
             * @return array<string, mixed>
             */
            private function getEnvParameters(): array
            {
                return [
                    'APP_ENV' => $this->appEnv,
                    'APP_DEBUG' => $this->appDebug,
                    'DATABASE_URL' => $this->databaseUrl,
                    'DATABASE_SERVER_VERSION' => $this->serverVersion,
                    'DATABASE_CHARSET' => $this->databaseCharset,
                    'MAILER_DSN' => $this->mailerDsn,
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
            public function updateEnvFile(): void
            {
                // $envDir = $this->eccubeConfig->get('kernel.project_dir');
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

    #[\Override]
    protected function configure(): void
    {
    }

    #[\Override]
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->title('EC-CUBE Installer Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, define the environment valiables as follows:',
            '',
            ' $ export APP_ENV=prod',
            ' $ export APP_DEBUG=0',
            ' $ export DATABASE_URL=database_url',
            ' $ export DATABASE_SERVER_VERSION=server_version',
            ' $ export MAILER_DSN=mailer_dsn',
            ' $ export ECCUBE_AUTH_MAGIC=auth_magic',
            ' ... and more',
            ' $ php bin/console eccube:install --no-interaction',
            '',
        ]);

        // TRUSTED_HOSTS
        $trustedHosts = env('TRUSTED_HOSTS', '^127\\.0\\.0\\.1$,^localhost$');
        $this->envFileUpdater->trustedHosts = $this->io->ask('Trusted hosts. ex) www.example.com, localhost ...etc', $trustedHosts);

        // DATABASE_URL
        $databaseUrl = $this->eccubeConfig->get('eccube_database_url');
        if (empty($databaseUrl)) {
            $databaseUrl = 'sqlite:///var/eccube.db';
        }
        $this->envFileUpdater->databaseUrl = $this->io->ask('Database Url', $databaseUrl);
        $databaseUrl = $this->envFileUpdater->databaseUrl;

        // DATABASE_SERVER_VERSION
        $this->envFileUpdater->serverVersion = $this->getDatabaseServerVersion($databaseUrl);

        // DATABASE_CHARSET
        $this->envFileUpdater->databaseCharset = \str_starts_with((string) $databaseUrl, 'mysql') ? 'utf8mb4' : 'utf8';

        // MAILER_DSN
        $mailerDsn = $this->eccubeConfig->get('eccube_mailer_dsn');
        if (empty($mailerDsn)) {
            $mailerDsn = 'null://null';
        }
        $this->envFileUpdater->mailerDsn = $this->io->ask('Mailer Dsn', $mailerDsn);

        // ECCUBE_AUTH_MAGIC
        $authMagic = $this->eccubeConfig->get('eccube_auth_magic');
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
        $adminRoute = $this->eccubeConfig->get('eccube_admin_route');
        if (empty($adminRoute)) {
            $adminRoute = 'admin';
        }
        $this->envFileUpdater->adminRoute = $adminRoute;

        // ECCUBE_TEMPLATE_CODE
        $templateCode = $this->eccubeConfig->get('eccube_theme_code');
        if (empty($templateCode)) {
            $templateCode = 'default';
        }
        $this->envFileUpdater->templateCode = $templateCode;

        // ECCUBE_LOCALE
        $locale = $this->eccubeConfig->get('locale');
        if (empty($locale)) {
            $locale = 'ja';
        }
        $this->envFileUpdater->locale = $locale;

        $this->io->caution('Execute the installation process. All data is initialized.');
        $question = new ConfirmationQuestion('Is it OK?');
        if (!$this->io->askQuestion($question)) {
            // `no`の場合はキャンセルメッセージを出力して終了する
            $this->setCode(function (): void {
                $this->io->success('EC-CUBE installation stopped.');
            });

            return;
        }

        // envファイルへの更新反映処理
        $this->envFileUpdater->envDir = $this->eccubeConfig->get('kernel.project_dir');
        $this->envFileUpdater->updateEnvFile();
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Process実行時に, APP_ENV/APP_DEBUGが子プロセスに引き継がれてしまうため,
        // 生成された.envをロードして上書きする.
        if ($input->isInteractive()) {
            $envDir = $this->eccubeConfig->get('kernel.project_dir');
            if (file_exists($envDir.'/.env')) {
                Dotenv::createUnsafeMutable($envDir)->load();
            }
        }

        // 対話モード実行時, eccubeConfig->get('eccube_database_url')では
        // 更新後の値が取得できないため, getenv()を使用する.
        $databaseUrl = getenv('DATABASE_URL');
        $databaseName = $this->getDatabaseName($databaseUrl);

        // データベース作成, スキーマ作成, 初期データの投入を行う.
        $commands = [];
        if ($databaseName !== 'sqlite') {
            $commands[] = ['doctrine:database:create', '--if-not-exists'];
        }
        $commands = array_merge($commands, [
            ['doctrine:schema:drop', '--force'],
            ['doctrine:schema:create'],
            ['eccube:fixtures:load'],
            ['cache:clear', '--no-warmup'],
        ]);

        // コンテナを再ロードするため別プロセスで実行する.
        foreach ($commands as $command) {
            try {
                $this->io->text(sprintf('<info>Run %s</info>...', implode(' ', $command)));
                $process = new Process(array_merge(['bin/console'], $command));
                $process->mustRun();
                $this->io->text($process->getOutput());
            } catch (ProcessFailedException $e) {
                $this->io->error($e->getMessage());

                return 1;
            }
        }

        $this->io->success('EC-CUBE installation successful.');

        return 0;
    }

    protected function getDatabaseName(string $databaseUrl): string
    {
        if (str_starts_with($databaseUrl, 'sqlite')) {
            return 'sqlite';
        }
        if (str_starts_with($databaseUrl, 'postgres') || str_starts_with($databaseUrl, 'pgsql')) {
            return 'postgres';
        }
        if (str_starts_with($databaseUrl, 'mysql')) {
            return 'mysql';
        }

        throw new \LogicException(sprintf('Database Url %s is invalid.', $databaseUrl));
    }

    /**
     * @return false|string
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getDatabaseServerVersion(string $databaseUrl): false|string
    {
        try {
            // DBAL 4 では DriverManager が 'url' を解析しなくなったため, DsnParser で展開する.
            $params = (new DsnParser(ConnectionFactory::DEFAULT_SCHEME_MAP))->parse($databaseUrl);
            $conn = DriverManager::getConnection($params);
        } catch (\Exception) {
            throw new \LogicException(sprintf('Database Url %s is invalid.', $databaseUrl));
        }
        $platform = $conn->getDatabasePlatform();
        $sql = match (true) {
            $platform instanceof SQLitePlatform => 'SELECT sqlite_version() AS server_version',
            $platform instanceof AbstractMySQLPlatform => 'SELECT version() AS server_version',
            default => 'SHOW server_version',
        };
        $stmt = $conn->executeQuery($sql);
        $version = $stmt->fetchOne();

        if ($platform instanceof PostgreSQLPlatform) {
            preg_match('/\A([\d+\.]+)/', (string) $version, $matches);
            $version = $matches[1];
        }

        return $version;
    }
}
