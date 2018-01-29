<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\comments\frontend;

use demi\comments\common\components\BaseCommentModule;

class CommentModule extends BaseCommentModule
{
    public $controllerNamespace = 'demi\comments\frontend\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
