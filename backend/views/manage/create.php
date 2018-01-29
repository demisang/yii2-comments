<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

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
