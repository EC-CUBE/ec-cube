<?php


namespace Eccube\Twig\Extension;


use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\Node\Node;
use Twig\TwigFunction;

class IgnoreRoutingNotFoundExtension extends AbstractExtension
{
    /**
     * @var RoutingExtension
     */
    private $routingExtension;

    /**
     * @var ContainerBagInterface
     */
    private $containerBag;

    public function __construct(RoutingExtension $extension, ContainerBagInterface $containerBag)
    {
        $this->routingExtension = $extension;
        $this->containerBag = $containerBag;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [$this, 'getUrl'], ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]),
            new TwigFunction('path', [$this, 'getPath'], ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]),
        ];
    }

    public function getPath(string $name, array $parameters = [], bool $relative = false): string
    {
        if ($this->containerBag->get('kernel.debug')) {
            return $this->routingExtension->getPath($name, $parameters, $relative);
        }

        try {
            return $this->routingExtension->getPath($name, $parameters, $relative);
        } catch (RouteNotFoundException $e) {
            // FIXME log関数でエラーになるので, 修正されたらコメントアウトをはずす
            // log_warning($e->getMessage(), ['exception' => $e]);

            return $this->routingExtension->getPath('homepage') . '404?bind=' . $name;
        }
    }

    public function getUrl(string $name, array $parameters = [], bool $schemeRelative = false): string
    {
        if ($this->containerBag->get('kernel.debug')) {
            return $this->routingExtension->getUrl($name, $parameters, $schemeRelative);
        }

        try {
            return $this->routingExtension->getUrl($name, $parameters, $schemeRelative);
        } catch (RouteNotFoundException $e) {
            // FIXME log関数でエラーになるので, 修正されたらコメントアウトをはずす
            // log_warning($e->getMessage(), ['exception' => $e]);

            return $this->routingExtension->getUrl('homepage', $parameters, $schemeRelative) . '404?bind=' . $name;
        }
    }

    public function isUrlGenerationSafe(Node $argsNode): array
    {
        return $this->routingExtension->isUrlGenerationSafe($argsNode);
    }
}
