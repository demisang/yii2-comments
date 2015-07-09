<?php

use demi\comments\frontend\widgets\Comments;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $widget Comments */
/* @var $model demi\comments\common\models\Comment */

?>

<hr />
<h3 class="text-primary"><span class="restore-comment-form" style="border-bottom: 1px dashed #585a53; cursor: pointer;">Leave a comment</span></h3>

<div class="primary-form-container">
<div class="comment-form">
    <?php $form = ActiveForm::begin($widget->formConfig); ?>

    <?= Html::activeHiddenInput($model, 'parent_id', ['class' => 'parent_comment_id']) ?>
    <?= Html::activeHiddenInput($model, 'material_type') ?>
    <?= Html::activeHiddenInput($model, 'material_id') ?>

    <?php if (Yii::$app->user->isGuest): ?>
    <div class="row">
        <?= $form->field($model, 'user_name', ['options' => ['class' => 'col-md-6'], 'enableAjaxValidation' => true])
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_email', ['options' => ['class' => 'col-md-6'], 'enableAjaxValidation' => true])
            ->input('email', ['maxlength' => true]) ?>
    </div>
    <?php endif ?>

    <div class="row">
        <?= $form->field($model, 'text', ['options' => ['class' => 'col-md-12'], 'enableAjaxValidation' => true])
            ->textarea(['rows' => 4, 'maxlength' => true]) ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= Html::submitButton('Post Comment', ['class' => 'btn btn-primary btn-lg']) ?>
            <?php if (Yii::$app->user->isGuest): ?>
            <?php endif ?>
        </div>
        <?= $form->field($model, 'captcha', ['options' => ['class' => 'col-md-6'], 'enableAjaxValidation' => true])
                ->widget(Captcha::className(), [
                    'captchaAction' => ['/comment/default/captcha'],
                ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
</div>