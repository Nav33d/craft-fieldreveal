<?php

/**
 * Field Reveal plugin for Craft CMS 3
 *
 * View where your custom fields are being used.
 *
 * @link      https://naveedziarab.co.uk/
 * @copyright Copyright (c) 2019 Nav33d
 */

namespace nav33d\fieldreveal;

use Craft;
use craft\web\UrlManager;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\services\Elements;
use craft\events\PluginEvent;
use craft\events\ElementEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;

use yii\base\Event;

/**
 * @author    Naveed Ziarab
 * @package   Field Reveal
 * @since     1.0.0
 */

class FieldReveal extends Plugin
{
    // Static Properties
    // =========================================================================

    public static $plugin;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register services
        $this->setComponents([
            'fieldreveal' => \nav33d\fieldreveal\services\FieldReveal::class,
        ]);

        $request = Craft::$app->getRequest();
        if ( $request->getIsCpRequest() )
        {
            // Redirect to dashboard after install
            Event::on(
                Plugins::class,
                Plugins::EVENT_AFTER_INSTALL_PLUGIN,
                function(PluginEvent $event) {
                    if ( $event->plugin === $this )
                    {
                        $request = Craft::$app->getRequest();
                        if ($request->isCpRequest) {
                            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('fieldreveal/dashboard'))->send();
                        }
                    }
                }
            );
        }

        // Control panel request
        if ($request->getIsCpRequest() && !$request->getIsConsoleRequest())
        {
            // Register CP routes
            Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
              $event->rules = array_merge($event->rules, $this->getCpUrlRules());
            });
        }
    }


    /**
     * @return array
     */
    private function getCpUrlRules()
    {
      return [
        'fieldreveal' => 'fieldreveal/default/index',
        'fieldreveal/dashboard' => 'fieldreveal/default/dashboard',
      ];
    }

}
