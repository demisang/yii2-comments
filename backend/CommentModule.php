<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\comments\backend;

use Yii;
use demi\comments\backend\assets\CommentsAsset;
use demi\comments\common\components\BaseCommentModule;

class CommentModule extends BaseCommentModule
{
    public $controllerNamespace = 'demi\comments\backend\controllers';
    /**
     * Check access for current user to mark existing comment as "approved"
     *
     * You can set callable-function such as:
     *
     * function($materialType, $materialId, $comment) {
     *     return \Yii::$app->user->can('admin');
     * }
     *
     * @var bool|callable
     */
    public $canApprove = true;
    public $canUpdate = true;
    public $canDelete = true;

    public function init()
    {
        parent::init();

        // Register JS & CSS helpful files
        CommentsAsset::register(Yii::$app->view);

        // custom initialization code goes here
    }
}
