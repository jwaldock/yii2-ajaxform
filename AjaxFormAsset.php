<?php
namespace jwaldock\ajaxform;

use yii\web\AssetBundle;

class AjaxFormAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'yii.ajaxForm.js'
    ];

    public $depends = [
        'yii\widgets\ActiveFormAsset'
    ];
}
