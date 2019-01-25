<?php
namespace bitboxde\minifier\services;


use bitboxde\minifier\Minifier;
use bitboxde\minifier\minify\CSS;
use bitboxde\minifier\minify\JS;
use craft\base\Component;
use craft\helpers\ArrayHelper;

class View extends Component
{
    /**
     * @var CSS[] array the registered CSS files.
     * @see registerCssFile()
     */
    protected $cssMinifier = [];
    /**
     * @var JS[] array the registered CSS files.
     * @see registerCssFile()
     */
    protected $jsMinifier = [];

    /** @var bool User logged in as Admin or is devMode */
    protected $doMinify = false;

    public function init()
    {
        parent::init();

        $this->doMinify = $this->doMinify();
    }

    public function registerCssFile($url, $options = [], $targetFile = null) {
        return $this->addFile('Css', $url, $options, $targetFile);
    }

    public function registerJsFile($url, $options = [], $targetFile = null) {
        return $this->addFile('Js', $url, $options, $targetFile);
    }

    protected function addFile($type, $url, $options = [], $targetFile = null) {
        $registerMethod = sprintf('register%sFile', $type);

        if($this->doMinify && $this->canMinifyFile($url)) {
            if(!$targetFile) {
                $options['hash'] = true;
                ksort($options);
                $targetFile = md5('hash-' . implode('-', $options));
            }
            $rootAlias = \Yii::getRootAlias($url);
            $url = \Yii::getAlias($url);

            if(!file_exists($url) && !$rootAlias) {
                return $this->$registerMethod('@webroot' . '/' . $url, $targetFile, $options);
            }

            $getMinifierMethod = sprintf('get%sMinifier', $type);

            /** @var CSS|JS $cssMinifier */
            $cssMinifier = $this->$getMinifierMethod($targetFile);
            $cssMinifier->add($url);
            $cssMinifier->addOptions($options);
        } else {
            \Craft::$app->getView()->$registerMethod($url, $options, $targetFile);
        }

        return $this;
    }

    /**
     * @param $key
     *
     * @return CSS
     */
    public function getCssMinifier($targetFile) {
        if(!isset($this->cssMinifier[$targetFile])) {
            $this->cssMinifier[$targetFile] = new CSS();
            $this->cssMinifier[$targetFile]->setTargetFile($targetFile);
        }

        return $this->cssMinifier[$targetFile];
    }

    /**
     * @param $key
     *
     * @return JS
     */
    public function getJsMinifier($targetFile) {
        if(!isset($this->jsMinifier[$targetFile])) {
            $this->jsMinifier[$targetFile] = new JS();
            $this->jsMinifier[$targetFile]->setTargetFile($targetFile);
        }

        return $this->jsMinifier[$targetFile];
    }

    public function minifyCss() {
        foreach($this->cssMinifier as $key => &$cssMinify) {
            $cssMinify->minify();
            unset($this->cssMinifier[$key]);
        }
    }

    public function minifyJs() {
        foreach($this->jsMinifier as $key => $jsMinify) {
            $jsMinify->minify();
            unset($this->jsMinifier[$key]);
        }
    }

    public function canMinifyFile($path) {
        $parsed = parse_url($path);
        if (
            // file is elsewhere
            isset($parsed['host']) ||
            // file responds to queries (may change, or need to bypass cache)
            isset($parsed['query'])
        ) {
            return false;
        }

        return true;
    }

    public function doMinify() {
        if (Minifier::getInstance()->getSettings()->disableAdmin && \Craft::$app->getUser()->getIsAdmin()) {
            return false;
        }

        return !\Craft::$app->getConfig()->getGeneral()->devMode;
    }
}