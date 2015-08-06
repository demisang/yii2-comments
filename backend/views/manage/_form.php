<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model demi\comments\common\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'material_type', ['options' => ['class' => 'col-xs-6 col-sm-4 col-md-2']])
            ->dropDownList(Yii::$app->comment->types) ?>

        <?= $form->field($model, 'material_id', ['options' => ['class' => 'col-xs-6 col-sm-4 col-md-2']])
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'parent_id', ['options' => ['class' => 'col-xs-6 col-sm-4 col-md-2']])
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_id', [
            'options' => ['class' => 'col-xs-6 col-sm-4 col-md-2'],
            'enableClientValidation' => false
        ])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_name', [
            'options' => ['class' => 'col-xs-6 col-sm-4 col-md-2'],
            'enableClientValidation' => false
        ])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_email', [
            'options' => ['class' => 'col-xs-6 col-sm-4 col-md-2'],
            'enableClientValidation' => false
        ])->input('email', ['maxlength' => true]) ?>
    </div>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <div class="row">
        <?= $form->field($model, 'is_replied', ['options' => ['class' => 'col-xs-12 col-sm-3 col-md-2']])
            ->checkbox(['disabled' => 'disabled']) ?>

        <?= $form->field($model, 'is_approved', ['options' => ['class' => 'col-xs-12 col-sm-3 col-md-2']])
            ->checkbox() ?>

        <?= $form->field($model, 'is_deleted', ['options' => ['class' => 'col-xs-12 col-sm-3 col-md-2']])
            ->checkbox() ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success btn-lg' : 'btn btn-primary btn-lg']) ?>
        or
        <?= Html::a(Yii::t('app', 'Cancel'), $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id],
            ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
