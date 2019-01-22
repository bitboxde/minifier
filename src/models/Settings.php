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
    public $cssPath = '@webroot/css/min';
    public $jsPath = '@webroot/js/min';
    public $cssUrl = '@web/css/min';
    public $jsUrl = '@web/js/min';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cssPath', 'jsPath', 'cssUrl', 'jsUrl'], 'required'],
            [['cssPath', 'jsPath', 'cssUrl', 'jsUrl'], 'string'],
            [['cssPath', 'jsPath', 'cssUrl', 'jsUrl'], 'default', 'value' => 'CSS Path'],
        ];
    }
}
