<?php
namespace bitboxde\minifier\twigextensions;


use bitboxde\minifier\Minifier;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use voku\helper\HtmlMin;

class Extension extends AbstractExtension
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

        $this->forceCompression = $forceCompression;
    }
    public function minify(Environment $twig, $html)
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
            new TwigFunction('minifyhtml', $this->callable, $this->options),
        );
    }
    public function getFilters()
    {
        return array(
            new TwigFilter('minifyhtml', $this->callable, $this->options),
        );
    }
}