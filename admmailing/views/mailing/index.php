<?php

use kartik\grid\GridView;
use pavlinter\admmailing\models\Mailing;
use pavlinter\admmailing\Module;
use yii\helpers\Html;
use pavlinter\adm\Adm;

/* @var $this yii\web\View */
/* @var $searchModel \pavlinter\admmailing\models\MailingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Yii::$app->i18n->disableDot();
$this->title = Yii::t('adm-mailing', 'Mailings');
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->i18n->resetDot();
?>
<div class="mailing-index">
    <?= Module::trasnalateLink() ?>
    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <p>
        <?= Html::a(Yii::t('adm-mailing', 'Create Mailing'), ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= Adm::widget('GridView',[
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'title',
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'email',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'format' => 'email',
            ],
            [
                'attribute' => 'name',
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'type',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=> $searchModel::typeList(),
                'value' => function ($model) {
                    return $model::typeList($model->type);
                },
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Adm::t('','Select ...', ['dot' => false])],
                'format' => 'raw'
            ],

            [
                'class' => '\kartik\grid\ActionColumn',
                'width' => '130px',
                'template' => '{send} {copy} {update} {delete}',
                'buttons' => [
                    'send' => function ($url, $model, $key) {
                        return Html::a('<span class="fa fa-envelope"></span>', $url, [
                            'title' => Yii::t('adm-mailing', 'Send', ['dot' => false]),
                            'data-pjax' => '0',
                        ]);
                    },
                    'copy' => function ($url, $model) {
                        return Html::a('<span class="fa fa-copy"></span>', ['create', 'id' => $model->id], [
                            'title' => Yii::t('adm-mailing/title', 'Copy', ['dot' => false]),
                            'data-pjax' => '0',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
