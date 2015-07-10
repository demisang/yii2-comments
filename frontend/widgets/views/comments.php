<?php

use yii\helpers\Html;
use yii\web\View;
use demi\comments\common\models\Comment;
use demi\comments\frontend\widgets\Comments;

/* @var $this View */
/* @var $widget Comments */
/* @var $comments Comment[] */
/* @var $parentId int|null */
/* @var $nestedLevel int */

$comments = $widget->comments;
$hasComments = false; // Check that $comments has contain at least one comment with parent_id==$patentId
$ulOptions = $parentId ? $widget->nestedOptions : $widget->options;

if ($nestedLevel <= $widget->maxNestedLevel) {
    $content[] = Html::beginTag('ul', $ulOptions);
}

foreach ($comments as $comment) {
    if ($comment->parent_id != $parentId) {
        // Render only one level of comments. Level based on $parentId
        continue;
    }

    // Make mark that in this level exists at least one comment
    $hasComments = true;

    $content[] = "\t<li>";

    // Render comment data
    $content[] = "\t\t" . $this->render($widget->component->itemView, ['comment' => $comment]);

    // Recursive render sub-comments
    $subComments = $this->render($widget->component->listView, [
        'widget' => $widget,
        'comments' => $comments,
        'parentId' => $comment->id,
        'nestedLevel' => $nestedLevel + 1,
    ]);
    if (!empty($subComments)) {
        $content[] = $subComments;
    }

    $content[] = "\t</li>";
}

if ($nestedLevel <= $widget->maxNestedLevel) {
    $content[] = Html::endTag('ul');
}

if ($hasComments || $nestedLevel === 1) {
    echo implode("\n", $content);
}