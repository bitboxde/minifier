<?php
namespace bitboxde\minifier\minify;

use bitboxde\minifier\Minifier;

class JS extends \MatthiasMullie\Minify\JS
{
    protected $targetFile;
    protected $options = [];

    public function setTargetFile($targetFile) {
        $this->targetFile = $targetFile;

        return $this;
    }

    public function addOptions($options) {
        $this->options = array_merge_recursive($this->options, $options);

        return $this;
    }

    public function minify($path = null)
    {
        $storePath = \Yii::getAlias(Minifier::getInstance()->getSettings()->jsPath);
        $registerUrl = \Yii::getAlias(Minifier::getInstance()->getSettings()->jsUrl);

        if(!$path) {
            $path = $this->getTargetFile();

            if(strpos($path, 'md5') !== false) {
                $path = md5(implode(',', array_keys($this->data))) . '.js';
            }
        }

        $doMinify = false;
        $targetFilePath = $storePath . '/' . $path;

        if(file_exists($targetFilePath)) {
            $targetMTime = filemtime($targetFilePath);

            foreach ($this->data as $filePath => $fileContent) {
                $fileMTime = filemtime($filePath);
                if($fileMTime && $fileMTime > $targetMTime) {
                    $doMinify = true;
                    break;
                }
            }
        } else {
            $doMinify = true;
        }

        \Craft::$app->getView()->registerJsFile($registerUrl . '/' . $path, $this->options);

        if($doMinify) {
            return parent::minify($targetFilePath);
        }

        return false;
    }

    public function getTargetFile() {
        return $this->targetFile;
    }
}