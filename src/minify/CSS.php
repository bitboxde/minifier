<?php
namespace bitboxde\minifier\minify;

use bitboxde\minifier\Minifier;

class CSS extends \MatthiasMullie\Minify\CSS
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
        $storePath = \Yii::getAlias(Minifier::getInstance()->getSettings()->cssPath);
        $registerUrl = \Yii::getAlias(Minifier::getInstance()->getSettings()->cssUrl);

        if(!$path) {
            $path = $this->getTargetFile();

            if($path === 'md5') {
                $path = md5(implode(',', array_keys($this->data))) . '.css';
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

        \Craft::$app->getView()->registerCssFile($registerUrl . '/' . $path, $this->options);

        if($doMinify) {
            return parent::minify($targetFilePath);
        }

        return false;
    }

    public function getTargetFile() {
        return $this->targetFile;
    }
}