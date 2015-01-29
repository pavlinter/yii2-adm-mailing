<?php

use pavlinter\admmailing\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admmailing\models\Mailing */

Yii::$app->i18n->disableDot();
$this->title = Yii::t('adm-mailing', 'Update Mailing: ') . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm-mailing', 'Mailings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('adm-mailing', 'Update');
Yii::$app->i18n->resetDot();
?>
<div class="mailing-update">
    <?= Module::trasnalateLink() ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
