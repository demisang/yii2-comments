<?php

namespace demi\comments\backend\components;

use Yii;
use demi\comments\common\models\Comment;
use yii\helpers\Html;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $headerOptions = ['class' => 'comment-action-col'];
    public $filterAttribute;

    public function init()
    {
        $this->template = '
<div class="btn-group" role="group">
    {approve}
    {delete}
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Actions</span>
    </button>
    <ul class="dropdown-menu">
        <li>{view}</li>
        <li>{update}</li>
    </ul>
</div>';

        if (!isset($this->buttons['isApprovedClass'])) {
            $this->buttons['isApprovedClass'] = function ($url, $model, $key) {
                /* @var $model Comment */
                return $model->is_approved ? 'btn-success' : 'btn-warning';
            };
        }

        if (!isset($this->buttons['approve'])) {
            $this->buttons['approve'] = function ($url, $model, $key) {
                /* @var $model Comment */

                // if comment is already approved
                if ($model->is_approved) {
                    return Html::a('<span class="glyphicon glyphicon-check"></span> ',
                        ['toggle-approve', 'id' => $model->id],
                        [
                            'title' => Yii::t('yii', 'Toggle approve status'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-success toggle-approve',
                        ]);
                }

                // if comment needs in admin approve
                return Html::a('<span class="glyphicon glyphicon-unchecked"></span> ',
                    ['toggle-approve', 'id' => $model->id],
                    [
                        'title' => Yii::t('yii', 'Toggle approve status'),
                        'data-pjax' => '0',
                        'class' => 'btn btn-warning toggle-approve',
                    ]);
            };
        }

        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' . Yii::t('yii', 'View'),
                    $url, ['data-pjax' => '0']);
            };
        }

        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('yii', 'Update'),
                    $url, ['data-pjax' => '0']);
            };
        }

        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span> ', $url,
                    [
                        'title' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'class' => 'btn btn-danger',
                    ]);
            };
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function renderFilterCellContent()
    {
        if (empty($this->filterAttribute)) {
            return parent::renderFilterCellContent();
        }

        $model = $this->grid->filterModel;
        $attr = $this->filterAttribute;
        $value = $model->$attr;


        $allActive = "$value" === '' ? ' active' : '';
        //\demi\helpers\VD::dump($value);die;
        $allChecked = "$value" === '' ? ' checked' : '';
        $unapprovedActive = "$value" === "0" ? ' active' : '';
        $approvedActive = "$value" === "1" ? ' active' : '';

        $inputName = Html::getInputName($model, $attr);
        $unapprovedInput = Html::radio($inputName, !empty($unapprovedActive), ['value' => 0, 'autocomplete' => 'off']);
        $approvedInput = Html::radio($inputName, !empty($approvedActive), ['value' => 1, 'autocomplete' => 'off']);

        return <<<HTML
<div class="btn-group" data-toggle="buttons">
    <label class="btn btn-default$allActive">
        <input type="radio" name="$inputName" value="" autocomplete="off"$allChecked>All
    </label>
    <label class="btn btn-default$unapprovedActive" title="Unapproved">
        $unapprovedInput
        <span class="glyphicon glyphicon-unchecked"></span>
    </label>
    <label class="btn btn-default$approvedActive" title="Approved">
        $approvedInput
        <span class="glyphicon glyphicon-check"></span>
    </label>
</div>
HTML;
    }
}