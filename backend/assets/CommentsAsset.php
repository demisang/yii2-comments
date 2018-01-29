<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

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
