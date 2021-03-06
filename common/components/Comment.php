<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\comments\common\components;

use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * Comments component
 *
 * @property \demi\comments\common\models\Comment $model
 */
class Comment extends Component
{
    /** @var array Material Types: [1 => 'Posts', 2 => 'Products'] */
    public $types;
    /** @var string Classname of app [[User]] model */
    public $userModelClass = 'common\models\User';
    /** @var string Classname of [[Comment]] model */
    public $commentModelConfig = ['class' => 'demi\comments\common\models\Comment'];
    /**
     * Check access for current user to Create new comment.
     *
     * You can set callable-function such as:
     *
     * function($materialType, $materialId) {
     *     return !\Yii::$app->user->isGuest();
     * }
     *
     * @var bool|callable
     */
    public $canCreate = true;
    /**
     * Check access for current user to Update existing comment
     *
     * You can set callable-function such as:
     *
     * function($comment) {
     *     return !$comment->isAnonymous && \Yii::$app->user->id == $comment->user_id;
     * }
     *
     * @var bool|callable
     */
    public $canUpdate = false;
    /**
     * Check access for current user to Delete existing comment
     *
     * @var bool|callable
     * @see [[canUpdate]]
     */
    public $canDelete = false;
    /**
     * Anonymous function to get user display name
     *
     * @var null|callable
     */
    public $getUsername;
    /**
     * Anonymous function to get user profile view url
     *
     * @var null|callable
     */
    public $getUserProfileUrl;
    /**
     * Anonymous function to get user photo image src
     *
     * @var null|callable
     */
    public $getUserPhoto;
    /**
     * Anonymous function to get prepared(for output) comment text
     *
     * @var null|callable
     */
    public $getCommentText;
    /**
     * Anonymous function to get comment create time.
     *
     * @var null|callable
     */
    public $getCommentDate;
    /**
     * Anonymous function to get comment permalink.
     * Primary used only in backend for action "goToComment", not for frontend.
     *
     * @var null|callable
     */
    public $getPermalink;
    /** @var string Path to view file for render comments list (<ul> and <li> tags + nested) */
    public $listView = '@vendor/demi/comments/frontend/widgets/views/comments';
    /** @var string Path to view file for render each comment item (inside the <li> tag) */
    public $itemView = '@vendor/demi/comments/frontend/widgets/views/_comment';
    /** @var string Path to view file for render new comment form */
    public $formView = '@vendor/demi/comments/frontend/widgets/views/form';
    /**
     * reCAPTCHA API public key
     * Follow this link https://www.google.com/recaptcha/admin to get your API keys
     *
     * @var string|callable
     */
    public $reCaptchaSiteKey;
    /**
     * reCAPTCHA API secret key
     * Follow this link https://www.google.com/recaptcha/admin to get your API keys
     *
     * @var string|callable
     */
    public $reCaptchaSecretKey;

    public function init()
    {
        parent::init();

        if (!is_array($this->types)) {
            throw new Exception('You must specify $types for "comment" component');
        }

        // Call callable config params
        $callableConfigKeys = ['reCaptchaSiteKey', 'reCaptchaSecretKey'];
        foreach ($callableConfigKeys as $key) {
            if (is_callable($this->$key)) {
                $this->$key = call_user_func($this->$key);
            }
        }
    }

    /**
     * Get new Comment model object
     *
     * @return \demi\comments\common\models\Comment
     */
    public function getModel()
    {
        return Yii::createObject($this->commentModelConfig);
    }
}
