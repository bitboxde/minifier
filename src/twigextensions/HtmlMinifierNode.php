<?php
namespace bitboxde\minifier\twigextensions;

use Twig\Compiler;
use Twig\Node\Node;

class HtmlMinifierNode extends Node
{
    public function __construct(Node $body, $lineno, $tag = 'minify')
    {
        parent::__construct(['body' => $body], [], $lineno, $tag);
    }
    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write('$extension = $this->env->getExtension(\\bitboxde\\minifier\\twigextensions\\Extension::class);' . "\n")
            ->write('echo $extension->minify($this->env, ob_get_clean());' . "\n");
    }
}