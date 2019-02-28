<?php
namespace bitboxde\minifier\services;


use bitboxde\minifier\events\ViewEvent;
use bitboxde\minifier\Minifier;
use craft\base\Component;
use Symfony\Component\Filesystem\Filesystem;
use yii\base\Exception;

class View extends Component
{
    const EVENT_BEFORE_MINIFY_FILE = 'beforeMinifyFile';

    /**
     * @var array the registered CSS files.
     */
    protected $cssFiles = [];
    /**
     * @var array the registered Js files.
     */
    protected $jsFiles = [];

    /**
     * @var bool User logged in as Admin or is devMode
     */
    protected $doMinify = false;

    public function init()
    {
        parent::init();

        $this->doMinify = $this->doMinify();
    }

    /**
     * @param string $url
     * @param array $options
     * @param string $targetFile
     *
     * @return View
     * @throws Exception
     */
    public function registerCssFile($url, $options = [], $targetFile = null) {
        return $this->addFile('css', $url, $options, $targetFile);
    }

    /**
     * @param string $url
     * @param array $options
     * @param string $targetFile
     *
     * @return View
     * @throws Exception
     */
    public function registerJsFile($url, $options = [], $targetFile = null) {
        return $this->addFile('js', $url, $options, $targetFile);
    }

    /**
     * @param string $type 'Js' or 'Css'
     * @param string $url
     * @param array $options
     * @param null $targetFile
     * @param bool $fallback
     *
     * @return $this
     * @throws Exception
     */
    protected function addFile($type, $url, $options = [], $targetFile = null) {
        $type = strtolower($type);

        $options['targetFile'] = $targetFile;
        $options = $this->getFileOptions($type, $options);

        if($this->doMinify && !$this->isExternalFile($url)) {
            $url = Minifier::parseEnv($options['basePath'] . $url);

            if(file_exists($url)) {

                if(!$options['targetFile']) {
                    $options['hash'] = true;
                    ksort($options);
                    $options['targetFile'] = md5('hash-' . implode('-', $options));
                }

                $filesProperty = $type . 'Files';
                $targetFilePath = $options['targetPath'] . '/' . $options['targetFile'];

                if(!isset($this->$filesProperty[$targetFilePath])) {
                    $this->$filesProperty[$targetFilePath] = array(
                        'type'          => $type,
                        'options'       => [],
                        'files'         => []
                    );
                }

                $this->$filesProperty[$targetFilePath]['files'][] = $url;
                $this->$filesProperty[$targetFilePath]['options'] = array_replace_recursive(
                    $this->$filesProperty[$targetFilePath]['options'],
                    $options
                );

            } else {
                throw new Exception(sprintf('The file "%s" does not exist.', $url));
            }
        } else {
            $registerMethod = sprintf('register%sFile', ucfirst($type));
            if(!$this->isExternalFile($url)) {
                $url = Minifier::parseEnv($options['baseUrl'] . $url);
            }

            \Craft::$app->getView()->$registerMethod($url, $this->getRegisterOptions($options), $targetFile);
        }

        return $this;
    }

    /**
     * Is checking if the files have changed and a minifing is necessary.
     *
     * @param $type
     *
     * @return bool
     */
    public function minify($type) {
        $type = strtolower($type);
        $property = $type . 'Files';

        foreach($this->$property as $key => $data) {
            $options = $data['options'];

            if(isset($options['hash']) && $options['hash']) {
                $options['targetFile'] = md5(implode(',', array_values($data['files'])));
            }

            $options['targetFile'] .=  '.' . $type;

            $options['targetPath'] = Minifier::parseEnv($options['targetPath']);

            $fs = new Filesystem();
            if(!$fs->exists($options['targetPath'])) {
                $fs->mkdir($options['targetPath'], 0775);
            }

            $targetFilePath = $options['targetPath'] . '/' . $options['targetFile'];
            $targetFileUrl = $options['targetUrl'] . '/' . $options['targetFile'];

            $doMinify = $this->targetFileIsOlder($targetFilePath, $data['files']);
            $paramTime = $doMinify ? time() : filemtime($targetFilePath);

            $registerMethod = 'register' . ucfirst($type) . 'File';

            \Craft::$app->getView()->$registerMethod($targetFileUrl . '?c=' . $paramTime, $this->getRegisterOptions($options));

            if($doMinify) {
                $class = Minifier::getConfig()->getClass($type);
                $minifier = new $class();

                foreach ($data['files'] as $filePath) {
                    // Fire an 'beforeAddFile' event
                    if ($this->hasEventHandlers(self::EVENT_BEFORE_MINIFY_FILE)) {
                        $event = new ViewEvent([
                            'filePath'      => $filePath,
                            'type'          => $type
                        ]);

                        $this->trigger(self::EVENT_BEFORE_MINIFY_FILE, $event);

                        $filePath = $event->output ?: $filePath;
                    }

                    $minifier->add($filePath);
                }

                $minifier->minify($targetFilePath);
            }

            unset($this->$property[$key]);
        }
    }

