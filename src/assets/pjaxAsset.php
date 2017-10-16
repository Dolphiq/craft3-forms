<?php

/**
 * Created by Dolphiq
 * Lucas Weijers
 * Date: 2017-10-16
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