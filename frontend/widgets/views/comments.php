<?php

use yii\web\View;
use demi\comments\common\models\Comment;
use demi\comments\frontend\widgets\Comments;

/* @var $this View */
/* @var $widget Comments */
/* @var $comments Comment[] */
/* @var $parentId int|null */

$comments = $widget->comments;
$hasComments = false; // Check that $comments has contain at least one comment with parent_id==$patentId
$content[] = '<ul>';

foreach ($comments as $comment) {
    if ($comment->parent_id != $parentId) {
        // Render only one level of comments. Level based on $parentId
        continue;
    }

    // Make mark that in this level exists at least one comment
    $hasComments = true;

    $content[] = "\t<li>";

    // Render comment data
    $content[] = "\t\t" . $this->render('_comment', [
            'widget' => $widget,
            'comment' => $comment,
        ]);

    // Recursive render sub-comments
    $subComments = $this->render('comments', [
        'widget' => $widget,
        'comments' => $comments,
        'parentId' => $comment->id,
    ]);
    if (!empty($subComments)) {
        $content[] = $subComments;
    }

    $content[] = "\t</li>";
}

$content[] = '</ul>';

if ($hasComments) {
    echo implode("\n", $content);
}