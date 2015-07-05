<?php

use common\helpers\CommentHelper;
use common\helpers\LangHelper;
use common\models\Comment;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\comment\models\CommentSearch */
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

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'material_type',
                'filter' => CommentHelper::getMaterialTypesList(),
                'value' => function ($model) {
                        /* @var $model Comment */
                        return $model->material_type ? CommentHelper::getMaterialTypesList($model->material_type) : null;
                    },
            ],
            'material_id',
            'text:ntext',
            'user_id',
            'user_name',
            'user_email:email',
            [
                'attribute' => 'language_id',
                'filter' => LangHelper::getLanguages(),
                'value' => function ($model) {
                        /* @var $model Comment */
                        return $model->language_id ? LangHelper::getLanguages($model->language_id) : null;
                    },
            ],
            'created_at',
            ['class' => '\demi\helpers\grid\BigActionColumn'],
        ],
    ]); ?>

</div>
