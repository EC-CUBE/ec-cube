<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Eccube\Common\EccubeNav;
use Eccube\Common\EccubeTwigBlock;
use Eccube\DependencyInjection\Compiler\AutoConfigurationTagPass;
use Eccube\DependencyInjection\Compiler\NavCompilerPass;
use Eccube\DependencyInjection\Compiler\PaymentMethodPass;
use Eccube\DependencyInjection\Compiler\PluginPass;
use Eccube\DependencyInjection\Compiler\PurchaseFlowPass;
use Eccube\DependencyInjection\Compiler\QueryCustomizerPass;
use Eccube\DependencyInjection\Compiler\TemplateListenerPass;
use Eccube\DependencyInjection\Compiler\TwigBlockPass;
use Eccube\DependencyInjection\Compiler\TwigExtensionPass;
use Eccube\DependencyInjection\Compiler\WebServerDocumentRootPass;
use Eccube\DependencyInjection\EccubeExtension;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeType;
use Eccube\Doctrine\DBAL\Types\UTCDateTimeTzType;
use Eccube\Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Eccube\Doctrine\Query\QueryCustomizer;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\PurchaseFlow\DiscountProcessor;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\ItemPreprocessor;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;
use Eccube\Validator\EmailValidator\NoRFCEmailValidator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir().'/app/config/eccube/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\HttpKernel\Kernel::boot()
     */
    public function boot()
    {
        // Symfonyがsrc/Eccube/Entity以下を読み込む前にapp/proxy/entity以下をロードする
        $this->loadEntityProxies();

        parent::boot();

        $container = $this->getContainer();

        // DateTime/DateTimeTzのタイムゾーンを設定.
        $timezone = $container->getParameter('timezone');
        UTCDateTimeType::setTimeZone($timezone);
        UTCDateTimeTzType::setTimeZone($timezone);
        date_default_timezone_set($timezone);

        // RFC違反のメールを送信できるよう独自のValidationを設定
        if (!$container->getParameter('eccube_rfc_email_check')) {
            // RFC違反のメールを許容する
            \Swift_DependencyContainer::getInstance()
                ->register('email.validator')
                ->asSharedInstanceOf(NoRFCEmailValidator::class);
        }

        // Activate to $app
        $app = Application::getInstance(['debug' => $this->isDebug()]);
        $app->setParentContainer($container);
        $app->initialize();
        $app->boot();

        $container->set('app', $app);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $confDir = $this->getProjectDir().'/app/config/eccube';
        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->environment)) {
            $loader->load($confDir.'/packages/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        }
        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/services_'.$this->environment.self::CONFIG_EXTS, 'glob');

        // プラグインのservices.phpをロードする.
        $dir = dirname(__DIR__).'/../app/Plugin/*/Resource/config';
        $loader->load($dir.'/services'.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $container = $this->getContainer();

        $forceSSL = $container->getParameter('eccube_force_ssl');
        $scheme = $forceSSL ? 'https' : 'http';
        $routes->setSchemes($scheme);

        $confDir = $this->getProjectDir().'/app/config/eccube';
        if (is_dir($confDir.'/routes/')) {
            $builder = $routes->import($confDir.'/routes/*'.self::CONFIG_EXTS, '/', 'glob');
            $builder->setSchemes($scheme);
        }
        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $builder = $routes->import($confDir.'/routes/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
            $builder->setSchemes($scheme);
        }
        $builder = $routes->import($confDir.'/routes'.self::CONFIG_EXTS, '/', 'glob');
        $builder->setSchemes($scheme);
        $builder = $routes->import($confDir.'/routes_'.$this->environment.self::CONFIG_EXTS, '/', 'glob');
        $builder->setSchemes($scheme);

        // 有効なプラグインのルーティングをインポートする.
        $plugins = $container->getParameter('eccube.plugins.enabled');
        $pluginDir = $this->getProjectDir().'/app/Plugin';
        foreach ($plugins as $plugin) {
            $dir = $pluginDir.'/'.$plugin.'/Controller';
            if (file_exists($dir)) {
                $builder = $routes->import($dir, '/', 'annotation');
                $builder->setSchemes($scheme);
            }
        }
    }

    protected function build(ContainerBuilder $container)
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

        if ($this->environment !== 'install') {
            // テンプレートフックポイントを動作させるように.
            $container->addCompilerPass(new TemplateListenerPass());
        }

        // twigのurl,path関数を差し替え
        $container->addCompilerPass(new TwigExtensionPass());

        $container->register('app', Application::class)
            ->setSynthetic(true)
            ->setPublic(true);

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
    }

    protected function addEntityExtensionPass(ContainerBuilder $container)
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        // Eccube
        $paths = ['%kernel.project_dir%/src/Eccube/Entity'];
        $namespaces = ['Eccube\\Entity'];
        $reader = new Reference('annotation_reader');
        $driver = new Definition(AnnotationDriver::class, [$reader, $paths]);
        $driver->addMethodCall('setTraitProxiesDirectory', [$projectDir.'/app/proxy/entity']);
        $container->addCompilerPass(new DoctrineOrmMappingsPass($driver, $namespaces, []));

        // Customize
        $container->addCompilerPass(DoctrineOrmMappingsPass::createAnnotationMappingDriver(
            ['Customize\\Entity'],
            ['%kernel.project_dir%/app/Customize/Entity']
        ));

        // Plugin
        $pluginDir = $projectDir.'/app/Plugin';
        $finder = (new Finder())
            ->in($pluginDir)
            ->sortByName()
            ->depth(0)
            ->directories();
        $plugins = array_map(function ($dir) {
            return $dir->getBaseName();
        }, iterator_to_array($finder));

        foreach ($plugins as $code) {
            if (file_exists($pluginDir.'/'.$code.'/Entity')) {
                $container->addCompilerPass(DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                    ['Plugin\\'.$code.'\\Entity'],
                    ['%kernel.project_dir%/app/Plugin/'.$code.'/Entity']
                ));
            }
        }
    }

    protected function loadEntityProxies()
    {
        foreach (glob(__DIR__.'/../../app/proxy/entity/*.php') as $file) {
            require_once $file;
        }
    }
}