    public function targetFileIsOlder($targetFile, $files) {
        if(!file_exists($targetFile)) {
            return true;
        }

        $targetMTime = filemtime($targetFile);

        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $filePath) {
            $fileMTime = filemtime($filePath);
            if ($fileMTime && $fileMTime > $targetMTime) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the file is external. At this moment, we can't minifiy
     * external files.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isExternalFile($path) {
        $parsed = parse_url($path);
        if (
            // file is elsewhere
            isset($parsed['host']) ||
            // file responds to queries (may change, or need to bypass cache)
            isset($parsed['query'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Checks the devMode, the logged in user and the Plugin-Settings Disable for "Admin" and Enable for devMode
     *
     * @return bool
     */
    public function doMinify() {
        if (Minifier::getInstance()->getSettings()->disableAdmin && \Craft::$app->getUser()->getIsAdmin()) {
            return false;
        }

        if(Minifier::getInstance()->getSettings()->enableDevMode && \Craft::$app->getConfig()->getGeneral()->devMode) {
            return true;
        }

        return !\Craft::$app->getConfig()->getGeneral()->devMode;
    }

    /**
     * If you want to work with an other Class. Extend the default class or be sure the new class has
     * the following methods:
     *  - add(string $data)
     *  - addOptions(array $options)
     *  - setTargetFile(string $targetFile)
     *  - minify(string $path = null)
     *
     * @param string $className
     *
     * @return $this
     */
    public function setJsMinifierClass($className) {
        $this->jsMinifierClass = $className;

        return $this;
    }

    /**
     * If you want to work with an other Class. Extend the default class or be sure the new class has
     * the following methods:
     *  - add(string $data)
     *  - addOptions(array $options)
     *  - setTargetFile(string $targetFile)
     *  - minify(string $path = null)
     *
     * @param string $className
     *
     * @return $this
     */
    public function setCssMinifierClass($className) {
        $this->cssMinifierClass = $className;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsMinifierClass() {
        return $this->jsMinifierClass;
    }

    /**
     * @return string
     */
    public function getCssMinifierClass() {
        return $this->jsMinifierClass;
    }

    /**
     * @param string $type
     * @param array $options
     *
     * @return array
     */
    protected function getFileOptions(string $type, array $options) {
        $defaultOptions = [
            'targetFile'    => null,
            'hash'          => false,
            'basePath'      => Minifier::getInstance()->getSettings()->getPath($type),
            'baseUrl'       => Minifier::getInstance()->getSettings()->getUrl($type),
            'targetPath'    => Minifier::getInstance()->getSettings()->getPath($type) . '/' . Minifier::getConfig()->getMinDir($type),
            'targetUrl'     => Minifier::getInstance()->getSettings()->getUrl($type) . '/' . Minifier::getConfig()->getMinDir($type)
        ];

        if($type === 'css') {
            $defaultOptions['media'] = 'screen';
        }

        $options = array_replace_recursive($defaultOptions, $options);

        return $options;
    }

    public function getRegisterOptions(array $options) {
        unset($options['targetFile'], $options['hash'], $options['basePath'], $options['baseUrl'], $options['targetPath'], $options['targetUrl']);

        return $options;
    }
}