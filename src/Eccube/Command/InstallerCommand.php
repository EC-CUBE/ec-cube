<?php

namespace Eccube\Command;


use Dotenv\Dotenv;
use Eccube\Util\CacheUtil;
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
     * @var CacheUtil
     */
    protected $cacheUtil;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var string
     */
    protected $databaseUrl;

    /**
     * @var bool
     */
    protected $cancelInstalation = false;

    public function __construct(ContainerInterface $container, CacheUtil $cacheUtil)
    {
        parent::__construct();

        $this->container = $container;
        $this->cacheUtil = $cacheUtil;
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
            'If you prefer to not use this interactive wizard, provide the',
            'environments required by this command as follows:',
            '',
            '', // TODO 非対話形式でも動作するようにする.
            ' $ export DATABASE_URL=database_url',
            ' $ export ECCUBE_AUTH_MAGIC=auth_magic',
            ' $ php bin/console eccube:install -n',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // DATABASE_URL
        $databaseUrl = $this->container->getParameter('eccube_database_url');
        if (empty($databaseUrl)) {
            $databaseUrl = 'sqlite:///%kernel.project_dir%/var/eccube.db';
        }
        $databaseUrl = $this->io->ask('Database Url', $databaseUrl);

        // execute()でDB種別の判定に使用するので, プロパティに保持しておく.
        $this->databaseUrl = $databaseUrl;

        // MAILER_URL
        $mailerUrl = $this->container->getParameter('eccube_mailer_url');
        if (empty($mailerUrl)) {
            $mailerUrl = 'null://localhost';
        }
        $mailerUrl = $this->io->ask('Mailer Url', $mailerUrl);

        // ECCUBE_AUTH_MAGIC
        $authMagic = $this->container->getParameter('eccube_auth_magic');
        if (empty($authMagic) || $authMagic === '<change.me>') {
            $authMagic = StringUtil::random();
        }
        $authMagic = $this->io->ask('Auth Magic', $authMagic);

        $this->io->caution('Execute the installation process. All data is initialized.');
        $question = new ConfirmationQuestion('Is it OK?');
        if (!$this->io->askQuestion($question)) {
            // `no`の場合はキャンセルメッセージを出力して終了する
            $this->setCode(function () {
                $this->io->success('EC-CUBE installation process stopped.');
            });

            return;
        }

        $envParameters = [
            'APP_ENV' => 'dev',
            'APP_DEBUG' => '1',
            'DATABASE_URL' => $databaseUrl,
            'DATABASE_SERVER_VERSION' => $this->getDatabaseServerVersion($this->getDatabaseName($databaseUrl)),
            'MAILER_URL' => $mailerUrl,
            'ECCUBE_AUTH_MAGIC' => $authMagic,
        ];

        $envDir = $this->container->getParameter('kernel.project_dir');
        $envFile = $envDir.'/.env';
        $envDistFile = $envDir.'/.env.dist';

        $env = file_exists($envFile)
            ? file_get_contents($envFile)
            : file_get_contents($envDistFile);

        $env = StringUtil::replaceOrAddEnv($env, $envParameters);

        file_put_contents($envFile, $env);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cacheUtil->clearCache('dev');

        $databaseName = $this->getDatabaseName($this->databaseUrl);
        $ifNotExists = $databaseName === 'sqlite' ? '' : ' --if-not-exists';

        // データベース作成, スキーマ作成, EC-CUBEデータのロードを実行する
        $commands = [
            'doctrine:database:create'.$ifNotExists,
            'doctrine:schema:drop --force',
            'doctrine:schema:create',
            'eccube:fixtures:load',
        ];

        // 実行プロセスの環境変数(APP_ENV,APP_DEBUG)が子プロセスに引き継がれるので, .envの環境変数をロードしなおして上書きする.
        $envDir = $this->container->getParameter('kernel.project_dir');
        (new Dotenv($envDir))->overload();

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

        $this->io->success('EC-CUBE Install Successfull.');
    }

    protected function getDatabaseName($databaseUrl)
    {
        if (0 === strpos($databaseUrl, 'sqlite')) {
            return 'sqlite';
        }
        if (0 === strpos($databaseUrl, 'postgres')) {
            return 'postgres';
        }
        if (0 === strpos($databaseUrl, 'mysql')) {
            return 'mysql';
        }

        throw new \LogicException(sprintf('Database Url %s is invalid.', $databaseUrl));
    }

    protected function getDatabaseServerVersion($databaseName)
    {
        $versions = [
            'sqlite' => 3,
            'postgres' => 9,
            'mysql' => 5
        ];

        if (!isset($versions[$databaseName])) {
            throw new \LogicException(sprintf('Database Name %s is invalid.', $databaseName));
        }

        return $versions[$databaseName];
    }
}
