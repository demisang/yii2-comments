<?php

namespace demi\comments\frontend\widgets;

use Yii;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Class Comments
 *
 * GETTERS
 * @property \demi\comments\common\components\Comment|null $component
 * @property \demi\comments\common\models\Comment[] $comments
 */
class Comments extends Widget
{
    /** @var \demi\comments\common\models\Comment[] Widget comments */
    private $_comments;
    /** @var int Type of material. Can be null if $comment provided */
    public $materialType;
    /** @var int Id of material. Can be null if $comment provided */
    public $materialId;
    /** @var string URL path to default user photo image */
    public $defaultPhoto;
    /** @var array HTML-options for main comments ul-tag */
    public $options = [];
    /** @var array HTML-options for nested comments ul-tag */
    public $nestedOptions = [];
    /** @var array jQ-plugin options */
    public $clientOptions = [];
    /**
     * Maximum nested level. If level reached - nested comments will be outputted without ul-tag.
     *
     * For example:
     * If maxNestedLevel = 6, then comments on level 7+ will be outputted on 6th level,
     * users also can reply on 7+ level comments, but reply will be placed on bottom replied-comment on level 6
     *
     * @var int
     */
    public $maxNestedLevel = 6;
    /** @var string|array Url for permalink (without '#') */
    public $materialViewUrl = '';
    /** @var array [[ActiveForm]] configuration */
    public $formConfig = [
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
    ];

    public function init()
    {
        parent::init();

//        $this->formConfig['beforeSubmit'] = new JsExpression('function() { alert(1); }');

        // Set form submit url
        if (!array_key_exists('action', $this->formConfig)) {
            $this->formConfig['action'] = ['/comment/default/create'];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $comments = $this->render($this->component->listView, [
            'widget' => $this,
            'comments' => $this->comments,
            'parentId' => null, // Render first comments level
            'nestedLevel' => 1,
        ]);

        $model = $this->component->model;
        $model->material_type = $this->materialType;
        $model->material_id = $this->materialId;

        $form = $this->render($this->component->formView, [
            'widget' => $this,
            'comments' => $this->comments,
            'model' => $model,
        ]);

        $this->registerClientScript();

        return '<div id="' . $this->id . '">' . PHP_EOL . $comments . PHP_EOL . $form . PHP_EOL . '</div>';
    }

    /**
     * Get all comments
     *
     * @return \demi\comments\common\models\Comment[]
     */
    public function getComments()
    {
        if (!is_array($this->_comments)) {
            $model = $this->component->getModel();
            $alias = $model->tableName();
            $this->_comments = $model->find()
                ->where([
                    'material_type' => $this->materialType,
                    'material_id' => $this->materialId,
                    'is_deleted' => 0,
                ])
                ->orderBy(["$alias.parent_id" => SORT_ASC, "$alias.created_at" => SORT_ASC])
                ->with(['user'])
                ->all();
        }

        return $this->_comments;
    }

    /**
     * Set custom comments
     *
     * @param \demi\comments\common\models\Comment[] $value
     */
    public function setComments($value)
    {
        $this->_comments = $value;
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

    /**
     * Register plugin assets and jQ-plugin execute
     */
    public function registerClientScript()
    {
        $view = $this->getView();

        // Register assets
        CommentsAsset::register($view);

        $id = $this->id;

        $options = [
            'maxNestedLevel' => $this->maxNestedLevel,
            'nestedListOptions' => $this->nestedOptions,
        ];

        $options = Json::encode(ArrayHelper::merge($options, $this->clientOptions));

        // Register plugin
        $view->registerJs("jQuery('#$id').commentsWidget($options);");
    }
} 