<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model demi\comments\common\models\Comment */

$this->title = 'Create comment';
$this->params['breadcrumbs'][] = ['label' => 'Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
