<?php
/**
 * @link https://github.com/jwaldock/yii2-ajaxform/
 * @copyright Copyright (c) 2016 Joel Waldock
 */

namespace jwaldock\ajaxform;

use yii\web\AssetBundle;

/**
 * Asset bundle for the [[AjaxFormWidget]]
 * 
 * @author Joel Waldock
 */
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
