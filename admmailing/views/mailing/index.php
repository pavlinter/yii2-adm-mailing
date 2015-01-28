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
$this->title = Adm::t('mailing', 'Mailings');
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->i18n->resetDot();
?>
<div class="mailing-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Adm::t('mailing', 'Create Mailing'), ['create'], ['class' => 'btn btn-primary']) ?>
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
                'filter'=> Mailing::typeList(),
                'value' => function ($model) {
                    return Mailing::typeList($model->type);
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
                'template' => '{send} {update} {delete}',
                'buttons' => [
                    'send' => function ($url, $model, $key) {
                        return Html::a('<span class="fa fa-envelope"></span>', $url, [
                            'title' => Adm::t('mailing', 'Send', ['dot' => false]),
                            'data-pjax' => '0',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
