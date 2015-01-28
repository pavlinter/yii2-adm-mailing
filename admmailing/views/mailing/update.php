<?php

use yii\helpers\Html;
use pavlinter\adm\Adm;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admmailing\models\Mailing */

Yii::$app->i18n->disableDot();
$this->title = Adm::t('mailing', 'Update Mailing: ') . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Adm::t('mailing', 'Mailings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Adm::t('mailing', 'Update');
Yii::$app->i18n->resetDot();
?>
<div class="mailing-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
