<?php

use yii\helpers\Html;
use pavlinter\adm\Adm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admmailing\models\Mailing */

Yii::$app->i18n->disableDot();
$this->title = Adm::t('mailing', 'Send emails: ') . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Adm::t('mailing', 'Mailings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Adm::t('mailing', 'Send');
Yii::$app->i18n->resetDot();
?>
<div class="mailing-send">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="">
        <div class="mailing-emailTo"></div>
        <div class="mailing-bb-cont">
            <div class="progress">
                <div class="progress-bar progress-bar-success progress-bar-striped" style="width: 0%">
                    <span class="mailing-procent">0</span>%
                </div>
            </div>
        </div>
        <div class="mailing-res-process"></div>
        <button class="btn btn-primary mailing-btn-send"><?= Adm::t('mailing', 'Send', ['dot' => false]) ?></button>
        <button class="btn btn-primary mailing-btn-continue mailing-mbtn" style="display: none;"><?= Adm::t('mailing', 'Continue', ['dot' => false]) ?></button>
    </div>


</div>


<?php
$this->registerJs('
    var lastNum = 0;
    var count = "";
    var changeTransport = 0;
    var sendEmail = function(num, contin){
        num = parseInt(num);
        var contin = contin || false;
        var $resCont = $(".mailing-res-process");
        var $bbCont = $(".mailing-bb-cont");
        var $btn = $(".mailing-mbtn");
        if(!num){
            $resCont.text("");
            $btn.hide();
        }
        $bbCont.show();
        $resCont.show()
        var $oneProcess = $("<div class=\"text-primary mailing-one-process\">'.Adm::t('mailing', 'Loading.....', ['dot' => false]).'<div>");
        $resCont.append($oneProcess);

        var data = {
            last : num,
            changeTransport : changeTransport,
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
            changeTransport = d.changeTransport;
            lastNum = d.last;
            $(".emailTo").html(d.username);
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
                $btn.show();
                if(d.count){
                    //$oneProcess.html(d.text);
                    //prevHide($oneProcess);
                    $oneProcess.remove();
                } else {
                    $oneProcess.remove();
                }


                $("title").text(d.text_success);
                $resCont.append("<div class=\"text-success mailing-success-process\">"+d.text_success+"<div>");

            } else if(d.r == "error") {
                $("title").text(d.error_text);
                $oneProcess.html(d.text);
                prevHide($oneProcess);
                $oneProcess.after("<div class=\"text-danger mailing-error-process\">"+d.error_text+"<div>");
                $(".mailing-btn-continue").show();
            }


        }).always(function(jqXHR, textStatus){
            if (textStatus !== "success") {

            }
        }).fail(function(jqXHR, textStatus, message){
                var textError = "'.Adm::t('mailing', 'Server error: {start}/{end}', ['dot' => false]).'"
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



    var prevHide = function($el){
        $el.prev(".mailing-one-process").hide();
    }

');
