<?php

namespace demi\comments\backend\assets;

use yii\web\AssetBundle;

class CommentsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/demi/comments/backend/assets/src';
    public $js = [
        'comments.js',
    ];
    public $css = [
        'comments.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
