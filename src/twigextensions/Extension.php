<?php
namespace bitboxde\minifier\twigextensions;


use bitboxde\minifier\Minifier;
use voku\helper\HtmlMin;

class Extension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var array
     */
    private $options = array(
        'is_safe'           => array('html'),
        'needs_environment' => true,
    );
    /**
     * @var callable
     */
    private $callable;
    /**
     * @var HtmlMin
     */
    private $minifier;
    /**
     * @var bool
     */
    private $forceCompression;
    /**
     * @param bool $forceCompression Default: false. Forces compression regardless of Twig's debug setting.
     */
    public function __construct($forceCompression = false)
    {
        $this->minifier = new HtmlMin();
        $this->minifier->doRemoveWhitespaceAroundTags(true);
        $this->minifier->doRemoveEmptyAttributes(true);
        $this->minifier->doRemoveWhitespaceAroundTags(true);
    }
    public function minify(\Twig_Environment $twig, $html)
    {
        if (!$twig->isDebug() || $this->forceCompression) {

            return $this->minifier->minify($html);
        }
        return $html;
    }
    public function getTokenParsers()
    {
        return array(
            new HtmlMinifierTokenParser(),
        );
    }
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('minifyhtml', $this->callable, $this->options),
        );
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('minifyhtml', $this->callable, $this->options),
        );
    }

    public function getGlobals() {
        return [
            'minifier'  => Minifier::getInstance()
        ];
    }
}