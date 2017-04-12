<?php
namespace Eccube\Twig\Extension;

class GenericNode extends \Twig_Node
{
    protected $compiler_callback;
    public function __construct(\Twig_Node_Expression $expr, $lineno, $tag, \Closure $compiler_callback)
    {
        parent::__construct(array('expr' => $expr), array(), $lineno, $tag);
        $this->compiler_callback = $compiler_callback;
    }

    public function compile(\Twig_Compiler $compiler)
    {
        call_user_func($this->compiler_callback, $this, $compiler);
    }
}
