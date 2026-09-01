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

namespace Eccube;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Eccube\Common\EccubeNav;
use Eccube\Common\EccubeTwigBlock;
use Eccube\DependencyInjection\Compiler\AutoConfigurationTagPass;
use Eccube\DependencyInjection\Compiler\BuildDirCacheWarmerPass;
use Eccube\DependencyInjection\Compiler\McpAuditLoggerChannelLockPass;
use Eccube\DependencyInjection\Compiler\McpCliCommandPass;
use Eccube\DependencyInjection\Compiler\McpScopeEnforcementPass;
use Eccube\DependencyInjection\Compiler\NavCompilerPass;
use Eccube\DependencyInjection\Compiler\PaymentMethodPass;
use Eccube\DependencyInjection\Compiler\PluginPass;
use Eccube\DependencyInjection\Compiler\PurchaseFlowPass;
use Eccube\DependencyInjection\Compiler\QueryCustomizerPass;
use Eccube\DependencyInjection\Compiler\RuntimeCacheDirPass;
use Eccube\DependencyInjection\Compiler\StripAutoMappedEntityPathsPass;
use Eccube\DependencyInjection\Compiler\StripReportFieldsArgPass;
use Eccube\DependencyInjection\Compiler\TwigBlockPass;
use Eccube\DependencyInjection\Compiler\TwigExtensionPass;
use Eccube\DependencyInjection\Compiler\WebServerDocumentRootPass;
use Eccube\DependencyInjection\EccubeExtension;
use Eccube\DependencyInjection\Facade\LoggerFacade;
use Eccube\DependencyInjection\Facade\TranslatorFacade;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeType;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeTzType;
use Eccube\Doctrine\ORM\Mapping\Driver\TraitProxyAttributeDriver;
use Eccube\Doctrine\Query\QueryCustomizer;
use Eccube\Log\Logger;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerInterface;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\PurchaseFlow\DiscountProcessor;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\ItemPreprocessor;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Contracts\Translation\TranslatorInterface;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->loadEntityProxies();
    }

    /**
     * ビルド時にのみ書き込まれるディレクトリ.
     *
     * getBuildDir() と別パスであることが, twig の 3 層キャッシュ
     * (readonly_cache + runtime_cache) が有効になる条件のため, 統合してはならない.
     * see TwigExtension::load() の `$cacheDir === $buildDir` 判定.
     */
    #[\Override]
    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    /**
     * コンパイル済みコンテナ・ルーティング・メタデータ・twig(prod) の出力先.
     *
     * getCacheDir() と分離することで, Web サーバーからは読み取り専用で運用できる.
     * 生成は eccube:cache:build (CLI) が行う.
     */
    #[\Override]
    public function getBuildDir(): string
    {
        return $this->getProjectDir().'/var/build/'.$this->environment;
    }

    /**
     * リクエスト処理中に Web サーバーが書き込むディレクトリ.
     *
     * cache pool・翻訳・htmlpurifier・twig のランタイムキャッシュ等,
     * 実行時に生成されるものはすべてここへ集約する.
     * getCacheDir() / getBuildDir() を Web サーバーから書けない構成にするための受け皿.
     */
    public function getRuntimeDir(): string
    {
        return $this->getProjectDir().'/var/runtime/'.$this->environment;
    }

    /**
     * HttpCache のストア (share_dir/http_cache) はリクエスト処理中に書き込まれるため,
     * ランタイムディレクトリへ向ける.
     */
    #[\Override]
    public function getShareDir(): ?string
    {
        return $this->getRuntimeDir();
    }

    #[\Override]
    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    public function getConfigDir(): string
    {
        return $this->getProjectDir().'/app/config/eccube';
    }

    #[\Override]
    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/app/config/eccube/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }

        $pluginDir = $this->getProjectDir().'/app/Plugin';
        $finder = (new Finder())
            ->in($pluginDir)
            ->sortByName()
            ->depth(0)
            ->directories();
        $plugins = array_map(fn ($dir) => $dir->getBaseName(), iterator_to_array($finder));

        foreach ($plugins as $code) {
            $pluginBundles = $pluginDir.'/'.$code.'/Resource/config/bundles.php';
            if (file_exists($pluginBundles)) {
                $contents = require $pluginBundles;
                foreach ($contents as $class => $envs) {
                    if (isset($envs['all']) || isset($envs[$this->environment])) {
                        yield new $class();
                    }
                }
            }
        }

        $customizeBundles = $this->getProjectDir().'/app/Customize/Resource/config/bundles.php';
        if (file_exists($customizeBundles)) {
            $contents = require $customizeBundles;
            foreach ($contents as $class => $envs) {
                if (isset($envs['all']) || isset($envs[$this->environment])) {
                    yield new $class();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see BaseKernel::boot()
     */
    #[\Override]
    public function boot(): void
    {
        // Symfonyがsrc/Eccube/Entity以下を読み込む前にapp/proxy/entity以下をロードする
        // $this->loadEntityProxies();

        parent::boot();

        $container = $this->getContainer();

        // DateTime/DateTimeTzのタイムゾーンを設定.
        $timezone = $container->getParameter('timezone');
        UTCDateTimeType::setTimeZone($timezone);
        UTCDateTimeTzType::setTimeZone($timezone);

        date_default_timezone_set($timezone);

        $Logger = $container->get('eccube.logger');
        if ($Logger instanceof Logger) {
            LoggerFacade::init($container, $Logger);
        }
        $Translator = $container->get('translator');
        if ($Translator instanceof TranslatorInterface) {
            TranslatorFacade::init($Translator);
        }

        // TODO:削除
        //        /** @var AnnotationReaderFacade $AnnotationReaderFacade */
        //        $AnnotationReaderFacade = $container->get(AnnotationReaderFacade::class);
        //        $AnnotationReader = $AnnotationReaderFacade->getAnnotationReader();
        //        if ($AnnotationReader !== null && $AnnotationReader instanceof \Doctrine\Common\Annotations\Reader) {
        //            AnnotationReaderFacade::init($AnnotationReader);
        //        }
    }

    /**
     * @throws \Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $confDir = $this->getProjectDir().'/app/config/eccube';
        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->environment)) {
            $loader->load($confDir.'/packages/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->load($confDir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');

        // プラグインのservices.phpをロードする.
        $dir = dirname(__DIR__).'/../app/Plugin/*/Resource/config';
        $loader->load($dir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($dir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');

        // カスタマイズディレクトリのservices.phpをロードする.
        $dir = dirname(__DIR__).'/../app/Customize/Resource/config';
        $loader->load($dir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($dir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $container = $this->getContainer();

        $scheme = ['https', 'http'];
        $forceSSL = $container->getParameter('eccube_force_ssl');
        if ($forceSSL) {
            $scheme = ['https'];
        }

        $confDir = $this->getProjectDir().'/app/config/eccube';
        if (is_dir($confDir.'/routes/')) {
            $builder = $routes->import($confDir.'/routes/*'.self::CONFIG_EXTS);
            $builder->schemes($scheme);
        }
        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $builder = $routes->import($confDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS);
            $builder->schemes($scheme);
        }
        $builder = $routes->import($confDir.'/routes'.self::CONFIG_EXTS);
        $builder->schemes($scheme);
        $builder = $routes->import($confDir.'/routes_'.$this->environment.self::CONFIG_EXTS);
        $builder->schemes($scheme);

        // 有効なプラグインのルーティングをインポートする.
        $plugins = $container->getParameter('eccube.plugins.enabled');
        $pluginDir = $this->getProjectDir().'/app/Plugin';
        foreach ($plugins as $plugin) {
            $dir = $pluginDir.'/'.$plugin.'/Controller';
            if (file_exists($dir)) {
                $builder = $routes->import($dir, 'attribute');
                $builder->schemes($scheme);
            }
            if (file_exists($pluginDir.'/'.$plugin.'/Resource/config')) {
                $builder = $routes->import($pluginDir.'/'.$plugin.'/Resource/config/routes'.self::CONFIG_EXTS);
                $builder->schemes($scheme);
            }
        }
    }

    #[\Override]
    protected function build(ContainerBuilder $container): void
    {
        $this->addEntityExtensionPass($container);

        $container->registerExtension(new EccubeExtension());

        // サービスタグの自動設定を行う
        $container->addCompilerPass(new AutoConfigurationTagPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 11);

        // サービスタグの収集より先に実行し, 付与されているタグをクリアする.
        // FormPassは優先度0で実行されているので, それより速いタイミングで実行させる.
        // 自動登録されるタグやコンパイラパスの登録タイミングは, FrameworkExtension::load(), FrameworkBundle::build()を参考に.
        $container->addCompilerPass(new PluginPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);

        // DocumentRootをルーティディレクトリに設定する.
        $container->addCompilerPass(new WebServerDocumentRootPass('%kernel.project_dir%/'));

        // twigのurl,path関数を差し替え
        $container->addCompilerPass(new TwigExtensionPass());

        // リクエスト処理中に書き込まれるキャッシュを %eccube_runtime_dir% へ寄せる.
        $container->addCompilerPass(new RuntimeCacheDirPass());

        // 自動 warmup をビルドディレクトリへ書くものだけに絞る.
        $container->addCompilerPass(new BuildDirCacheWarmerPass());

        // クエリカスタマイズの拡張.
        $container->registerForAutoconfiguration(QueryCustomizer::class)
            ->addTag(QueryCustomizerPass::QUERY_CUSTOMIZER_TAG);
        $container->addCompilerPass(new QueryCustomizerPass());

        // 管理画面ナビの拡張
        $container->registerForAutoconfiguration(EccubeNav::class)
            ->addTag(NavCompilerPass::NAV_TAG);
        $container->addCompilerPass(new NavCompilerPass());

        // TwigBlockの拡張
        $container->registerForAutoconfiguration(EccubeTwigBlock::class)
            ->addTag(TwigBlockPass::TWIG_BLOCK_TAG);
        $container->addCompilerPass(new TwigBlockPass());

        // PaymentMethod の拡張
        $container->registerForAutoconfiguration(PaymentMethodInterface::class)
            ->addTag(PaymentMethodPass::PAYMENT_METHOD_TAG);
        $container->addCompilerPass(new PaymentMethodPass());

        // Agent Commerce 決済ハンドラ (#6574 UCP / #6776 ACP) の拡張。
        // 決済プラグインの具象ハンドラは Plugin\ glob (services.php・#6915) で登録されるため、
        // services.yaml のファイルスコープな _instanceof ではタグが付かない。
        // PaymentMethodInterface と同様にコンテナ全体へ効く registerForAutoconfiguration でタグ付けする。
        $container->registerForAutoconfiguration(AgentCheckoutPaymentHandlerInterface::class)
            ->addTag('agent_commerce.payment_handler');

        // PurchaseFlow の拡張
        $container->registerForAutoconfiguration(ItemPreprocessor::class)
            ->addTag(PurchaseFlowPass::ITEM_PREPROCESSOR_TAG);
        $container->registerForAutoconfiguration(ItemValidator::class)
            ->addTag(PurchaseFlowPass::ITEM_VALIDATOR_TAG);
        $container->registerForAutoconfiguration(ItemHolderPreprocessor::class)
            ->addTag(PurchaseFlowPass::ITEM_HOLDER_PREPROCESSOR_TAG);
        $container->registerForAutoconfiguration(ItemHolderValidator::class)
            ->addTag(PurchaseFlowPass::ITEM_HOLDER_VALIDATOR_TAG);
        $container->registerForAutoconfiguration(ItemHolderPostValidator::class)
            ->addTag(PurchaseFlowPass::ITEM_HOLDER_POST_VALIDATOR_TAG);
        $container->registerForAutoconfiguration(DiscountProcessor::class)
            ->addTag(PurchaseFlowPass::DISCOUNT_PROCESSOR_TAG);
        $container->registerForAutoconfiguration(PurchaseProcessor::class)
            ->addTag(PurchaseFlowPass::PURCHASE_PROCESSOR_TAG);
        $container->addCompilerPass(new PurchaseFlowPass());
        // StripReportFieldsArgPass は DoctrineOrmMappingsPass の後に実行する必要があるため、優先度を-1000に設定
        $container->addCompilerPass(new StripReportFieldsArgPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1000);

        // MCP: 全 Tool 呼び出しの手前で scope を強制する referenceHandler を mcp-bundle の builder に差し込む。
        // mcp-bundle の McpPass (優先度 0、 builder->setContainer を組む) の後に走らせるため負の優先度で登録する。
        $container->addCompilerPass(new McpScopeEnforcementPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -100);

        // MCP: 監査ログ (mcp チャンネル) の autowire alias を削除し、 書き手を McpAuditLogger に縛る。
        // monolog の LoggerChannelPass (優先度 0) が alias を作った後に走らせるため負の優先度で登録する。
        $container->addCompilerPass(new McpAuditLoggerChannelLockPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -100);

        // MCP: 各ツールを eccube:cli:<tool> コマンドとして登録する。 inner ReferenceHandler を定義する
        // McpScopeEnforcementPass (-100) の後に走らせるため、 それより低い優先度で登録する。
        $container->addCompilerPass(new McpCliCommandPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -200);
    }

    protected function addEntityExtensionPass(ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        // TraitProxyAttributeDriver で明示登録した Entity ディレクトリ
        $explicitlyMappedPaths = [];

        // Eccube
        $paths = ['%kernel.project_dir%/src/Eccube/Entity'];
        $namespaces = ['Eccube\\Entity'];
        $driver = new Definition(TraitProxyAttributeDriver::class, [$paths]);
        $driver->addMethodCall('setTraitProxiesDirectory', [$projectDir.'/app/proxy/entity']);
        $container->addCompilerPass(new DoctrineOrmMappingsPass($driver, $namespaces, []));
        $explicitlyMappedPaths = [...$explicitlyMappedPaths, ...$paths];

        // Customize
        $customizePaths = ['%kernel.project_dir%/app/Customize/Entity'];
        $customizeNamespaces = ['Customize\\Entity'];
        $customizeDriver = new Definition(TraitProxyAttributeDriver::class, [$customizePaths]);
        $customizeDriver->addMethodCall('setTraitProxiesDirectory', [$projectDir.'/app/proxy/entity']);
        $container->addCompilerPass(new DoctrineOrmMappingsPass($customizeDriver, $customizeNamespaces, []));
        $explicitlyMappedPaths = [...$explicitlyMappedPaths, ...$customizePaths];

        // Plugin
        $pluginDir = $projectDir.'/app/Plugin';
        $finder = (new Finder())
            ->in($pluginDir)
            ->sortByName()
            ->depth(0)
            ->directories();
        $plugins = array_map(fn ($dir) => $dir->getBaseName(), iterator_to_array($finder));

        foreach ($plugins as $code) {
            if (file_exists($pluginDir.'/'.$code.'/Entity')) {
                $paths = ['%kernel.project_dir%/app/Plugin/'.$code.'/Entity'];
                $namespaces = ['Plugin\\'.$code.'\\Entity'];
                $driver = new Definition(TraitProxyAttributeDriver::class, [$paths]);
                $driver->addMethodCall('setTraitProxiesDirectory', [$projectDir.'/app/proxy/entity']);
                $container->addCompilerPass(new DoctrineOrmMappingsPass($driver, $namespaces, []));
                $explicitlyMappedPaths = [...$explicitlyMappedPaths, ...$paths];
            }
        }

        // 明示登録した Entity ディレクトリを auto_mapping の素の AttributeDriver から取り除く.
        // StripReportFieldsArgPass が paths を第1引数へ正規化した後に実行する必要があるため、優先度を-1001に設定
        $container->addCompilerPass(
            new StripAutoMappedEntityPathsPass($explicitlyMappedPaths),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            -1001
        );
    }

    protected function loadEntityProxies(): void
    {
        // see https://github.com/EC-CUBE/ec-cube/issues/4727
        // キャッシュクリアなど、コード内でコマンドを利用している場合に2回実行されてしまう
        if (true === $this->booted) {
            return;
        }

        $files = Finder::create()
            ->in(__DIR__.'/../../app/proxy/entity/')
            ->name('*.php')
            ->files();
        foreach ($files as $file) {
            require_once $file->getRealPath();
        }
    }
}
