<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use demi\comments\common\models\Comment;

/* @var $this yii\web\View */
/* @var $searchModel demi\comments\backend\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Comment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin() ?>
    <?=
    GridView::widget([
        'id' => 'comments-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'dataColumnClass' => 'demi\comments\backend\components\DataColumn',
        'columns' => [
            [
                'attribute' => 'material_type',
                // 'prepend' => '<span class="glyphicon glyphicon-th-list"></span>',
                'filter' => Yii::$app->comment->types,
                'filterOptions' => ['class' => 'col-md-1'],
                'value' => function ($model) {
                    /* @var $model Comment */
                    $types = Yii::$app->comment->types;

                    return isset($types[$model->material_type]) ? $types[$model->material_type] : null;
                },
            ],
            [
                'attribute' => 'material_id',
                'filterInputOptions' => ['class' => 'form-control', 'id' => null],
                'filterOptions' => ['class' => 'col-md-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var $model Comment */

                    return Html::a('#' . $model->material_id . ' open', ['go-to-comment', 'id' => $model->id], [
                        'target' => '_blank',
                        'title' => Yii::t('app', 'Go to comment'),
                        'data-pjax' => 0,
                        'class' => 'btn btn-default',
                    ]);
                },
                // 'prepend' => '<span class="glyphicon glyphicon-chevron-right"></span>',
            ],
            [
                'attribute' => 'text',
                'format' => 'raw',
                'prepend' => '<span class="glyphicon glyphicon-comment form-control-feedback" aria-hidden="true"></span>',
                'value' => function ($model) {
                    /* @var $model Comment */
                    $shortText = StringHelper::truncate(strip_tags($model->text), 160);

                    return Html::encode($shortText);
                },
            ],
            [
                'attribute' => 'user_id',
                'prepend' => '<span class="glyphicon glyphicon-user form-control-feedback" aria-hidden="true"></span>',
                'format' => 'raw',
                'label' => 'User',
                'value' => function ($model) {
                    /* @var $model Comment */
                    $username = $model->getUsername();

                    if ($model->isAnonymous) {
                        return $username;
                    }

                    return Html::a($username, $model->getUserProfileUrl());
                },
            ],
            [
                'attribute' => 'user_email',
                'format' => 'email',
                'prepend' => '<span class="glyphicon glyphicon-envelope form-control-feedback" aria-hidden="true"></span>',
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'prepend' => '<span class="glyphicon glyphicon-calendar form-control-feedback" aria-hidden="true"></span>',
                'filter' => '<div class="input-group drp-container">' .
                    \kartik\daterange\DateRangePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'convertFormat' => true,
                        'useWithAddon' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                                'separator' => ' - ',
                            ],
                            'autoApply' => true,
                            'opens' => 'left',
                        ],
                    ]) .
                    '</div>',
            ],
            [
                'class' => 'demi\comments\backend\components\ActionColumn',
                'filterAttribute' => 'is_approved',
            ],
        ],
    ]); ?>
    <?php Pjax::end() ?>

</div>
