<?php

namespace plugins\dolphiq\form\twigextensions;

use craft\helpers\UrlHelper;

use Craft;
use plugins\dolphiq\form\assets\pjaxAsset;
use Twig_Extension;
use Twig_SimpleFunction;
use yii\validators\ValidationAsset;
use yii\widgets\ActiveFormAsset;

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

    public function dolphiqForm($handle = null, $params = []){

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
        Craft::$app->view->registerAssetBundle(pjaxAsset::className());
        Craft::$app->view->registerAssetBundle(ValidationAsset::className());
        Craft::$app->view->registerAssetBundle(ActiveFormAsset::className());

        return '<div id="dolphiqFormPlugin-'.$uniqId.'" data-ajaxfill="'.UrlHelper::actionUrl('dolphiq-form/main/index', ['handle' => $handle, 'params' => json_encode($params)]).'" class="form--wrapper"></div>';
    }


}
