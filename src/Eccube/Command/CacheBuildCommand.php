<?php

declare(strict_types=1);

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

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\TwigBundle\CacheWarmer\TemplateCacheWarmer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\RebootableInterface;

/**
 * ビルドディレクトリ (kernel.build_dir) のみを再生成する.
 *
 * cache:clear は kernel.build_dir と kernel.cache_dir の双方に加え, 実行時キャッシュも
 * 作り直そうとするため, Web サーバーと CLI で所有者を分けた構成では使用できない
 * (CacheClearCommand.php の is_writable() 検査). 本コマンドは CacheClearCommand と同じ
 * 「別名のディレクトリへ warmup してから rename で差し替える」手順を build 側だけに適用し,
 * %eccube_runtime_dir% (Web サーバー所有) には触れない.
 */
#[AsCommand(name: 'eccube:cache:build', description: 'コンパイル済みコンテナとテンプレートを生成します.')]
final class CacheBuildCommand extends Command
{
    /**
     * 書き込み権限が不足しており, 手動での対応が必要.
     *
     * PluginCommandTrait::EXIT_MANUAL_ACTION_REQUIRED と同じ意味づけの終了コード.
     * (2 は Symfony の Command::INVALID が使用済みのため避ける)
     */
    public const EXIT_MANUAL_ACTION_REQUIRED = 3;

    private readonly Filesystem $filesystem;

    public function __construct(?Filesystem $filesystem = null)
    {
        parent::__construct();

        $this->filesystem = $filesystem ?? new Filesystem();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('no-twig', null, InputOption::VALUE_NONE, 'テンプレートの事前コンパイルを省略します.')
            ->setHelp(<<<'EOF'
                <info>%command.name%</info> は kernel.build_dir のみを再生成します.

                  <info>php %command.full_name% --env=prod --no-debug</info>

                実行時キャッシュ (%eccube_runtime_dir%) は削除しません.
                Web サーバーが生成したキャッシュを削除するには, Web サーバーのユーザーで
                <info>bin/console cache:pool:clear --all</info> を実行するか, 管理画面のキャッシュ管理を使用してください.
                EOF
            );
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $application = $this->getApplication();
        if (!$application instanceof Application) {
            $io->error('このコマンドは bin/console から実行してください.');

            return self::FAILURE;
        }

        $kernel = $application->getKernel();
        if (!$kernel instanceof RebootableInterface) {
            $io->error(sprintf('%s は RebootableInterface を実装していないため再生成できません.', $kernel::class));

            return self::FAILURE;
        }

        $container = $kernel->getContainer();
        $realBuildDir = (string) $container->getParameter('kernel.build_dir');
        $realCacheDir = (string) $container->getParameter('kernel.cache_dir');

        if (($unwritable = $this->findUnwritable([$realBuildDir, $realCacheDir])) !== null) {
            $this->reportUnwritable($io, $unwritable);

            return self::EXIT_MANUAL_ACTION_REQUIRED;
        }

        $io->comment(sprintf(
            'ビルドディレクトリを再生成します (環境: <info>%s</info>, デバッグ: <info>%s</info>)',
            $kernel->getEnvironment(),
            var_export($kernel->isDebug(), true)
        ));

        // 現在のコンテナのディレクトリ名は再起動前にしか取得できない.
        $containerDir = basename(dirname((string) (new \ReflectionObject($container))->getFileName()));

        // warmup 先はシリアライズされたリソース内のパス長を変えないよう, 実体と同じ長さにする.
        $warmupDir = substr($realBuildDir, 0, -1).(str_ends_with($realBuildDir, '_') ? '-' : '_');
        $oldBuildDir = substr($realBuildDir, 0, -1).(str_ends_with($realBuildDir, '~') ? '+' : '~');
        $this->filesystem->remove([$warmupDir, $oldBuildDir]);
        $this->filesystem->mkdir($warmupDir);

        // 再起動するとコンテナは作り直されるため, 既存のディスパッチャは使えなくなる.
        $application->setDispatcher(new EventDispatcher());
        $kernel->reboot($warmupDir);

        // 事前コンパイルより先に実行する. dev では twig の出力先が %eccube_runtime_dir%/twig に
        // なるため, 後から削除すると warmup の結果まで消えてしまう.
        $this->clearStaleRuntimeCache($io, (string) $container->getParameter('eccube_runtime_dir'));

        if (!$input->getOption('no-twig')) {
            $this->warmUpTemplates($kernel, $realCacheDir, $warmupDir, $io);
        }

        $this->replaceWarmupPaths($warmupDir, $realBuildDir);

        if (!$this->filesystem->exists($warmupDir.'/'.$containerDir)) {
            $this->filesystem->rename($realBuildDir.'/'.$containerDir, $warmupDir.'/'.$containerDir);
            touch($warmupDir.'/'.$containerDir.'.legacy');
        }

        $this->filesystem->rename($realBuildDir, $oldBuildDir);
        // 差し替えの最中に別プロセスが再構築した中途半端な成果物は捨てる.
        $this->filesystem->remove($realBuildDir);
        $this->filesystem->rename($warmupDir, $realBuildDir);
        $this->filesystem->remove($oldBuildDir);

        $io->success(sprintf('"%s" 環境のビルドディレクトリを生成しました.', $kernel->getEnvironment()));

        return self::SUCCESS;
    }

