<?php

use pavlinter\buttons\InputButton;
use pavlinter\adm\Adm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admmailing\models\Mailing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mailing-form">

    <?php $form = Adm::begin('ActiveForm'); ?>

    <?= $form->errorSummary([$model] + $model->getLangModels(), ['class' => 'alert alert-danger']); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4">
            <?= $form->field($model, 'title')->textInput(['maxlength' => 250]) ?>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4">
            <?= $form->field($model, 'type')->widget(\kartik\widgets\Select2::classname(), [
                'data' => \pavlinter\admmailing\models\Mailing::typeList(),
                'options' => ['placeholder' => Adm::t('','Select ...', ['dot' => false])],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]); ?>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4 col-language" style="display: none;">
            <?= $form->field($model, 'def_language_id')->widget(\kartik\widgets\Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(Yii::$app->getI18n()->getLanguages(), 'id', Yii::$app->getI18n()->langColLabel),
                'options' => ['placeholder' => Adm::t('','Select ...', ['dot' => false])],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => 320]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">

            <?= $form->field($model, 'name')->textInput(['maxlength' => 250]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6">
            <?= $form->field($model, 'reply_email')->textInput(['maxlength' => 320]) ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">

            <?= $form->field($model, 'reply_name')->textInput(['maxlength' => 250]) ?>
        </div>
    </div>

    <section class="panel adm-langs-panel">
        <header class="panel-heading bg-light">
            <ul class="nav nav-tabs nav-justified text-uc">
            <?php  foreach (Yii::$app->getI18n()->getLanguages() as $id_language => $language) { ?>
                <li><a href="#lang-<?=  $id_language ?>" data-toggle="tab"><?=  $language['name'] ?></a></li>
            <?php  }?>
            </ul>
        </header>
        <div class="panel-body">
            <div class="tab-content">
                <?php  foreach (Yii::$app->getI18n()->getLanguages() as $id_language => $language) { ?>
                    <div class="tab-pane" id="lang-<?=  $id_language ?>">
                        <a class="btn btn-primary btn-xs m-b-sm" data-toggle="collapse" href="#mailing-variables-<?= $id_language ?>">
                            <?= Yii::t('adm-mailing', 'Variables', ['dot' => false]) ?>
                        </a>
                        <div class="mailing-variables collapse m-b-sm" id="mailing-variables-<?= $id_language ?>"></div>
                    <?= $form->field($model->getTranslation($id_language), '['.$id_language.']subject')->textInput(['maxlength' => 100]) ?>

                    <?= \pavlinter\adm\Adm::widget('Redactor',[
						'form' => $form,
						'model'      => $model->getTranslation($id_language),
						'attribute'  => '['.$id_language.']text'
					]) ?>
                    </div>
                <?php  }?>
            </div>
        </div>
    </section>


    <div class="form-group">
        <?=  InputButton::widget([
            'label' => $model->isNewRecord ? Adm::t('', 'Create', ['dot' => false]) : Adm::t('', 'Update', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'formSelector' => $form,
        ]);?>

        <?php  if ($model->isNewRecord) {?>
            <?=  InputButton::widget([
                'label' => Adm::t('', 'Create and insert new', ['dot' => false]),
                'options' => ['class' => 'btn btn-primary'],
                'input' => 'adm-redirect',
                'name' => 'redirect',
                'value' => Url::to(['create']),
                'formSelector' => $form,
            ]);?>
        <?php  }?>

        <?=  InputButton::widget([
            'label' => $model->isNewRecord ? Adm::t('', 'Create and list', ['dot' => false]) : Adm::t('', 'Update and list', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'value' => Url::to(['index']),
            'formSelector' => $form,
        ]);?>

        <?=  InputButton::widget([
            'label' => $model->isNewRecord ? Yii::t('adm-mailing', 'Create and Send', ['dot' => false]) : Yii::t('adm-mailing', 'Update and Send', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'value' => Url::to(['send', 'id' => '{id}']),
            'formSelector' => $form,
        ]);?>
    </div>

    <?php Adm::end('ActiveForm'); ?>

</div>
<?php

$this->registerJs('

    var lang = ' . ($model->def_language_id?:'null') . ';

    $("#mailing-type").on("change", function(){
        var v = $(this).val();
        var $cont = $(".mailing-variables");
        var $langSelect = $("#mailing-def_language_id");
        if(v == ""){
            $cont.html("");
            $langSelect.select2("val", $langSelect.find("option:eq(1)").val());
            return true;
        }

        $.ajax({
            url: "' . Url::to(['ajax']) . '",
            type: "GET",
            dataType: "json",
            data: {type: v}
        }).done(function(d){
            if(d.r){
                $cont.html(d.html);
                $langSelect.closest(".form-group").show();

                if(d.disableDefaultLang){
                    $langSelect.select2({allowClear: true});
                    $langSelect.select2("val", "");
                    $langSelect.closest(".col-language").hide();
                } else {
                    $langSelect.select2({allowClear: false});
                    if(lang){
                        $langSelect.select2("val", lang);
                    } else {
                        $langSelect.select2("val", $langSelect.find("option:eq(1)").val());
                    }
                    $langSelect.closest(".col-language").show();
                }
            } else {
                $cont.html("");
            }
        }).always(function(jqXHR, textStatus){
            if (textStatus !== "success") {

            }
        }).fail(function(jqXHR, textStatus, message){
            alert(message);
        });

    }).trigger("change");
');
