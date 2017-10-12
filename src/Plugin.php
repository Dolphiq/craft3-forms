<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 19-05-17
 * Time: 15:23
 */


namespace plugins\dolphiq\form;

use Craft;
use plugins\dolphiq\form\controllers\MainController;
use plugins\dolphiq\form\models\Settings;
use plugins\dolphiq\form\twigextensions\YiiTwigExtension;

class Plugin extends \craft\base\Plugin
{
    public $hasCpSettings = true;

    public function init()
    {
        parent::init();

        // Custom initialization code goes here...
        Craft::$app->view->twig->addExtension(new YiiTwigExtension());
    }

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        $mainController = new MainController('dq-forms-main', 'dolphiq-craft3-forms');

        return \Craft::$app->getView()->renderTemplate('dolphiq-craft3-forms/settings', [
            'forms' => $mainController->getForms(),
            'settings' => $this->getSettings()
        ]);
    }
}

?>