    /**
     * @param list<string> $dirs
     */
    private function findUnwritable(array $dirs): ?string
    {
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
                    return $dir;
                }

                continue;
            }

            if (!is_writable($dir)) {
                return $dir;
            }
        }

        return null;
    }

    private function reportUnwritable(SymfonyStyle $io, string $dir): void
    {
        $io->error(sprintf('%s へ書き込めません.', $dir));
        $io->text([
            'ビルドディレクトリの生成には kernel.build_dir と kernel.cache_dir の双方への書き込み権限が必要です.',
            '所有者を確認するには次を実行してください.',
            '',
            '    bin/console eccube:doctor:permissions',
        ]);
    }

    private function warmUpTemplates(KernelInterface $kernel, string $realCacheDir, string $warmupDir, SymfonyStyle $io): void
    {
        // BuildDirCacheWarmerPass が kernel.cache_warmer タグを外しているため, ここで明示的に実行する.
        $container = $kernel->getContainer();
        if (!$container->has('twig.template_cache_warmer')) {
            return;
        }

        $warmer = $container->get('twig.template_cache_warmer');
        if (!$warmer instanceof TemplateCacheWarmer) {
            return;
        }

        $io->comment('テンプレートを事前コンパイルしています...');
        $warmer->warmUp($realCacheDir, $warmupDir);
    }

    /**
     * warmup 先のパスを実体のパスへ書き換える.
     */
    private function replaceWarmupPaths(string $warmupDir, string $realBuildDir): void
    {
        $search = [$warmupDir, str_replace('/', '\\/', $warmupDir), str_replace('\\', '\\\\', $warmupDir)];
        $replace = str_replace('\\', '/', $realBuildDir);

        foreach (Finder::create()->files()->in($warmupDir) as $file) {
            $path = (string) $file;
            $content = str_replace($search, $replace, $this->filesystem->readFile($path), $count);
            if ($count) {
                file_put_contents($path, $content);
            }
        }
    }

    /**
     * ビルド結果と食い違いうる実行時キャッシュ (事前コンパイル漏れのテンプレート) を削除する.
     *
     * 権限を分離した構成では Web サーバー所有のため削除できない. その場合は案内に留める.
     */
    private function clearStaleRuntimeCache(SymfonyStyle $io, string $runtimeDir): void
    {
        $stale = $runtimeDir.'/twig';
        if (!is_dir($stale)) {
            return;
        }

        if (!is_writable($runtimeDir)) {
            $io->warning([
                sprintf('%s へ書き込めないため, 実行時キャッシュを削除できませんでした.', $runtimeDir),
                'Web サーバーのユーザーで次を実行するか, 管理画面のキャッシュ管理から削除してください.',
                '    bin/console cache:pool:clear --all',
            ]);

            return;
        }

        $this->filesystem->remove($stale);
        $io->comment('古い実行時キャッシュを削除しました.');
    }
}
