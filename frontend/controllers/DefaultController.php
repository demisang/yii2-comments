<?php

namespace demi\comments\frontend\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use demi\comments\common\models\Comment;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class DefaultController
 *
 * @property \demi\comments\common\components\Comment $component
 */
class DefaultController extends Controller
{
    public $itemViewFile = '@vendor/demi/comments/frontend/widgets/views/_comment';
    public $commentComponentName = 'comment';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Create new [[Comment]]
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->component->model;

        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'result' => false,
        ];

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // Refresh model data
                $model->refresh();

                $response['result'] = true;
                $response['comment'] = $this->renderAjax($this->component->itemView, ['comment' => $model]);
            } else {
                $response['result'] = false;
                $response['errors'] = $model->firstErrors;
            }
        }

        return $response;
    }

    /**
     * Update existing [[Comment]]
     *
     * @param int $id
     *
     * @return mixed
     *
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUpdate()) {
            throw new ForbiddenHttpException('You are not allowed to perform this action');
        }

        // Rerutn plain comment text
        if (Yii::$app->request->isGet) {
            if (!Yii::$app->request->isAjax) {
                throw new BadRequestHttpException('You should not open this link directly');
            }

            return $model->text;
        }

        // Try to save and return JSON with "status" and "text"
        $text = Yii::$app->request->post('text');
        $model->setAttributes(['text' => $text]);

        $response = [
            'status' => $model->save(),
        ];

        if ($response['status']) {
            $response['text'] = $model->getPreparedText();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    /**
     * Delete existing [[Comment]]
     *
     * @param int $id
     *
     * @return array
     *
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$model->canDelete()) {
            throw new ForbiddenHttpException('You are not allowed to perform this action');
        }

        $status = (bool)$model->delete();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'status' => $status,
        ];
    }

    /**
     * Finds the [[Comment]] model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = $this->component->model;
        if (($model = $model->findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Get comments component
     *
     * @param string|null $name
     *
     * @return \demi\comments\common\components\Comment
     * @throws \yii\base\InvalidConfigException
     */
    public function getComponent($name = null)
    {
        if ($name === null) {
            $name = $this->commentComponentName;
        }

        return Yii::$app->get($name);
    }
}
