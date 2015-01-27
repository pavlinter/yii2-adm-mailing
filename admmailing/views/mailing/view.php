<?php

use yii\helpers\Html;
use pavlinter\adm\Adm;

/* @var $this yii\web\View */
/* @var $model app\models\Mailing */

Yii::$app->i18n->disableDot();
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Adm::t('mailing', 'Mailings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->i18n->resetDot();
?>
<div class="mailing-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Adm::t('mailing', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Adm::t('mailing', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Adm::t('mailing', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= Adm::widget('DetailView', [
        'model' => $model,
        'hover' => true,
        'mode' => \kartik\detail\DetailView::MODE_VIEW,
        'attributes' => [
            'id',
            'title',
            'email:email',
            'name',
            'type',
            'created_at',
            'updated_at',
            //translations
            'subject',
            'text',
        ],
    ]) ?>

</div>
