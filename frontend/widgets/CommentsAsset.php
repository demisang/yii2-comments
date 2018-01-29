<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

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
