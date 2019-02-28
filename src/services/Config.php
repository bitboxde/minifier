<?php
namespace bitboxde\minifier\services;


use bitboxde\minifier\Minifier;
use craft\base\Component;

class Config extends Component
{
    const GENERAL_PREFIX = 'minifier';

    public $cssMinDir = 'min';
    public $jsMinDir = 'min';

    public $cssClass = '\MatthiasMullie\Minify\CSS';
    public $jsClass = '\MatthiasMullie\Minify\JS';

    public function init()
    {
        parent::init();

        $this->setProperties();
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed|null
     */
    public function get(string $name, $default = null) {
        $configName = Config::GENERAL_PREFIX . ucfirst($name);

        if(isset(\Craft::$app->getConfig()->getGeneral()->$configName)) {
            return \Craft::$app->getConfig()->getGeneral()->$configName;
        }

        return $default;
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return $this
     */
    public function set(string $name, $value) {
        $this->$name = $value;

        return $this;
    }

    /**
     * @return $this
     */
    protected function setProperties() {
        foreach (get_class_vars(get_class($this)) as $name => $value) {
            $this->set($name, $this->get($name, $this->$name));
        }

        return $this;
    }

    /**
     * The min-dir you set in General-Config, for the given $type. Might be an alias.
     * @param string $type js|css
     *
     * @return string|null
     */
    public function getMinDir(string $type) {
        $type = strtolower($type);

        if($type === 'css') {
            return $this->cssMinDir;
        } elseif($type === 'js') {
            return $this->jsMinDir;
        }

        return null;
    }

    /**
     * The minifier class you set in General-Config, for the given $type.
     * @param string $type js|css
     *
     * @return string|null
     */
    public function getClass(string $type) {
        $type = strtolower($type);

        if($type === 'css') {
            return $this->cssClass;
        } elseif($type === 'js') {
            return $this->jsClass;
        }

        return null;
    }
}