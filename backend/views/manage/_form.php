<?php

use common\helpers\CommentHelper;
use common\helpers\LangHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'material_type', ['options' => ['class' => 'col-md-2']])
            ->dropDownList(CommentHelper::getMaterialTypesList()) ?>

        <?= $form->field($model, 'material_id', ['options' => ['class' => 'col-md-2']])
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_id', ['options' => ['class' => 'col-md-2']])
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_name', ['options' => ['class' => 'col-md-3']])
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_email', ['options' => ['class' => 'col-md-3']])
            ->input('email', ['maxlength' => true]) ?>
    </div>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <div class="row">
        <?= $form->field($model, 'parent_id', ['options' => ['class' => 'col-md-2']])
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'language_id', ['options' => ['class' => 'col-md-2']])
            ->dropDownList(LangHelper::getLanguages()) ?>

        <?= $form->field($model, 'is_replied', ['options' => ['class' => 'col-md-2']])
            ->checkbox() ?>

        <?= $form->field($model, 'is_approved', ['options' => ['class' => 'col-md-2']])
            ->checkbox() ?>

        <?= $form->field($model, 'is_deleted', ['options' => ['class' => 'col-md-2']])
            ->checkbox() ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
