<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model demi\comments\common\models\Comment */

$this->title = Yii::t('app', 'Comment') . ' #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Comments material types
$types = Yii::$app->comment->types;
// Convert short user IP to long(human) format
$user_ip = Yii::$app->db->createCommand('SELECT INET_NTOA(:ip)', [':ip' => $model->user_ip])->queryScalar()
?>
<div class="comment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default">

        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?= Html::a('Open comment', ['go-to-comment', 'id' => $model->id],
                        ['class' => 'btn btn-default', 'target' => '_blank']) ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                    <?= Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id],
                        ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <?= $model->getPreparedText() ?>
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-bordered detail-view table-hover'],
            'attributes' => [
                'id',
                [
                    'attribute' => 'material_type',
                    'value' => isset($types[$model->material_type]) ? $types[$model->material_type] : null,
                ],
                'material_id',
                [
                    'attribute' => 'user_id',
                    'format' => 'raw',
                    'label' => 'User',
                    'value' => !$model->isAnonymous ? Html::a($model->getUsername(),
                        $model->getUserProfileUrl()) : null,
                ],
                [
                    'attribute' => 'user_name',
                    'label' => 'User name',
                ],
                'user_email:email',
                [
                    'attribute' => 'user_ip',
                    'format' => 'raw',
                    'value' => Html::a($user_ip, "http://www.infobyip.com/ip-$user_ip.html",
                        ['target' => '_blank', 'title' => 'Show info about this IP address']),
                ],
                [
                    'attribute' => 'parent_id',
                    'format' => 'raw',
                    'value' => $model->parent_id ? Html::a('Comment #' . $model->parent_id,
                        ['view', 'id' => $model->parent_id]) : null,
                ],
                'is_replied:boolean',
                'is_approved:boolean',
                'is_deleted:boolean',
                [
                    'attribute' => 'created_at',
                    'value' => $model->fDate,
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => Yii::$app->formatter->asDatetime($model->updated_at),
                ],
            ],
        ]) ?>
    </div>
</div>
