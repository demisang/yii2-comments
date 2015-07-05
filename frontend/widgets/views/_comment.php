<?php

use demi\comments\common\models\Comment;
use demi\comments\frontend\widgets\Comments;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $widget Comments */
/* @var $comment Comment */

// Photo
$userPhoto = $comment->getUserPhoto();
if (empty($userPhoto) && $widget->defaultPhoto) {
    $userPhoto = $widget->defaultPhoto;
}
// Profile url
$userProfileUrl = $comment->getUserProfileUrl();
// User name
$username = $comment->getUsername();
if (empty($username)) {
    $username = 'Anonymous';
}

// Generate link tag to user profile view
$profileLink = $userProfileUrl ? Html::a($username, $userProfileUrl) : $username;
?>
<div class="comment<?php if (empty($userPhoto)) { echo ' no-photo'; } ?>">
    <?php if (!empty($userPhoto)): ?>
    <div class="comment-user-photo">
        <?php
        // User photo image
        $image = Html::img($userPhoto, ['alt' => Html::decode($username)]);
        // Show link+image or simple image
        echo $userProfileUrl !== null ? Html::a($image, $userProfileUrl) : $image;
        ?>
    </div>
    <?php endif ?>
    <div class="comment-body">
        <div class="comment-username"><?= $profileLink ?></div>
        <p class="comment-text"><?= $comment->getPreparedText() ?></p>
        <div class="comment-date"><?= $comment->fDate ?></div>
    </div>
</div>