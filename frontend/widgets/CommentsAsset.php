<?php

namespace demi\comments\frontend\widgets;

use yii\web\AssetBundle;

/**
 * CommentsAsset
 */
class CommentsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/demi/comments/frontend/widgets/assets';
    public $js = [
        'js/comments.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}