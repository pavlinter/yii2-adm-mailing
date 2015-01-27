<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @package yii2-adm-mailing
 */

namespace pavlinter\admmailing;

use pavlinter\adm\Manager;
use Yii;

/**
 * @method \pavlinter\admmailing\models\Mailing createMailing
 * @method \pavlinter\admmailing\models\Mailing createMailingQuery
 * @method \pavlinter\admmailing\models\MailingSearch createMailingSearch
 * @method \pavlinter\admmailing\models\MailingLang createMailingLang
 * @method \pavlinter\admmailing\models\MailingLang createMailingLangQuery
 */
class ModelManager extends Manager
{
    /**
     * @var string|\pavlinter\admmailing\models\Mailing
     */
    public $mailingClass = 'pavlinter\admmailing\models\Mailing';
    /**
     * @var string|\pavlinter\admmailing\models\MailingSearch
     */
    public $mailingSearchClass = 'pavlinter\admmailing\models\MailingSearch';
    /**
     * @var string|\pavlinter\admmailing\models\MailingLang
     */
    public $mailingLangClass = 'pavlinter\admmailing\models\MailingLang';
}