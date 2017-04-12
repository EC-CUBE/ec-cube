<?php
namespace Eccube\Twig\Extension;

class GenericTokenParser extends AbstractTokenParser
{

    protected $tagName;

    public function __construct($app, $tagName)
    {
        $this->app = $app;
        $this->tagName = $tagName;
    }
    public function getTag()
    {
        return $this->tagName;
    }
}
