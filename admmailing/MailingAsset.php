<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @package yii2-adm-mailing
 */

namespace pavlinter\admmailing;

/**
 * Class MailingAsset
 */
class MailingAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/pavlinter/yii2-adm-mailing/admmailing/assets';

    public $css = [
        'css/mailing.css'
    ];
}