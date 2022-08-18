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

use bitboxde\minifier\events\ViewEvent;
use bitboxde\minifier\models\Settings;

use bitboxde\minifier\services\Config;
use bitboxde\minifier\twigextensions\Extension;
use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\TemplateEvent;
use craft\helpers\App;
use craft\services\Plugins;
use craft\events\PluginEvent;

use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use yii\base\Event;

/**
 * Class Minifier
 * @author    bitbox GmbH & Co. KG
 * @package   Minifier
 * @since     1.0.0
 * @property  \bitboxde\minifier\services\View $view
 * @property  \bitboxde\minifier\services\Config $config
 * @method Settings getSettings()
 */
class Minifier extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Minifier
     */
    public static $plugin;
    public static $defaultConfig;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->getView()->registerTwigExtension(new Extension());
        Craft::$app->getView()->getTwig()->addGlobal('minifier', $this);

        Event::on(
            View::class,
            View::EVENT_AFTER_RENDER_TEMPLATE,
            function(Event $event) {
                Minifier::getInstance()->view->minify('css');
                Minifier::getInstance()->view->minify('js');
            }
        );

//        Event::on(
//            \bitboxde\minifier\services\View::class,
//            \bitboxde\minifier\services\View::EVENT_BEFORE_MINIFY_FILE,
//            function(ViewEvent $event) {
//                $pathinfo = pathinfo($event->filePath);
//                $ext = $pathinfo['extension'];
//
//                if($ext === 'less') {
//                    $parser = new \lessc();
//                    $event->output = $parser->compileFile($event->filePath);
//                }
//            }
//        );

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
            'config'    => Config::class
        ]);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
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

    public static function parseEnv(string $str = null) {
        if(method_exists(Craft::class, 'parseEnv')) {
            return App::parseEnv($str);
        }

        return Craft::getAlias($str);
    }

    /**
     * Returns only an alias or an enviroment variable. If there neither of them, it return false.
     *
     * @param $str
     *
     * @return bool|false|string
     */
    public static function getRootEnv($str) {
        if (preg_match('/^\$(\w+)$/', $str, $matches)) {
            return getenv($matches[1]);
        }

        return \Craft::getRootAlias($str);
    }

    /**
     * @return services\View
     */
    public static function getView() {
        return Minifier::getInstance()->view;
    }

    /**
     * @return Config|mixed
     */
    public static function getConfig() {
        return Minifier::getInstance()->config;
    }
}
