<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 28-08-17
 * Time: 15:08
 */

namespace plugins\dolphiq\form\assets;

use craft\web\AssetManager;
use Yii;
use yii\helpers\Url;
use yii\web\AssetBundle;

class pjaxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/dolphiq/form/src/resources';

    public $js = [
        'js/pjax.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}