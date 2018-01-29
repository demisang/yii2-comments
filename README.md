Yii2-comments
===================
Yii2 module for comments management<br>
[Live frontend demo](https://orlov.io/articles/podrobno-o-renderpartial-v-yii#comments)

Installation
---
Run
```code
composer require require "demi/comments" "~1.0"
```

# Configurations
---
Create `comments` table:
```code
yii migrate --migrationPath=@vendor/demi/comments/console/migrations
```
Create config file: `/common/config/comments.php`
```php
<?php

use yii\helpers\Html;
use demi\comments\common\models\Comment;

return [
    'class' => 'demi\comments\common\components\Comment',
    // Material types list: [materialTypeIntIndex => 'Material type name']
    'types' => [
        1 => 'Publication',
        2 => 'Product',
    ],
    // User model class name (by default "common\models\User")
    'userModelClass' => 'common\models\User',
    // Anonymous function to get user display name
    'getUsername' => function (Comment $comment) {
        // By default anon comment user name
        $name = $comment->user_name;
        // If comment author is registered user
        if ($comment->user) {
            // $comment->user by default relation to your \common\models\User
            $name = $comment->user->first_name . ' ' . $comment->user->last_name;
        }

        return Html::encode($name);
    },
    // Anonymous function to get user profile view url
    'getUserProfileUrl' => function (Comment $comment) {
        // You can check if app is backend and return url to user profile edit
        return $comment->isAnonymous ? null : ['/user/view', 'id' => $comment->user_id];
    },
    // Anonymous function to get user photo image src
    'getUserPhoto' => function (Comment $comment) {
        if ($comment->isAnonymous) {
            return Yii::$app->request->baseUrl . '/images/user_noimage.png';
        }

        // $comment->user by default relation to your \common\models\User
        return $comment->user->avatar_url;
    },
    // Anonymous function to get comment text
    // By default: nl2br(Html::encode($comment->text))
    'getCommentText' => function (Comment $comment) {
        return nl2br(Html::encode($comment->text));
    },
    // Anonymous function to get comment create time
    // By default: Yii::$app->formatter->asDatetime($comment->created_at)
    'getCommentDate' => function (Comment $comment) {
        return Yii::$app->formatter->asDatetime($comment->created_at);
    },
    // Anonymous function to get comment permalink.
    // By default: '#comment-' . $comment->id
    'getPermalink' => function (Comment $comment) {
        $url = '#comment-' . $comment->id;

        // If you have "admin" subdomain, you can specify absolute url path for use "goToComment" from admin page
        if ($comment->material_type == 1) {
            // http://site.com/publication/3221#comment-4
            $url = 'http://site.com/publication/' . $comment->material_id . $url;
        } elseif ($comment->material_type == 2) {
            // http://site.com/product/44212#comment-2
            $url = 'http://site.com/product/' . $comment->material_id . $url;
        }

        return $url;
    },
    'canDelete' => function (Comment $comment) {
        // Only admin can delete comment
        return Yii::$app->has('user') && Yii::$app->user->can('admin');
    },
    'canUpdate' => function (Comment $comment) {
        if (Yii::$app->has('user') && Yii::$app->user->can('admin')) {
            // Admin can edit any comment
            return true;
        } elseif ($comment->isAnonymous) {
            // Any non-admin user cannot edit any anon comment
            return false;
        }

        // Comment can be edited by author at anytime
        // todo You can calc $comment->created_at and eg. allow comment editing by author within X hours after posting
        return Yii::$app->has('user') && Yii::$app->user->id == $comment->user_id;
    },
    // Anonymous function to set siteKey for reCAPTCHA widget
    // @see https://www.google.com/recaptcha/admin
    // You can set string value instead function
    'reCaptchaSiteKey' => function () {
        return Yii::$app->params['reCAPTCHA.siteKey'];
    },
    'reCaptchaSecretKey' => function () {
        return Yii::$app->params['reCAPTCHA.secretKey'];
    },

    // FOR FIRST RECOMMENDED USE DEFAULT VIEW FILES ADAPTED FOR BOOTSTRAP TEMPLATE,
    // CUSTOMIZE IT AFTER TESTING, SO PLEASE UNCOMMENT 'listView', 'itemView' and 'formView' LATER

    // But after checking extension is working you can customize view templates, useful bash copy commands in project root dir:
    /*
        mkdir "frontend/views/comment"
        cp "vendor/demi/comments/frontend/widgets/views/comments.php" "frontend/views/comment/myCustomCommentsListView.php"
        cp "vendor/demi/comments/frontend/widgets/views/_comment.php" "frontend/views/comment/_myCustomCommentItem.php"
        cp "vendor/demi/comments/frontend/widgets/views/form.php" "frontend/views/comment/myCustomCommentForm.php"
    */
    // ALSO: WHILE CUSTOMIZING YOU SHOULD SAVE SOME HTML CLASS SELECTORS!
    // SEE ALL SELECTORS HERE: vendor/demi/comments/frontend/widgets/assets/js/comments.js:61
    // OR YOU CAN SPECIFY YOU NEW SELECTORS FOR WIDGET CONFIG 'clientOptions': \demi\comments\frontend\widgets\Comments::$clientOptions

    // Path to view file for render comments list (<ul> and <li> tags + nested)
//    'listView' => '@frontend/views/comment/myCustomCommentsListView',
    // Path to view file for render each comment item (inside the <li> tag)
//    'itemView' => '@frontend/views/comment/_myCustomCommentItem',
    // Path to view file for render new comment form
//    'formView' => '@frontend/views/comment/myCustomCommentForm',
];
```
Include config file to `/common/config/main.php`:
```php
<?php

return [
    'components' => [
        'comment' => require(__DIR__ . '/comments.php'),
    ],
];
```
Configure frontend `/frontend/config/main.php`:
```php
return [
    'modules' => [
        'comment' => [
            'class' => 'demi\comments\frontend\CommentModule',
        ],
    ],
];
```

Configure backend module `/backend/config/main.php`:
```php
return [
    'modules' => [
        'comment' => [
            'class' => 'demi\comments\backend\CommentModule',
        ],
    ],
];
```

# Usage
---

For example, you wish add comments to model "Publication", so append publication view file `/frontend/views/publication/view.php`:
```php
<h4>Comments:</h4>

<?= \demi\comments\frontend\widgets\Comments::widget([
    // From config file "types" array key (1 => 'Publication')
    'materialType' => 1,
    // You Publication model unique identifier
    // If you don't have this value simply type your unique key eg: "123",
    // for clarify code you can make const ABOUT_PAGE_COMMENTS_ID = 123
    'materialId' => $model->id,
    
    // RECOMENDED FOR FIRST RUN COMMENTED OPTIONS BELOW AND CUSTOMIZED IT AFTER TESTING
    // HTML-options for main comments ul-tag
    'options' => [
        'class' => 'comments list-unstyled',
    ],
    // HTML-options for nested comments ul-tag
    'nestedOptions' => [
        'class' => 'comments reply list-unstyled',
    ],
    // jQuery-plugin options, see all options: vendor/demi/comments/frontend/widgets/assets/js/comments.js:55
    'clientOptions' => [
        'deleteComfirmText' => 'Are you sure you want to delete this comment?',
        'updateButtonText' => 'Update',
        'cancelUpdateButtonText' => 'Cancel',
    ],
    // Maximum nested level. If level reached - nested comments will be outputted without ul-tag.
    'maxNestedLevel' => 6,
    // Url for permalink (without '#')
    'materialViewUrl' => Url::to(['view', 'id' => $model->id]),
    // ActiveForm configuration
    'formConfig' => [
        // This is required config attributes, you should save it
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
    ],
]) ?>
```

# Backedn (admin page)
---
Navigate to `http://admin.site.com/comment/manage/index`<br>
ie: `<you backend url>/comment/manage/index`
