<?php
namespace bitboxde\minifier\services;


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

    /** @var bool set in general-config */
    protected $forceMinify = false;

    public function init()
    {
        parent::init();

        if(isset(\Craft::$app->getConfig()->getGeneral()->forceMinify)) {
            $this->forceMinify = \Craft::$app->getConfig()->getGeneral()->forceMinify;
        }

        $this->doMinify = $this->doMinify();
    }

    public function registerCssFile($url, $targetFile = null, $options = []) {
        if($this->doMinify && $this->canMinifyFile($url)) {
            if(!$targetFile) {
                $targetFile = 'md5';
            }
            $rootAlias = \Yii::getRootAlias($url);
            $url = \Yii::getAlias($url);

            if(!file_exists($url) && !$rootAlias) {
                return $this->registerCssFile('@webroot' . '/' . $url, $targetFile, $options);
            }

            $cssMinifier = $this->getCSSMinifier($targetFile);
            $cssMinifier->add($url);
            $cssMinifier->addOptions($options);
        } else {
            \Craft::$app->getView()->registerCssFile($url, $options, $targetFile);
        }

        return $this;
    }

    public function registerJsFile($url, $targetFile = null, $options = []) {
        if($this->doMinify && $this->canMinifyFile($url)) {
            if(!$targetFile) {
                $targetFile = 'md5';
            }

            $position = ArrayHelper::getValue($options, 'position', \craft\web\View::POS_END);

            $targetFile .= '-' . $position;

            $rootAlias = \Yii::getRootAlias($url);
            $url = \Yii::getAlias($url);

            if(!file_exists($url) && !$rootAlias) {
                return $this->registerJsFile('@webroot' . '/' . $url, $targetFile, $options);
            }

            $jsMinifier = $this->getJSMinifier($targetFile);
            $jsMinifier->add($url);
            $jsMinifier->addOptions($options);
        } else {
            \Craft::$app->getView()->registerJsFile($url, $options, $targetFile);
        }



        return $this;
    }

    /**
     * @param $key
     *
     * @return CSS
     */
    public function getCSSMinifier($targetFile) {
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
    public function getJSMinifier($targetFile) {
        if(!isset($this->jsMinifier[$targetFile])) {
            $this->jsMinifier[$targetFile] = new JS();
            $this->jsMinifier[$targetFile]->setTargetFile($targetFile);
        }

        return $this->jsMinifier[$targetFile];
    }

    public function minifyCSS() {
        foreach($this->cssMinifier as $cssMinify) {
            $cssMinify->minify();
        }
    }

    public function minifyJS() {
        foreach($this->jsMinifier as $jsMinify) {
            $jsMinify->minify();
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
        return $this->forceMinify || (!\Craft::$app->getUser()->getIsAdmin() && !\Craft::$app->getConfig()->getGeneral()->devMode);
    }
}