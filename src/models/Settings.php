<?php
/**
 * Minifier plugin for Craft CMS 3.x
 *
 * For CSS and JavaScript
 *
 * @link      https://www.bitbox.de
 * @copyright Copyright (c) 2019 bitbox GmbH & Co. KG
 */

namespace bitboxde\minifier\models;

use bitboxde\minifier\Minifier;

use Craft;
use craft\base\Model;
use yii\base\Exception;

/**
 * @author    bitbox GmbH & Co. KG
 * @package   Minifier
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $cssPath = '@webroot/css';
    public $jsPath = '@webroot/js';
    public $cssUrl = '@web/css';
    public $jsUrl = '@web/js';
    public $disableAdmin = false;
    public $enableDevMode = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['cssPath', 'jsPath', 'cssUrl', 'jsUrl'], 'required'],
            [['cssPath', 'jsPath', 'cssUrl', 'jsUrl'], 'string'],
            [['cssPath', 'jsPath', 'cssUrl', 'jsUrl'], 'default', 'value' => 'CSS Path'],
            [['disableAdmin', 'enableDevMode'], 'boolean'],
        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();

        $this->disableAdmin = boolval($this->disableAdmin);
        $this->enableDevMode = boolval($this->enableDevMode);
    }

    /**
     * The CSS-Path you set in Plugin-Settings. Might be an alias.
     * @return string
     */
    public function getCssPath() {
        return $this->cssPath;
    }

    /**
     * The JS-Path you set in Plugin-Settings. Might be an alias.
     * @return string
     */
    public function getJsPath() {
        return $this->jsPath;
    }

    /**
     * The Path you set in Plugin-Settings, for the given $type. Might be an alias.
     * @param string $type js|css
     *
     * @return string|null
     */
    public function getPath(string $type) {
        $type = strtolower($type);

        if($type === 'css') {
            return $this->getCssPath();
        } elseif($type === 'js') {
            return $this->getJsPath();
        }

        return null;
    }

    /**
     * The CSS-URL you set in Plugin-Settings. Might be an alias.
     * @return string
     */
    public function getCssUrl() {
        return $this->cssUrl;
    }

    /**
     * The JS-URL you set in Plugin-Settings. Might be an alias.
     * @return string
     */
    public function getJsUrl() {
        return $this->jsUrl;
    }

    /**
     * The URL you set in Plugin-Settings, for the given $type. Might be an alias.
     * @param string $type js|css
     *
     * @return string|null
     */
    public function getUrl(string $type) {
        $type = strtolower($type);

        if($type === 'css') {
            return $this->getCssUrl();
        } elseif($type === 'js') {
            return $this->getJsUrl();
        }

        return null;
    }
}
