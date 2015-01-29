<?php

use pavlinter\admmailing\Module;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admmailing\models\Mailing */
/* @var $type \pavlinter\admmailing\objects\Type */

Yii::$app->i18n->disableDot();
$this->title = Yii::t('adm-mailing', 'Send emails: ') . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm-mailing', 'Mailings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('adm-mailing', 'Send');
Yii::$app->i18n->resetDot();
?>
<div class="mailing-send">
    <?= Module::trasnalateLink() ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="">
        <div class="mailing-trans clearfix">
            <span class="mailing-transport label label-primary"></span>
        </div>
        <div class="mailing-bb-cont">
            <div class="progress">
                <div class="progress-bar progress-bar-success progress-bar-striped" style="width: 0%">
                    <span class="mailing-procent">0</span>%
                </div>
            </div>
        </div>
        <div class="mailing-res-process"></div>
        <button class="btn btn-primary mailing-btn-send"><?= Yii::t('adm-mailing', 'Send', ['dot' => false]) ?></button>
        <button class="btn btn-primary mailing-btn-continue" style="display: none;"><?= Yii::t('adm-mailing', 'Continue', ['dot' => false]) ?></button>
    </div>


</div>


<?php
$this->registerJs('
    var type = ' . \yii\helpers\Json::encode($type) .';
    var lastNum = 0;
    var count = "";
    var changeTransport = 0;
    var badEmail = 0;
    var sendEmail = function(num, contin){
        num = parseInt(num);
        var contin = contin || false;
        var $resCont = $(".mailing-res-process");
        var $bbCont = $(".mailing-bb-cont");
        if(!num){
            $resCont.text("");
            $(".mailing-btn-continue").hide();
        }
        $bbCont.show();
        $resCont.show()
        var $oneProcess = $("<div class=\"text-primary mailing-one-process\">'.Yii::t('adm-mailing', 'Loading.....', ['dot' => false]).'<div>");
        $resCont.append($oneProcess);

        var data = {
            last : num,
            changeTransport : changeTransport,
            sumBadEmail : badEmail,
        };

        if(contin){
            data.continue = 1;
        }

        var xhr = $.ajax({
            url: "'. Url::to('').'",
            type: "POST",
            dataType: "json",
            data: data
        }).done(function(d){
            $(".mailing-btn-send").hide();
            changeTransport = d.changeTransport;
            badEmail += d.badEmail;
            lastNum = d.last;

            if(d.username == null){
                $(".mailing-trans").hide()
            } else {
                $(".mailing-trans").show()
            }
            $(".mailing-transport").html(d.username);
            $(".progress-bar").css("width",d.procent + "%").find(".mailing-procent").text(d.procent);

            if(d.countEmails){
                count = d.countEmails;
            }
            if(d.r == "process"){
                $oneProcess.html(d.text);
                prevHide($oneProcess);
                $("title").text(d.text);
                sendEmail(d.last);
            } else if(d.r == "end") {
                $(".mailing-btn-send").show();
                if(d.count){
                    $oneProcess.remove();
                } else {
                    $oneProcess.remove();
                }


                $("title").text(d.text_success);
                $resCont.append("<div class=\"text-success mailing-success-process\">" + d.text_success + "<div>");

            } else if(d.r == "error") {
                $("title").text(d.error_text);
                $oneProcess.html(d.text);
                prevHide($oneProcess);
                $oneProcess.after("<div class=\"text-danger mailing-error-process\">"+d.error_text+"<div>");
                $(".mailing-btn-continue").show();
            }


        }).always(function(jqXHR, textStatus){
            resScroll();
        }).fail(function(jqXHR, textStatus, message){
                var textError = "'.Yii::t('adm-mailing', 'Server error: {start}/{end}', ['dot' => false]).'"
                textError = textError.replace("{start}",lastNum).replace("{end}",count);
                $oneProcess.after("<div class=\"text-danger mailing-error-process\">"+textError+"<div>");
                $("title").text(textError);
                $oneProcess.remove();
                $(".mailing-btn-continue").show();
            if(xhr){
                xhr.abort();
            }
        });
    }

    $(".mailing-btn-continue").on("click", function(){
        $(this).hide();
        sendEmail(lastNum, true);
        return false;
    });

    $(".mailing-btn-send").on("click", function(){
        sendEmail(0);
        return false;
    });

    var resScroll = function(){
        var $resCont = $(".mailing-res-process");
        $resCont.scrollTop($resCont.prop("scrollHeight"));
    }

    var prevHide = function($el){
        if(!type.showAllstatistic){
            $el.prev(".mailing-one-process").hide();
        }
    }

');
