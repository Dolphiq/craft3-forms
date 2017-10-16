<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 28-08-17
 * Time: 15:08
 */

namespace plugins\dolphiq\form\assets;

use yii\web\AssetBundle;

class pjaxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/dolphiq/craft3-forms/src/resources';

    public $js = [
        'js/pjax.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\widgets\PjaxAsset'
    ];
}