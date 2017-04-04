<?php
namespace Eccube\Twig\Extension;

abstract class AbstractTokenParser extends \Twig_TokenParser
{
    protected $app;
    public function parse(\Twig_Token $token)
    {
        // 引き数を取得
        $expr = $this->parser->getExpressionParser()->parseExpression();
        // トークンが閉じタグかどうか判定
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        return new GenericNode($expr, $token->getLine(), $this->getTag(), $this->app['eccube.twig.node.'.$this->getTag()]);
    }

    abstract public function getTag();
}
