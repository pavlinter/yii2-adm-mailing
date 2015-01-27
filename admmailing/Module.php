<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @package yii2-adm-mailing
 */

namespace pavlinter\admmailing;

use Closure;
use pavlinter\adm\Adm;
use pavlinter\adm\AdmBootstrapInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * @property \pavlinter\admmailing\ModelManager $manager
 */
class Module extends \yii\base\Module implements AdmBootstrapInterface
{
    public $controllerNamespace = 'pavlinter\admmailing\controllers';

    public $layout = '@vendor/pavlinter/yii2-adm/adm/views/layouts/main';
    /**
     * @var array
     * [
     *   'user' => function(){ return \pavlinter\adm\models\User::find(); },
     * ]
     */
    public $typeList;
    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $config = ArrayHelper::merge([
            'components' => [
                'manager' => [
                    'class' => 'pavlinter\admmailing\ModelManager'
                ],
            ],
        ], $config);

        parent::__construct($id, $parent, $config);
    }

    public function init()
    {
        parent::init();
        if ($this->typeList instanceof Closure) {
            $this->typeList = call_user_func($this->typeList, $this);
        }

        if (!is_array($this->typeList)) {
            throw new InvalidConfigException('The "typeList" property must be array.');
        }

        if (empty($this->typeList)) {
            throw new InvalidConfigException('The "typeList" property must be at least one element.');
        }

    }

    /**
     * @param \pavlinter\adm\Adm $adm
     */
    public function loading($adm)
    {
        if ($adm->user->can('Adm-Mailing')) {
            $adm->params['left-menu']['admmailing'] = [
                'label' => '<i class="fa fa-envelope"></i><span>' . $adm::t('menu', 'Mailing') . '</span>',
                'url' => ['/admmailing/mailing/index']
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $adm = Adm::register();
        if (!parent::beforeAction($action) || !$adm->user->can('Adm-Mailing')) {
            return false;
        }
        return true;
    }
}
