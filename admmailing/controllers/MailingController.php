<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @package yii2-adm-mailing
 */

namespace pavlinter\admmailing\controllers;

use pavlinter\admmailing\models\Mailing;
use pavlinter\admmailing\Module;
use Swift_SwiftException;
use Yii;
use pavlinter\adm\Adm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

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
     * Creates a new Mailing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer|null $id
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = Module::getInstance()->manager->createMailing();

        $model->loadDefaultValues();
        if ($model->loadAll(Yii::$app->request->post()) && $model->validateAll()) {
            if ($model->save(false) && $model->saveTranslations(false)) {
                Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully inserted!'));
                return Adm::redirect(['update', 'id' => $model->id]);
            }
        }

        if($id){
            $model = $this->findModel($id);
            $model->setIsNewRecord(true);
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

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSend($id)
    {
        /* @var $module \pavlinter\admmailing\Module */
        /* @var $model \pavlinter\admmailing\models\Mailing */
        $module = Module::getInstance();
        $model  = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            set_time_limit(0);
            Yii::$app->response->format = Response::FORMAT_JSON;

            if (!isset($module->typeList[$model->type])) {
                $json['r'] = 'error';
                $json['error_text'] = Yii::t('adm-mailing', 'The type "'  . $model->type . '" is not exist!', ['dot' => false]);
                return $json;
            }
            /* @var $type \pavlinter\admmailing\objects\Type */
            $type = $module->typeList[$model->type];

            $from = $module->from;
            if ($model->email) {
                if ($model->name) {
                    $from = [$model->email => $model->name];
                } else {
                    $from = $model->email;
                }
            }


            $countIteration = $type->countIteration;

            $last =  (int)Yii::$app->request->post('last', 0);
            $continue =  (int)Yii::$app->request->post('continue', 0);
            $changeTransport =  (int)Yii::$app->request->post('changeTransport', 0);
            $sumBadEmail = (int)Yii::$app->request->post('sumBadEmail', 0);

            $username = null;
            if (isset($module->transport[$changeTransport])) {
                $transport = $module->transport[$changeTransport];
                Yii::$app->mailer->setTransport($transport);
                if (count($module->transport) > 1) {
                    $username = $transport->username;
                }
            } else {
                $changeTransport = 0;
            }

            if ($username) {
                $json['username'] = Yii::t('adm-mailing', 'Transport: {username}', ['dot' => false, 'username' => $username]);
            } else {
                $json['username'] = null;
            }



            /* @var $query \yii\db\Query */
            $query = call_user_func($type->getQuery());

            $countRows = clone $query;
            $queryRows = clone $query;
            $badEmail = 0;
            $countEmails = $countRows->count();
            $queryRows->asArray()->limit($countIteration)->offset($last);

            $i = 0;

            $json['r'] = null;
            foreach ($queryRows->batch(50) as $rows) {
                $braek = false;
                foreach ($rows as $row) {
                    if ($i + $last >= 19) {
                        //break; //force stop;
                    }

                    $i++;
                    /* @var \yii\swiftmailer\Message $mailer */
                    try {
                        /*if ($i + $last == 20 && !$continue) {
                            throw new Swift_SwiftException('Gmail! Continue please!');
                        }
                        if ($i + $last == 80 && !$continue) {
                            throw new Swift_SwiftException('Gmail! Continue please!');
                        }
                        if ($i + $last == 125 && !$continue) {
                            throw new Swift_SwiftException('Gmail! Continue please!');
                        }
                        if ($i + $last == 160 && !$continue) {
                            throw new Swift_SwiftException('Gmail! Continue please!');
                        }
                        if ($i + $last == 200 && !$continue) {
                            throw new Swift_SwiftException('Gmail! Continue please!');
                        }*/

                        if ($i + $last > 10 && $i + $last < 12) {
                            $row[$type->emailKey] = 'pavlinter@gmail.com';
                        }
                        $mailer = Yii::$app->mailer->compose();

                        if (call_user_func($type->getEmailFilter(), $row[$type->emailKey], $row)) {
                            $mailer->setTo($row[$type->emailKey]);
                            if ($model->reply_email) {
                                $mailer->setReplyTo($model->reply_email, $model->reply_name);
                            }
                            $replace = $type->getVarTemplate($row);
                            $subject = strtr($model->subject, $replace);
                            $text = strtr(nl2br($model->text), $replace);

                            $mailer->setFrom($from)
                                ->setSubject($subject)
                                ->setHtmlBody($text);
                            if (!$type->testMode) {
                                $mailer->send();
                            }
                            sleep($type->sendSleep);
                        } else {
                            $badEmail++;
                        }

                    } catch(\Exception $e) {
                        $changeTransport++;
                        $i--;
                        $json['r'] = 'error';
                        $json['error_text'] = Yii::t('adm-mailing', 'Error: {errorText}', ['dot' => false, 'errorText' => $e->getMessage()]);
                        $braek = true;
                        break;
                    }

                }
                if ($braek) {
                    break;
                }
            }

            $json['last'] = $i + $last;
            $json['changeTransport'] = $changeTransport;
            $json['badEmail'] = $badEmail;
            if ($json['r']) {

            }else if($i == $countIteration){
                $json['r']    = 'process';
            } else {
                $json['r']    = 'end';
                $json['text_success'] = Yii::t('adm-mailing', 'Success: {sum} / {end} <br/> Bad email: {countBadEmail}', ['dot' => false, 'sum' => $json['last'], 'end' => $countEmails, 'countBadEmail' => $sumBadEmail + $badEmail]);
            }

            $json['countEmails'] = $countEmails;
            $json['count'] = $i;
            $json['procent'] = (int)($json['last'] * 100 / $countEmails);
            $json['text'] = Yii::t('adm-mailing', 'Sended: {count} emails ({start}/{end})', ['count' => $i,'start' => $json['last'], 'end' => $countEmails, 'dot' => false]);
            return $json;
        }

        if (!isset($module->typeList[$model->type])) {
            throw new NotFoundHttpException(Yii::t('adm-mailing', 'The type "'  . $model->type . '" is not exist!', ['dot' => false]));
        }
        /* @var $type \pavlinter\admmailing\objects\Type */
        $type = $module->typeList[$model->type];

        return $this->render('send', [
            'model' => $model,
            'type' => $type,
        ]);
    }
}
