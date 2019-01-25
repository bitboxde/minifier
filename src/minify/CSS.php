<?php
namespace bitboxde\minifier\minify;

use bitboxde\minifier\Minifier;
use Symfony\Component\Filesystem\Filesystem;

class CSS extends \MatthiasMullie\Minify\CSS
{
    protected $targetFile;
    protected $options = [];

    public function setTargetFile($targetFile) {
        $this->targetFile = $targetFile;

        return $this;
    }

    public function addOptions($options) {
        $this->options = array_replace_recursive($this->options, $options);

        return $this;
    }

    public function minify($path = null)
    {
        $storePath = \Yii::getAlias(Minifier::getInstance()->getSettings()->cssPath);
        $registerUrl = \Yii::getAlias(Minifier::getInstance()->getSettings()->cssUrl);

        if(!$path) {
            $path = $this->getTargetFile();

            if(isset($this->options['hash']) && $this->options['hash']) {
                $path = md5(implode(',', array_keys($this->data))) . '.css';
                unset($this->options['hash']);
            }
        }

        $fs = new Filesystem();
        if(!$fs->exists($storePath)) {
            $fs->mkdir($storePath, 0755);
        }

        $doMinify = false;
        $targetFilePath = $storePath . '/' . $path;
        $paramTime = time();

        if(file_exists($targetFilePath)) {
            $paramTime = $targetMTime = filemtime($targetFilePath);

            foreach ($this->data as $filePath => $fileContent) {
                $fileMTime = filemtime($filePath);
                if($fileMTime && $fileMTime > $targetMTime) {
                    $doMinify = true;
                    $paramTime = time();
                    break;
                }
            }
        } else {
            $doMinify = true;
        }

        \Craft::$app->getView()->registerCssFile($registerUrl . '/' . $path . '?c=' . $paramTime, $this->options);

        if($doMinify) {
            return parent::minify($targetFilePath);
        }

        return false;
    }

    public function getTargetFile() {
        return $this->targetFile;
    }
}