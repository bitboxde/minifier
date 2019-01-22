<?php
namespace bitboxde\minifier\twigextensions;

class HtmlMinifierTokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();
        $lineNumber = $token->getLine();

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideMinifyEnd'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        return new HtmlMinifierNode($body, $lineNumber, $this->getTag());
    }
    public function getTag()
    {
        return 'minify';
    }
    public function decideMinifyEnd(\Twig_Token $token)
    {
        return $token->test('endminify');
    }
}