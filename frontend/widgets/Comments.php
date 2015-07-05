<?php

namespace demi\comments\frontend\widgets;

use Yii;
use yii\base\Exception;
use yii\base\Widget;

/**
 * Class Comments
 *
 * GETTERS
 * @property \demi\comments\common\components\Comment|null $component
 */
class Comments extends Widget
{
    public $comments;
    public $materialType;
    public $materialId;
    public $defaultPhoto;

    public function run()
    {
        return $this->render('comments', [
            'widget' => $this,
            'comments' => $this->_getComments(),
            'parentId' => null, // Render first comments level
        ]);
    }

    /**
     * Get all comments
     *
     * @return \demi\comments\common\models\Comment[]
     */
    protected function _getComments()
    {
        if (!is_array($this->comments)) {
            $model = $this->component->getModel();
            $alias = $model->tableName();
            $this->comments = $model->find()
                ->where(['material_type' => $this->materialType, 'material_id' => $this->materialId])
                ->orderBy(["$alias.parent_id" => SORT_ASC, "$alias.created_at" => SORT_ASC])
                ->with(['user'])
                ->all();
        }

        return $this->comments;
    }

    /**
     * Get comment component
     *
     * @param string $name Name of comment component
     *
     * @throws \yii\base\Exception
     * @return \demi\comments\common\components\Comment|null
     */
    public static function getComponent($name = 'comment')
    {
        if (Yii::$app->has($name)) {
            return Yii::$app->get($name);
        }

        throw new Exception('Component "' . $name . '" was not found');
    }
} 