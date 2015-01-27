<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @package yii2-adm-mailing
 */

namespace pavlinter\admmailing\controllers;

use pavlinter\admmailing\models\Mailing;
use pavlinter\admmailing\Module;
use Yii;
use pavlinter\adm\Adm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MailingController implements the CRUD actions for Mailing model.
 */
class MailingController extends Controller
{
    /**
    * @inheritdoc
    */
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
     * Lists all Mailing models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = Module::getInstance()->manager->createMailingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Mailing model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Mailing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = Module::getInstance()->manager->createMailing();

        $model->loadDefaultValues();
        if ($model->loadAll(Yii::$app->request->post()) && $model->validateAll()) {
            if ($model->save(false) && $model->saveTranslations(false)) {
                Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully inserted!'));
                return Adm::redirect(['update', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Mailing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->loadAll(Yii::$app->request->post()) && $model->validateAll()) {
            if ($model->save(false) && $model->saveTranslations(false)) {
                Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully changed!'));
                return Adm::redirect(['update', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Mailing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully removed!'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the Mailing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mailing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Module::getInstance()->manager->createMailingQuery('find')->with(['translations'])->where(['id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
