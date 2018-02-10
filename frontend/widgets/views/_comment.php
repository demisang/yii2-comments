<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

use yii\web\View;
use yii\helpers\Html;
use demi\comments\common\models\Comment;

/* @var $this View */
/* @var $comment Comment */

// Comment ID
$cid = $comment->primaryKey;
// Comment app component
$component = $comment->getComponent();
// Photo
$userPhoto = $comment->getUserPhoto();
// Profile url
$userProfileUrl = $comment->getUserProfileUrl();
// User name
$username = $comment->getUsername();
if (empty($username)) {
    $username = 'Anonymous';
}

// Generate link tag to user profile view
$profileLink = $userProfileUrl ? Html::a($username, $userProfileUrl, ['class' => 'p-author h-card']) : $username;

// NOTE that CSS-classes: "p-author", "h-card", "h-entry", "e-content", "dt-published"(within <time> tag)
// are special for microformats2 - http://microformats.org/wiki/microformats2 (it's can improve page SEO).
// But you can remove it's without problems.
?>
<article class="comment h-entry<?php if (empty($userPhoto)) { echo ' no-user-photo'; } ?>">
    <a name="<?= 'comment-' . $cid ?>"></a>
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
        <div class="comment-info">
            <time class="dt-published" datetime="<?= $comment->created_at ?>"><?= $comment->fDate ?></time>
            <?= Html::a('#', '#comment-' . $cid, ['class' => 'u-url']) ?>
        </div>

        <div class="comment-text e-content">
            <p><?= $comment->getPreparedText() ?></p>
        </div>
        <div class="comment-bottom">
            <div class="comment-actions">
                <a class="reply-button" data-comment-id="<?= $cid ?>" href="#">Reply</a>

                <?php if ($comment->canUpdate()): ?>
                <?= Html::a('Edit', ['/comment/default/update', 'id' => $cid], ['class' => 'update-button']) ?>
                <?php endif ?>

                <?php if ($comment->canDelete()): ?>
                <?= Html::a('Delete', ['/comment/default/delete', 'id' => $cid], ['class' => 'delete-button']) ?>
                <?php endif ?>

            </div>
        </div>
    </div>
</article>
