<?php
/**
 * Minifier plugin for Craft CMS 3.x
 *
 * For CSS and JavaScript
 *
 * @link      https://www.bitbox.de
 * @copyright Copyright (c) 2019 bitbox GmbH & Co. KG
 */

namespace bitboxde\minifier\assetbundles\Minifier;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    bitbox GmbH & Co. KG
 * @package   Minifier
 * @since     1.0.0
 */
class MinifierAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@bitboxde/minifier/assetbundles/Minifier/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Minifier.js',
        ];

        $this->css = [
            'css/Minifier.css',
        ];

        parent::init();
    }
}
