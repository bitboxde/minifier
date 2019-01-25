<?php
/**
 * Minifier plugin for Craft CMS 3.x
 *
 * For CSS and JavaScript
 *
 * @link      https://www.bitbox.de
 * @copyright Copyright (c) 2019 bitbox GmbH & Co. KG
 */

namespace bitboxde\minifier;

use bitboxde\minifier\minify\CSS;
use bitboxde\minifier\minify\JS;
use bitboxde\minifier\models\Settings;

use bitboxde\minifier\twigextensions\Extension;
use bitboxde\minifier\twigextensions\MinifierTwigExtension;
use Craft;
use craft\base\Plugin;
use craft\events\TemplateEvent;
use craft\services\Plugins;
use craft\events\PluginEvent;

use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use yii\base\Event;

/**
 * Class Minifier
 *
 * @author    bitbox GmbH & Co. KG
 * @package   Minifier
 * @since     1.0.0
 *
 * @property  \bitboxde\minifier\services\View $view
 *
 */
class Minifier extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Minifier
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->view->registerTwigExtension(new Extension());

        Event::on(
            View::class,
            View::EVENT_AFTER_RENDER_TEMPLATE,
            function(Event $event) {
                Minifier::getInstance()->view->minifyCss();
                Minifier::getInstance()->view->minifyJs();
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {

            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'minifier',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        $this->setComponents([
            'view'      => \bitboxde\minifier\services\View::class,
        ]);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'minifier/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }
}
