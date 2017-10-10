<?php

namespace plugins\dolphiq\form\twigextensions;

use craft\helpers\UrlHelper;

use plugins\dolphiq\form\models\vacancyForm;
use plugins\dolphiq\form\controllers\MainController;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

use Craft;
use ReflectionProperty;
use yii\base\Module;
use yii\web\View;
use yii\web\YiiAsset;

class YiiTwigExtension extends Twig_Extension
{

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'YiiTwig';
    }


    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('dolphiqForm', [$this, 'dolphiqForm']),
        ];
    }

    public function dolphiqForm($type = null, $params = []){

        $uniqId = uniqid().'-'.time();

        $script = <<<JS
var formPluginUrl = $('#dolphiqFormPlugin-$uniqId').data('ajaxfill');

$.ajax({
  url: formPluginUrl,
  cache: false
}).done(function( html ) {
  $( "#dolphiqFormPlugin-$uniqId" ).html( html );
});
JS;

        Craft::$app->view->registerJs($script);

        return '<div id="dolphiqFormPlugin-'.$uniqId.'" data-ajaxfill="'.UrlHelper::actionUrl('dolphiqform/main/index', ['type' => $type, 'params' => json_encode($params)]).'" class="form--wrapper"></div>';
    }


}
