<?php

namespace Eccube\DependencyInjection\Compiler;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\AbstractTypeExtension;

/**
 * サービスタグの自動設定を行う
 *
 * 以下のタグは自動設定が行われないため, 自動設定対象になるように処理する
 *
 * - doctrine.event_subscriber
 * - form.type_extension
 *
 * PluginPassで無効なプラグインのタグは解除されるため, PluginPassより先行して実行する必要がある
 */
class AutoConfigurationTagPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->configureDoctrineEventSubscriberTag($container);
        $this->configureFormTypeExtensionTag($container);
    }

    protected function configureDoctrineEventSubscriberTag(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(EventSubscriber::class)
            ->addTag('doctrine.event_subscriber');
    }

    protected function configureFormTypeExtensionTag(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();
            if (!is_subclass_of($class, AbstractTypeExtension::class)) {
                continue;
            }
            if ($definition->hasTag('form.type_extension')) {
                continue;
            }

            $ref = new \ReflectionClass($class);
            $instance = $ref->newInstanceWithoutConstructor();
            $type = $instance->getExtendedType();

            $definition->addTag('fform.type_extension', ['extended_type' => $type]);
        }
    }
}
