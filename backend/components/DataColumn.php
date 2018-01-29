<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\comments\backend\components;

use yii\helpers\Html;

class DataColumn extends \yii\grid\DataColumn
{
    public $prepend;

    /**
     * @inheritdoc
     */
    protected function renderFilterCellContent()
    {
        if ($this->prepend === null) {
            return parent::renderFilterCellContent();
        }

        $prependId = Html::getInputId($this->grid->filterModel, $this->attribute) . '_input_addon';
        $this->filterInputOptions['aria-describedby'] = $prependId;

        $filter = parent::renderFilterCellContent();

        $content = Html::beginTag('div', ['class' => 'has-feedback text-info']) . PHP_EOL .
            $filter .
            $this->prepend . PHP_EOL .
            Html::tag('span', $this->grid->filterModel->getAttributeLabel($this->attribute), [
                'id' => $prependId,
                'class' => 'sr-only'
            ]) . PHP_EOL .
            Html::endTag('div');

        /* $content = Html::beginTag('div', ['class' => 'input-group']) . PHP_EOL .
            Html::tag('span', $this->prepend, ['class' => 'input-group-addon', 'id' => $prependId]) . PHP_EOL .
            $filter .
            Html::endTag('div'); */

        return $content;
    }
}
