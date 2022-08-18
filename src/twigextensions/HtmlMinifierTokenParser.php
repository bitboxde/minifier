<?php
namespace bitboxde\minifier\twigextensions;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class HtmlMinifierTokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();
        $lineNumber = $token->getLine();

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideMinifyEnd'), true);
        $stream->expect(Token::BLOCK_END_TYPE);
        return new HtmlMinifierNode($body, $lineNumber, $this->getTag());
    }
    public function getTag()
    {
        return 'minify';
    }
    public function decideMinifyEnd(Token $token)
    {
        return $token->test('endminify');
    }
}