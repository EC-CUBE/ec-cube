<?php

namespace Eccube\Twig\Extension;

use Twig\Compiler;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class TemplateEventExtension extends AbstractExtension
{
    public function getNodeVisitors()
    {
        return [new TemplateEventNodeVisiror()];
    }
}

class TemplateEventNodeVisiror implements NodeVisitorInterface
{
    public function enterNode(Node $node, Environment $env): Node
    {
        return $node;
    }

    public function leaveNode(Node $node, Environment $env): Node
    {
        if ($node instanceof ModuleNode) {
            if (\str_starts_with($node->getTemplateName(), '__string_template__')) {
                return $node;
            }
            $node->setNode('display_start', new Node([new TemplateEventNode(), $node->getNode('display_start')]));
        }

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }
}

class TemplateEventNode extends Node
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write('$__eccube__gblobal = $this->env->getGlobals();')
            ->raw("\n")
            ->write('$__eccube__eventDispatcher = $__eccube__gblobal[\'event_dispatcher\'];')
            ->raw("\n")
            ->write('$__eccube__source = $this->env->getLoader()->getSourceContext($this->getTemplateName())->getCode();')
            ->raw("\n")
            ->write('$__eccube__event = new \\Eccube\\Event\\TemplateEvent($this->getTemplateName(), $__eccube__source, $context);')
            ->raw("\n")
            ->write('$__eccube__eventDispatcher->dispatch($__eccube__event, $this->getTemplateName());')
            ->raw("\n")
            ->write('$context = $__eccube__event->getParameters();')
            ->raw("\n")
            ->write('if ($__eccube__event->getSource() !== $__eccube__source) {')
            ->indent()
                ->raw("\n")
                ->write('$__eccube__newTemplate = $this->env->createTemplate($__eccube__event->getSource());')
                ->raw("\n")
                ->write('$__eccube__newTemplate->display($__eccube__event->getParameters());')
                ->raw("\n")
                ->write('return;')
                ->raw("\n")
            ->outdent()
            ->write('}')
            ->raw("\n\n")
        ;
    }
}
