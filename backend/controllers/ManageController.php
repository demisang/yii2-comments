<?php
/**
 * @copyright Copyright (c) 2018 Ivan Orlov
 * @license   https://github.com/demisang/yii2-comments/blob/master/LICENSE
 * @link      https://github.com/demisang/yii2-comments#readme
 * @author    Ivan Orlov <gnasimed@gmail.com>
 */

namespace demi\comments\backend\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use demi\comments\common\models\Comment;
use demi\comments\backend\models\CommentSearch;

/**
 * ManageController implements the CRUD actions for Comment model.
 */
class ManageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'toggle-approve' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Comment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comment model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Comment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Comment();
        $model->setScenario('admin');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Comment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('admin');

        if (!$model->canUpdate()) {
            throw new ForbiddenHttpException('You don\'t have permissions to update this comment');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Comment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$model->canDelete()) {
            throw new ForbiddenHttpException('You don\'t have permissions to delete this comment');
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Toggle comment moderation status
     *
     * @param int $id
     *
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionToggleApprove($id)
    {
        $model = $this->findModel($id);

        if (!$model->canUpdate()) {
            throw new ForbiddenHttpException('You don\'t have permissions for comment approving');
        }

        $model->is_approved = $model->is_approved ? 0 : 1;

        $result = $model->save(false, ['is_approved']);

        $response = Yii::$app->response;
        $response->getHeaders()->set('Vary', 'Accept');
        $response->format = Response::FORMAT_JSON;

        return [
            'status' => $result ? 'success' : 'error',
        ];
    }

    /**
     * Redirect to the page with this comment
     *
     * @param int $id
     *
     * @return Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionGoToComment($id)
    {
        $model = $this->findModel($id);

        if ($model->component->getPermalink === null) {
            throw new InvalidConfigException('For this action you must set comments config: ' .
                'demi\comments\common\components\Comment::$getPermalink (callable. must return a valid redirect url)');
        }

        return $this->redirect($model->permalink);
    }

    /**
     * Finds the Comment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
